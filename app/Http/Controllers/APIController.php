<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Hash;
use App\User;

class APIController extends Controller
{
    public function register(Request $request) {
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        User::create($input);
        return response()->json(['result' => true]);
    }

    public function login(Request $request) {
        $input = $request->all();
        if (! $token = JWTAuth::attempt($input)) {
            return response()->json(['result' => 'wrong email or password.']);
        }
        return response()->json(['result' => $token]);
    }

    public function getUserDetails(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json(['result' => $user]);
    }

    public function index() {
        $objUsers = User::all();
        return response()->json(compact('objUsers'));
    }
}
