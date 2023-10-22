<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\loginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(loginRequest $request)
    {
        
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['اسم المستخدم او كلمة المرور غير صحيحة']
            ], 401);
        }

        $token = $user->createToken('main')->plainTextToken;
        return response(compact('user', 'token'));
    }
}
