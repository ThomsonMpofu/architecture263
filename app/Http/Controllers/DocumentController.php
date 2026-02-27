<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = DB::table('documents')
            ->where('user_id', Auth::id())
            ->get();
        return view('documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,docx'
        ]);
        $user_id = auth()->id();

        $path = $request->file('file')->store('documents', 'public');

        DB::table('documents')->insert([
            'user_id' => $user_id,
            'file_name'=>$request->title,
            'file_path' => $path,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('documents.index')->with('success', 'Upload successful.');
    }

    public function show($id)
    {
        try {
            $document = DB::table('documents')->where('id', $id)->first();

            return view('documents.show', compact('document'));
        } catch (\Throwable $e) {
            Log::error('Document show error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load.');
        }
    }

    public function download($id)
    {
        $document = DB::table('documents')->find($id);

        if (!$document) {
            //abort(404);
            return back()->with('error', 'Document not found.');
        }
        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $fileName = $document->file_name . '.' . $extension;

        return Storage::disk('public')->download($document->file_path, $fileName);

        // return Storage::download($minute->file_path);
    }

    public function destroy($id)
    {
        try {
            DB::table('documents')->where('id', $id)->delete();
            return back()->with('success', 'Deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Document delete error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete.');
        }
    }
}
