<?php

declare(strict_types=1);

use App\Features\Assets\AssetService;
use App\Models\Asset;
use App\Models\Award;
use App\Models\User;
use Igaster\LaravelTheme\Facades\Theme;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function (): void {
    Theme::set('skylight');
    updateSetting('general.theme', 'skylight');
});

it('marks the signed-in pilot own profile as own', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/profile/'.$user->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('Profile', false)
            ->where('profile.isOwnProfile', true));
});

it("does not mark another pilot's profile as own", function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)
        ->get('/profile/'.$other->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->where('profile.isOwnProfile', false));
});

it("shows another pilot's shortened name, not their full name", function (): void {
    // A two-word name discriminates: name_private returns "John S" for
    // "John Smith" -- a single-word name would return unchanged and prove
    // nothing.
    $user = User::factory()->create();
    $other = User::factory()->create(['name' => 'John Smith']);

    $this->actingAs($user)
        ->get('/profile/'.$other->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->where('profile.name', 'John S'));
});

it('shows the full name on your own profile', function (): void {
    $user = User::factory()->create(['name' => 'John Smith']);

    $this->actingAs($user)
        ->get('/profile/'.$user->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->where('profile.name', 'John Smith'));
});

it('normalizes a blank award description to null', function (string $blankDescription): void {
    $user = User::factory()->create();
    $award = Award::factory()->create(['description' => $blankDescription]);
    $user->awards()->attach($award);

    $this->actingAs($user)
        ->get('/profile/'.$user->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->has('profile.awards', 1)
            ->where('profile.awards.0.description', null));
})->with([
    'empty paragraph'          => ['<p></p>'],
    'paragraph with only nbsp' => ['<p>&nbsp;</p>'],
]);

it('keeps a real award description, flattened to plain text', function (): void {
    $user = User::factory()->create();
    // Descriptions are plain text now; markup from the old RichEditor is
    // flattened on write by Award::description(). See AwardDescriptionTest.
    $award = Award::factory()->create(['description' => '<p>Real <strong>text</strong></p>']);
    $user->awards()->attach($award);

    $this->actingAs($user)
        ->get('/profile/'.$user->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->where('profile.awards.0.description', 'Real text'));
});

it('renders the Blade profile with the Edit button on your own profile', function (): void {
    $user = User::factory()->create();
    Theme::set('seven');
    updateSetting('general.theme', 'seven');

    $this->actingAs($user)
        ->get('/profile/'.$user->id)
        ->assertOk()
        ->assertSee('/profile/'.$user->id.'/edit', false);
});

it('hides the Edit button on the Blade profile of another pilot', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Theme::set('seven');
    updateSetting('general.theme', 'seven');

    $this->actingAs($user)
        ->get('/profile/'.$other->id)
        ->assertOk()
        ->assertDontSee('/profile/'.$other->id.'/edit', false);
});

/**
 * Award::imageUrl() queries assets once per award through assetUrl() unless
 * something preloads them first -- ProfileController::show() does, via
 * Award::preloadAssetUrls(). This fails on the pre-fix controller, which
 * issued one `assets` query per award (3 here) instead of the one preload
 * query below.
 */
it('resolves several award badges with one asset query, not one per award', function (): void {
    fakeAssetDisks();
    $user = User::factory()->create();
    $awards = Award::factory()->count(3)->create(['image_url' => null]);

    foreach ($awards as $award) {
        $user->awards()->attach($award);
        app(AssetService::class)->storeContents(
            ASSET_TEST_PNG."\x00".$award->id,
            Asset::SLOT_AWARD,
            (string) $award->id,
            storage: (string) config('filesystems.public_files'),
        );
    }

    DB::enableQueryLog();
    $this->actingAs($user)->get('/profile/'.$user->id)->assertOk();
    // The page also queries `assets` for branding and the airline logo --
    // this narrows to the award badge lookup specifically, by its slot binding.
    $awardAssetQueries = collect(DB::getQueryLog())
        ->filter(fn (array $q): bool => str_contains($q['query'], 'assets') && in_array(Asset::SLOT_AWARD, $q['bindings'], true));
    DB::disableQueryLog();

    expect($awardAssetQueries)->toHaveCount(1);
});

