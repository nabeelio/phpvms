<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Data migration: establish the `vacentral_id` setting.
 *
 * This is the VA's identity ON VACENTRAL — issued by vacentral when an install
 * completes setup, and written here by the vacentral addon. The ACARS and
 * vacentral addons both read it, so one install reports flights, syncs, and
 * registers under a single id.
 *
 * The row is created EMPTY on purpose. `va_global_id` is this install's own
 * registry identity and is not the same thing, so its value is deliberately not
 * carried over: an install that has never connected to vacentral has no
 * vacentral id, and pretending otherwise would hand vacentral an id it never
 * issued. Also seeded by SettingsSeeder for fresh installs; this covers
 * existing ones, and is idempotent.
 */
return new class() extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        $id = Setting::formatKey('vacentral_id');

        if (DB::table('settings')->where('id', $id)->exists()) {
            return;
        }

        DB::table('settings')->insert([
            'id'          => $id,
            'key'         => 'vacentral_id',
            'name'        => 'vaCentral ID',
            'value'       => '',
            'default'     => '',
            'group'       => 'general',
            'type'        => 'hidden',
            'options'     => '',
            'description' => '',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
};
