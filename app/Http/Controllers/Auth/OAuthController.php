<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{

    public function redirectToDiscordProvider(): RedirectResponse
    {
        return Socialite::driver('discord')->scopes(['identify'])->redirect();
    }

    public function handleDiscordProviderCallback(): RedirectResponse
    {
        $user = Socialite::driver('discord')->user();

        if ($user->id) {
            Auth::user()?->update([
                'discord_id' => $user->id,
            ]);

            flash()->success('Discord account linked!');
        } else {
            flash()->error('Unable to link Discord account!');
        }

        return redirect()->route('frontend.profile.index');
    }

    public function logoutDiscordProvider(): RedirectResponse
    {
        Auth::user()?->update([
            'discord_id' => null,
        ]);

        flash()->success('Discord account unlinked!');

        return redirect()->route('frontend.profile.index');
    }
}
