<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class SocialiteController extends Controller
{
    public function googleLogin(){
        return Socialite::driver('google')->redirect();       
    }

    public function googleAuth(){
        $googleUser = Socialite::driver('google')->user();
        $user = User::where('provider_id', $googleUser->id)->first();

        if($user){
            event(new Registered($user));
            Auth::login($user);
            return redirect()->route('dashboard');           
        }else{
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password'=> Hash::make('password@1234'),
                'provider_id' => $googleUser->id,
                'provider' => 'google'
            ]);

            event(new Registered($user));
            Auth::login($user);

            return redirect()->route('dashboard');
        }
    }
}
