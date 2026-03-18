<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Priority 1: intended URL (e.g. set by invitation flow)
            if ($request->session()->has('url.intended')) {
                return redirect()->intended();
            }

            // Priority 2: organizer
            if ($user->organizer) {
                return redirect('/dashboard');
            }

            // Priority 3: collaborations
            $collaborations = \App\Models\EventCollaborator::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->with('event')
                ->get();

            if ($collaborations->count() === 1) {
                return redirect()->route('staff.checkin', $collaborations->first()->event);
            }

            if ($collaborations->count() > 1) {
                return redirect()->route('staff.index');
            }

            return redirect('/my-tickets');
        }

        return back()->withErrors([
            'email' => 'E-mail ou senha incorretos.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        // Activate pending collaborator invite if present
        if ($request->session()->has('pending_collaborator_id')) {
            $collaboratorId = $request->session()->pull('pending_collaborator_id');
            $request->session()->forget('pending_collaborator_email');

            $collaborator = \App\Models\EventCollaborator::find($collaboratorId);

            if ($collaborator
                && $collaborator->status === 'pending'
                && strtolower($collaborator->invitee_email) === strtolower($user->email)
            ) {
                $collaborator->update([
                    'user_id' => $user->id,
                    'status' => 'active',
                    'accepted_at' => now(),
                ]);

                return redirect()->route('staff.checkin', $collaborator->event);
            }

            return redirect('/')->with('error', 'Seu convite foi cancelado antes de ser aceito.');
        }

        return redirect()->intended('/my-tickets');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
