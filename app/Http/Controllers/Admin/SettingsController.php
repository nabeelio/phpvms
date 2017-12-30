<?php

namespace App\Http\Controllers\Admin;

use Log;
use Illuminate\Http\Request;

use App\Models\Setting;

class SettingsController extends BaseController
{
    /**
     * Display the settings. Group them by the setting group
     */
    public function index()
    {
        $settings = Setting::orderBy('order', 'asc')->get();
        $settings = $settings->groupBy('group');

        return view('admin.settings.index',[
            'grouped_settings' => $settings,
        ]);
    }

    /**
     * Update the specified setting in storage.
     */
    public function update(Request $request)
    {
        foreach($request->post() as $id => $value) {
            $setting = Setting::find($id);
            if(!$setting) {
                continue;
            }

            Log::info('Updating "'.$setting->key.'" from "'.$setting->value.'" to "'.$value.'"');
            $setting->value = $value;
            $setting->save();
        }

        flash('Settings saved!');
        return redirect('/admin/settings');
    }

}
