<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], 422);
        }

        // Create a new user record
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Generate the access token for the newly registered user
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        // Optionally, set token expiration time (default is 1 year)
        $token->expires_at = Carbon::now()->addYear();
        $token->save();

        // Return the user and access token
        return response([
            'user' => $user,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
        ], 200);
    }

    public function login(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], 422);
        }

        // Check if the provided credentials are valid
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response(['error' => 'Unauthorized'], 401);
        }

        // Get the authenticated user
        $user = $request->user();

        // Generate the access token for the authenticated user
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        // Optionally, set token expiration time (default is 1 year)
        $token->expires_at = Carbon::now()->addYear();
        $token->save();

        // Return the user and access token
        return response([
            'user' => $user,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
        ], 200);
    }

    public function logout(Request $request)
    {
        // Revoke the user's access token
        if (Auth::guard('api')->check()) {
            $request->user()->token()->revoke();
            // Return a success message
            return response(['message' => 'Logged out successfully'], 200);
        }
        return Response(['data' => 'Unauthorized'],401);
    }

    public function refresh(Request $request)
    {
        // Get the user's current access token
        $currentToken = $request->user()->token();

        // Revoke the current access token
        $currentToken->revoke();

        // Generate a new access token
        $newTokenResult = $request->user()->createToken('Personal Access Token');
        $newToken = $newTokenResult->token;

        // Optionally, set token expiration time (default is 1 year)
        $newToken->expires_at = Carbon::now()->addYear();
        $newToken->save();

        // Return the new access token
        return response([
            'access_token' => $newTokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($newToken->expires_at)->toDateTimeString(),
        ], 200);
    }

    public function user(Request $request)
    {
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            return Response(['data' => $user], 200);
        }
        return Response(['data' => 'Unauthorized'], 401);
    }
}
