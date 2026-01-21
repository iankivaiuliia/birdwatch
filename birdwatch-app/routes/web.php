<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/whoami', function (Request $request) {
    return $request->user();
})->middleware('auth');

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
Route::post('/auth/logout', [GoogleAuthController::class, 'logout']);
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->noContent();
});

Route::get('/auth/google', fn () => Socialite::driver('google')->redirect()
);

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::updateOrCreate(
        ['google_id' => $googleUser->getId()],
        [
            'email' => $googleUser->getEmail(),
            'name' => $googleUser->getName(),
            'avatar_url' => $googleUser->getAvatar(),
        ]
    );

    Auth::login($user);

    return redirect('http://localhost:5173');
});

Route::post('/forgot-password', [ForgotPasswordController::class, 'store']);
