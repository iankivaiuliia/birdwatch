<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback()
    {
        $g = Socialite::driver('google')->user();

        $email = $g->getEmail();

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $g->getName() ?: 'Aves User',
                'email' => $email,
                'google_id' => $g->getId(),
                'avatar_url' => $g->getAvatar(),
                'password' => bcrypt(str()->random(32)), // просто заглушка
            ]);
        } else {
            $user->update([
                'google_id' => $user->google_id ?: $g->getId(),
                'avatar_url' => $g->getAvatar(),
                'name' => $user->name ?: ($g->getName() ?: $user->name),
            ]);
        }

        Auth::login($user); // создаёт session cookie

        $frontend = env('FRONTEND_URL', '/');

        return redirect()->to($frontend.'/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
