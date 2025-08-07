<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->email)->first();
            
            if($user) {
                Auth::login($user);
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt('123456dummy')
                ]);
                
                Auth::login($user);
            }
            
            // Evita redirecionar para rotas AJAX após login social
            $intended = session()->get('url.intended');
            if ($intended && str_contains($intended, '/comentarios/')) {
                return redirect(\App\Providers\RouteServiceProvider::HOME);
            }
            return redirect()->intended(\App\Providers\RouteServiceProvider::HOME);
            
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Erro ao fazer login com Google');
        }
    }
}