it('sends the edit payload only for the pilot own profile', function (): void {
    $user = User::factory()->create(['email' => 'own.profile@phpvms.net']);

    $this->actingAs($user)
        ->get('/profile/'.$user->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->where('profileEdit.email', 'own.profile@phpvms.net')
            ->where('profileEdit.name', $user->name)
            ->has('profileEdit.airlines')
            ->has('profileEdit.countries')
            ->has('profileEdit.timezones'));
});

it('never sends the edit payload -- and so never the email -- for another pilot', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create(['email' => 'someone.else@phpvms.net']);

    $response = $this->actingAs($user)->get('/profile/'.$other->id)->assertOk();

    $response->assertInertia(fn (Assert $page): Assert => $page->where('profileEdit', null));
    $response->assertDontSee('someone.else@phpvms.net');
});

it('decodes the nbsp padding out of timezone labels', function (): void {
    // Timezonelist pads labels with &nbsp; for Blade's raw echo; Vue
    // interpolation escapes, so an undecoded label renders the entity text.
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/profile/'.$user->id)
        ->assertOk()
        ->assertInertia(function (Assert $page): void {
            $timezones = $page->toArray()['props']['profileEdit']['timezones'];

            expect($timezones)->not->toBeEmpty();

            foreach ($timezones as $timezone) {
                expect($timezone['label'])
                    ->not->toContain('&nbsp;')
                    ->not->toContain('&amp;');
            }
        });
});

it('persists opt_in, which the update rules previously dropped', function (): void {
    $user = User::factory()->create(['opt_in' => false]);

    $this->actingAs($user)->put('/profile/'.$user->id, [
        'name'       => $user->name,
        'email'      => $user->email,
        'airline_id' => $user->airline_id,
        'opt_in'     => '1',
    ])->assertRedirect();

    expect($user->fresh()->opt_in)->toBeTrue();
});

it('clears opt_in when the box is absent from the payload', function (): void {
    // An unchecked box is simply not sent, so this is the only way a pilot can
    // ever opt back out.
    $user = User::factory()->create(['opt_in' => true]);

    $this->actingAs($user)->put('/profile/'.$user->id, [
        'name'       => $user->name,
        'email'      => $user->email,
        'airline_id' => $user->airline_id,
    ])->assertRedirect();

    expect($user->fresh()->opt_in)->toBeFalse();
});

it('sends the api key and connection list with the edit payload', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/profile/'.$user->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->where('profileEdit.apiKey', $user->api_key)
            ->where('profileEdit.optIn', (bool) $user->opt_in)
            ->has('profileEdit.connections')
            ->where('profileEdit.avatarWidth', (int) config('phpvms.avatar.width'))
            ->where('profileEdit.avatarHeight', (int) config('phpvms.avatar.height')));
});

it("never sends another pilot's api key", function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)
        ->get('/profile/'.$other->id)
        ->assertOk()
        ->assertDontSee($other->api_key);
});

it('surfaces the laracasts flash message the controllers actually write', function (): void {
    // ProfileController::update ends in Flash::success(), which writes to
    // session('flash_notification') -- not session('success'). Nothing read
    // that key, so every SPA flash was silently dropped.
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put('/profile/'.$user->id, [
            'name'       => $user->name,
            'email'      => $user->email,
            'airline_id' => $user->airline_id,
        ])
        ->assertRedirect();

    $this->actingAs($user)
        ->get('/profile/'.$user->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->where('flash.success', 'Profile updated successfully!'));
});

it('surfaces the flash from regenerating the api key', function (): void {
    $user = User::factory()->create();
    $before = $user->api_key;

    $this->actingAs($user)->get('/profile/regen_apikey')->assertRedirect();

    expect($user->fresh()->api_key)->not->toBe($before);

    $this->actingAs($user)
        ->get('/profile/'.$user->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->where('flash.success', 'New API key generated!'));
});
