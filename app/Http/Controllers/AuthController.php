<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //register users.
    public function register(Request $request){
        //validate fields.
        $fields = $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        //save user.
        $user = User::create($fields);

        //create a token for every user successfully signed with the user name.
        $token = $user->createToken($request->name);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully!',
            'data' => $user,
            'token' => $token->plainTextToken
        ]);

    }


    //login users.
    public function login(Request $request){
        //validate fields.
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required', 
        ]);

        $user = User::where('email', $request->email)->first();

        if($user && Hash::check($request->password, $user->password)){

            $token = $user->createToken($user->name); //create token.

            return response()->json([
                'status' => true,
                'message' => 'Login successfull',
                'data' => $user,
                'token' => $token->plainTextToken
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Email or password Incorrect'
        ]);
    }


    //logout users.
    public function logout(Request $request){
        //get all user with the token and delete all.
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged Out!'
        ]);
    }
}
