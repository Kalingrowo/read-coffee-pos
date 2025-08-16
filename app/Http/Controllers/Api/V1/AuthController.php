<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
{
    /**
     * Register new user.
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6|confirmed'
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
            ]);

            return $this->successResponse($user, 'User registered successfully', 201);
        } catch (\Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to register user', 500, $e->getMessage());
        }
    }

    /**
     * Handle user login and token creation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'device_name' => 'nullable',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (! $user || ! Hash::check($validated['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
    
            // Delete existing tokens for the device if you want only one active token per device
            $user->tokens()->where('name', ($request->device_name ?? 'PAT - auth token'))->delete();
    
            // Create a new token for the authenticated user
            $token = $user->createToken(($request->device_name ?? 'PAT - auth token'))->plainTextToken;
    
            return $this->successResponse([
                'user' => $user,
                'access_token' => $token
            ], 'Login successful.');
        } catch (ValidationException $ve) {
            return $this->errorResponse('Invalid credentials', 422, $ve->errors());
        } catch (\Exception $e) {
            Log::error('User login failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to login', 500, $e->getMessage());
        }

    }

    /**
     * Handle user logout and token revocation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return $this->successResponse(null, 'Logged out successfully');
        } catch (\Exception $e) {
            Log::error('User logout failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to logout', 500, $e->getMessage());
        }
    }
}
