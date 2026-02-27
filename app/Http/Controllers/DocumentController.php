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
}
