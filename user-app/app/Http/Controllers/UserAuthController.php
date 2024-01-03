<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserAuthController extends Controller
{
    public function showUserForm() {
        return view('uploadUser');
    }

    public function saveUser(Request $request) {
        $userData = $request->only([
            'name',
            'email',
            'id',
        ]);

        $user = new User();
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->id = $userData['id'];
        $user->password = ""; # to be edited

        // user logic to be added 
        $user->save();
    }
}
