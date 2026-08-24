<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['approved_at', 'blue_book_requested_at', 'subscription_active', 'subscription_expires_at', 'is_suspended'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'email_verified_at',
        'is_suspended',
        'registration_no',
        'specialty',
        'firm_name',
    ];

    /**
     * Pin Spatie Permission's guard resolution to "web" regardless of which
     * auth guard authenticated the current request (Laravel's auth:sanctum
     * middleware switches the default guard to "sanctum" mid-request, which
     * would otherwise make role/permission lookups miss the "web"-guard
     * roles seeded in AdminUserSeeder).
     */
    public function guardName(): string
    {
        return 'web';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'blue_book_requested_at' => 'datetime',
            'subscription_expires_at' => 'datetime',
            'subscription_active' => 'boolean',
            'is_suspended' => 'boolean',
            'password' => 'hashed',
        ];
    }
}
