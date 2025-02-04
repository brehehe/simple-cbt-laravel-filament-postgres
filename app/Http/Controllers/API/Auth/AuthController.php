<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Resources\General\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $rules = [
            'username' => ['required'],
            'password' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->sendError('Input tidak sesuai dengan ketentuan.', $validator->errors(), 400);
        $username = Str::lower($request->username);

        if (Auth::attempt(['username' => $username, 'password' => $request->password])) {

            $admin = Auth::user();
            $token = $admin->createToken('auth_token')->plainTextToken;

            $data = [
                'access_token' => $token,
                'token_type'   => 'Bearer'
            ];

            return $this->sendResponse($data, 'Hi '. $admin->name .', Selamat Datang di '. config('app.name') .' !');

        } else {

            return $this->sendError('Maaf gagal melakukan Login Aplikasi!');

        }
    }

    public function logout()
    {
        $user = Auth::user();
        if(!$user) return $this->sendError('Anda belum Login');
        $user->tokens()->delete();
        return $this->sendSuccess('Anda Berhasil Logout!');
    }

    public function profile()
    {
        $user = Auth::user();
        if(!$user) return $this->sendError('Anda belum Login');
        return $this->sendResponse(new ProfileResource($user), "Data Profil $this->created_msg");
    }
}