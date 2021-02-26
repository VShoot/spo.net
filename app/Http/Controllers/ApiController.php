<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * API signup function
     * 
     * @param Request $request
     * @return response json
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zа-я]+$/iu'],
            'surname' => ['required', 'string', 'max:255', 'regex:/^[a-zа-я]+$/iu'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'name_specialty' => ['required', 'string', 'max:255', 'regex:/^([a-zа-яё]\s?)+$/iu',],
            'course' => ['required', 'integer'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        User::create([
            'name' => $request['name'],
            'surname' => $request['surname'],
            'email' => $request['email'],
            'name_specialty' => $request['name_specialty'],
            'course' => $request['course'],
            'password' => Hash::make($request['password']),
        ]);

        return response()->json(['success' => 'You are sucessfully registered.'], 200);
    }

    /**
     * API login function
     * 
     * @param Request $request
     * @return response json
     */
    public function login(Request $request)
    {
        $user = new User();
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email']
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = $user->where(['email' => $request['email']])->first();
        $token = '';

        if (!Hash::check($request['password'], $user['password'])) {
            return response()->json(['error' => 'Go fuck yourself, stubid boy. I will fisting your ass via my big black friend Jackson'], 400);
        }

        $token = Str::random(60);
        $user->api_token = $token;
        $user->save();

        return response()->json(['success' => 'Success', 'api_token' => $token], 200);
    }
}