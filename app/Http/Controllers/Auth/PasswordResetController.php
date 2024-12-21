<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

use App\Models\User;

class PasswordResetController extends Controller 
{

    public function showResetRequestForm()
    {
        return view('auth.forgotPassword');
    }
    

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        
        $user = User::where('email', $request->email)->firstOrFail();
        $token = URL::temporarySignedRoute(
            'password.reset', 
            now()->addMinutes(30), 
            ['token' => Str::random(32), 'email' => $user->email]
        );

        Mail::send('emails.mail', ['token' => $token, 'user' => $user], function($message) use ($user) {
            $message->subject('Password Reset Request');
            $message->to($user->email, $user->username);
        });

        return redirect()->route('password.request')->with('status', 'Password reset link has been sent to your email.');
    }

    /**
     * Display the password reset view for the given token.
     */
    public function showResetForm(Request $request)
    {
        if (!$request->hasValidSignature()) {
            return abort(403, 'Invalid or expired link.');
        }

        $updateRoute = URL::temporarySignedRoute(
            'password.update', 
            now()->addMinutes(30), 
            ['token' => $request->token, 'email' => $request->email]
        );
    
        return view('auth.resetPassword', [
            'email' => $request->email,
            'token' => $request->token, 
            'updateRoute' => $updateRoute 
        ]);    
    }

    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        if (!$request->hasValidSignature()) {
            return abort(403, 'Invalid or expired link.');
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('status', 'Password successfully updated. Please log in.');
    }


}