<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function login(Request $request): JsonResponse
    {
        $phoneNumber = $request->input('phoneNumber');
        $password = $request->input('password');
        try {
            $user = User::where('phoneNumber', $phoneNumber)->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            if (!Hash::check($password, $user->password)) {
                return response()->json(['error' => 'Wrong password'], 401);
            }

            $user->loginStatus = true;
            $user->save();

            return response()->json(['success' => true, 'name' => $user['name'], 'bloodType' => $user['bloodType'], 'birthDate' => $user['birthDate'], 'email' => $user['email']]);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred', 'exception' => $e], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $phoneNumber = $request->input('phoneNumber');
        try {
            $user = User::where('phoneNumber', $phoneNumber)->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $user->loginStatus = false;
            $user->save();

            return response()->json(['success' => true]);
        } catch (Exception) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    public function editProfile(Request $request): JsonResponse {

        $phoneNumber = $request->input('phoneNumber');
        $name = $request->input('name');
        $bloodType = $request->input('bloodType');
        $birthDate = $request->input('birthDate');
        $email = $request->input('email');

        try {
            $user = User::where('phoneNumber', $phoneNumber)->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $user->name = $name;
            $user->bloodType = $bloodType;
            $user->birthDate = $birthDate;
            $user->email = $email;
            $user->save();

            return response()->json(['success' => true, 'bloodType' => $bloodType]);

        } catch (Exception) {
            return response()->json(['error' => 'An error occurred'], 500);
        }

    }
}
