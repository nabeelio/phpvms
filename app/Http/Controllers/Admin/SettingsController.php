<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Log;

/**
 * Class SettingsController
 */
class SettingsController extends Controller
{
    /**
     * Display the settings. Group them by the setting group
     */
    public function index()
    {
        $settings = Setting::orderBy('order', 'asc')->get();
        $settings = $settings->groupBy('group');

        return view('admin.settings.index', [
            'grouped_settings' => $settings,
        ]);
    }

    /**
     * Update the specified setting in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        foreach ($request->post() as $id => $value) {
            $setting = Setting::find($id);
            if (!$setting) {
                continue;
            }

            if ($setting->type == 'bool' || $setting->type == 'boolean') {
                $value = get_truth_state($value);
            }

            Log::info('Updating "'.$setting->id.'" from "'.$setting->value.'" to "'.$value.'"');
            $setting->value = $value;
            $setting->save();
        }

        flash('Settings saved!');

        return redirect('/admin/settings');
    }
}
