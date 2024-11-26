<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function searchByUsername(Request $request)
    {
        // Captura o termo pesquisado
        $username = $request->input('username');

        $user = User::where('username', $username)->first();

        if ($user) {
            return redirect()->route('user.profile', ['id' => $user->id]);
        } else {
            return redirect()->route('home')->with('error', 'No user found with this username.');
        }
    }
    public function profile($id)
    {
        $user = User::findOrFail($id);

        return view('users.profile', ['user' => $user]);
    }
}
