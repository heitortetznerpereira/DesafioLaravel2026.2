<?php

namespace App\Http\Controllers;

use App\Mail\AdminMessageMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminMailController extends Controller
{
    public function create()
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        $users = User::where('is_admin', false)->get();

        return view('admin.mail.create', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        Mail::to($user->email)->send(
            new AdminMessageMail(
                $validated['subject'],
                $validated['message'],
            )
        );

        return redirect()
            ->route('admin.mail.create')
            ->with('success', 'E-mail enviado com sucesso!');
    }
}
