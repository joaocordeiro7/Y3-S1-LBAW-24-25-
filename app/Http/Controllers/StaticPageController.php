<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StaticPageController extends Controller
{
    public function submitFeedback(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|max:1000',
        ]);

        Mail::raw($request->message, function ($message) use ($request) {
            $message->to('thebulletin@support.com')
                    ->from($request->email, $request->name)
                    ->subject('Feedback from ' . $request->name);
        });

        return redirect()->route('contacts')->with('success', 'Thank you for your feedback!');
    }
}
