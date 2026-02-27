<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\UserInvitation;
use Carbon\Carbon;

class UserInvitationController extends Controller
{
    /**
     * Show the invitation form and list of users.
     */
    public function create(Request $request)
    {
        $query = DB::table('users')
            ->leftJoin('user_invitations', 'users.email', '=', 'user_invitations.email')
            ->select('users.*', 'user_invitations.created_at as invitation_sent_at', 'user_invitations.token');

        // Apply Search Filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.username', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        // Apply Status Filter
        if ($request->has('status') && !empty($request->status)) {
            $status = $request->status;
            if ($status === 'active') {
                $query->whereNotNull('users.email_verified_at');
            } elseif ($status === 'pending') {
                $query->whereNull('users.email_verified_at')
                      ->whereNotNull('user_invitations.token')
                      ->where('user_invitations.created_at', '>=', Carbon::now()->subHours(24));
            } elseif ($status === 'expired') {
                $query->whereNull('users.email_verified_at')
                      ->whereNotNull('user_invitations.token')
                      ->where('user_invitations.created_at', '<', Carbon::now()->subHours(24));
            }
        }

        $users = $query->orderBy('users.created_at', 'desc')->get();

        // Process status for each user
        $processedUsers = $users->map(function ($user) {
            // Determine status first
            if ($user->email_verified_at) {
                $user->status = 'Active';
                $user->badge_class = 'bg-success';
            } elseif ($user->token) {
                // Check expiration (24 hours)
                $sentAt = Carbon::parse($user->invitation_sent_at);
                if ($sentAt->diffInHours(now()) >= 24) {
                    $user->status = 'Expired';
                    $user->badge_class = 'bg-danger';
                } else {
                    $user->status = 'Pending';
                    $user->badge_class = 'bg-warning text-dark';
                }
            } else {
                $user->status = 'Inactive';
                $user->badge_class = 'bg-secondary';
            }
            return $user;
        });

        return view('users.invite', [
            'users' => $processedUsers,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status
            ]
        ]);
    }

    /**
     * Store a newly created user invitation.
     */
    public function store(Request $request)
    {
        // 1. Validate Input (Username uniqueness handled manually for fallback logic)
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
        ]);

        // 2. Handle Username Uniqueness & Fallback (name.surname -> surname.name)
        $username = $validated['username'];
        $exists = DB::table('users')->where('username', $username)->exists();

        if ($exists) {
            // Check if the requested username matches the standard "first.last" pattern
            $firstNameLower = Str::lower($validated['first_name']);
            $lastNameLower = Str::lower($validated['last_name']);
            $standardUsername = $firstNameLower . '.' . $lastNameLower;

            if ($username === $standardUsername) {
                // Try fallback: surname.name
                $fallbackUsername = $lastNameLower . '.' . $firstNameLower;
                
                if (!DB::table('users')->where('username', $fallbackUsername)->exists()) {
                    // Fallback is available, use it
                    $username = $fallbackUsername;
                } else {
                    // Fallback also taken, return error to allow manual edit
                    return response()->json([
                        'message' => 'The username "' . $username . '" and its fallback "' . $fallbackUsername . '" are both taken. Please choose a different username.',
                        'errors' => ['username' => ['The username has already been taken.']]
                    ], 422);
                }
            } else {
                // User provided a custom username (or fallback logic shouldn't apply), and it's taken
                return response()->json([
                    'message' => 'The username has already been taken.',
                    'errors' => ['username' => ['The username has already been taken.']]
                ], 422);
            }
        }

        // 3. Generate Data
        $token = Str::random(64);
        $dummyPassword = Hash::make(Str::random(40)); // Secure random password user doesn't know
        $fullName = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name']);

        DB::beginTransaction();

        try {
            // 4. Insert User (Inactive state via unknown password)
            // Using DB Facade as requested (NO Eloquent)
            DB::table('users')->insert([
                'name' => $fullName,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'username' => $username,
                'email' => $validated['email'],
                'password' => $dummyPassword,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 5. Create Invitation Token
            DB::table('user_invitations')->insert([
                'email' => $validated['email'],
                'token' => $token,
                'created_at' => now(),
            ]);

            DB::commit();

            // 6. Send Invitation Email
            // Construct Activation Link
            // Route: /activate/{token}?email={email} (email for verification)
            $activationUrl = url('/activate/' . $token . '?email=' . urlencode($validated['email']));

            Mail::to($validated['email'])->send(new UserInvitation($fullName, $username, $activationUrl));

            return response()->json(['message' => 'User invited successfully with username: ' . $username], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to invite user: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Resend an invitation to a user.
     */
    public function resend($id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['error' => 'User is already active.'], 400);
        }

        DB::beginTransaction();

        try {
            // Generate new token
            $token = Str::random(64);

            // Delete old invitation if exists
            DB::table('user_invitations')->where('email', $user->email)->delete();

            // Create new invitation
            DB::table('user_invitations')->insert([
                'email' => $user->email,
                'token' => $token,
                'created_at' => now(),
            ]);

            DB::commit();

            // Send Email
            $activationUrl = url('/activate/' . $token . '?email=' . urlencode($user->email));
            Mail::to($user->email)->send(new UserInvitation($user->name, $user->username, $activationUrl));

            return response()->json(['message' => 'Invitation resent successfully.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to resend invitation: ' . $e->getMessage()], 500);
        }
    }
}
