<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        Log::debug($user);
        $success['token'] =  $user->createToken('MyAuthApp')->plainTextToken;
        $success['name'] =  $user->name;
        $success['avatar'] =  $user->avatar;
        $success['email'] =  $user->email;
        return response()->json($success, 201);
    }
    public function login(Request $request)
    {
        $attr = $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required|string|min:6'
        ]);

        if (!Auth::attempt($attr)) {
            return response()->json('Credentials not match', 401);
        }
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        return response()->json([
            "token" => $user->createToken('API Token')->plainTextToken,
            "name" => $user->name,
            "email" => $user->email,
            "avatar" => $user->avatar,
        ], 200);
        // return response()->json([
        //     'token' => auth()->user()->createToken('API Token')->plainTextToken
        // ], 200);
    }

    public function logout()
    {
        /** @var \App\Models\User $user */
        $user =  auth()->user();
        $user->tokens()->delete();
    }
}
