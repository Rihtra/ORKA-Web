<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(Str::random(16)),
                    'role' => 'mahasiswa',
                ]
            );

            $token = $user->createToken('mobile')->plainTextToken;

            // Encode URL parameters untuk menghindari masalah karakter khusus
            $name = urlencode($user->name);
            $email = urlencode($user->email);
            
            return redirect("flutter://callback?token=$token&name=$name&email=$email");
        } catch (\Exception $e) {
            // Handle error dan redirect ke error page
            return redirect("flutter://callback?error=login_failed&message=" . urlencode($e->getMessage()));
        }
    }
}