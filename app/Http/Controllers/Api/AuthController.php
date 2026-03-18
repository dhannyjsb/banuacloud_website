<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! in_array($user->role, ['admin', 'editor'], true)) {
            return response()->json([
                'message' => 'This account is not allowed to access the admin panel.',
            ], Response::HTTP_FORBIDDEN);
        }

        $user->tokens()->where('name', 'admin-panel')->delete();

        $token = $user->createToken('admin-panel')->plainTextToken;

        return response()->json([
            'user' => $this->formatUser($user),
            'token' => $token,
        ]);
    }

    public function validateToken(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! in_array($user->role, ['admin', 'editor'], true)) {
            return response()->json([
                'message' => 'This account is not allowed to access the admin panel.',
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'user' => $this->formatUser($user),
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'currentPassword' => ['required', 'string'],
            'newPassword' => ['required', 'string', 'min:8', 'different:currentPassword', 'confirmed'],
        ]);

        /** @var User $user */
        $user = $request->user();

        if (! Hash::check($data['currentPassword'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->forceFill([
            'password' => $data['newPassword'],
        ])->save();

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * @return array{id:string,name:string,email:string,role:string}
     */
    private function formatUser(User $user): array
    {
        return [
            'id' => (string) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];
    }
}
