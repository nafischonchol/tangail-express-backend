<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AdminLoginRequest;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle admin login via email or phone.
     */
    public function login(AdminLoginRequest $request): JsonResponse
    {
        $loginInput = $request->input('login')
            ?? $request->input('email')
            ?? $request->input('phone');

        $admin = Admin::where('email', $loginInput)
            ->orWhere('phone', $loginInput)
            ->first();

        if (! $admin || ! Hash::check($request->input('password'), $admin->password)) {
            return responseError('ভুল ইমেইল/ফোন নম্বর অথবা পাসওয়ার্ড।', 401);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return responseSuccess([
            'admin' => $admin,
            'token' => $token,
        ], 'লগইন সফল হয়েছে।');
    }

    /**
     * Get authenticated admin details.
     */
    public function me(Request $request): JsonResponse
    {
        $admin = $request->user();

        return responseSuccess([
            'admin' => $admin,
        ], 'অ্যাডমিন প্রোফাইল তথ্য পাওয়া গেছে।');
    }

    /**
     * Handle admin logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && method_exists($user, 'currentAccessToken')) {
            $user->currentAccessToken()?->delete();
        }

        return responseSuccess(null, 'লগআউট সফল হয়েছে।');
    }
}
