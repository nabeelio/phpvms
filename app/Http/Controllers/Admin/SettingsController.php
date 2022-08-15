<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Models\Setting;
use App\Services\FinanceService;
use Igaster\LaravelTheme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    private FinanceService $financeSvc;

    /**
     * @param FinanceService $financeSvc
     */
    public function __construct(FinanceService $financeSvc)
    {
        $this->financeSvc = $financeSvc;
    }

    /**
     * Get a list of themes formatted for a select box
     *
     * @return array
     */
    private function getThemes(): array
    {
        Theme::rebuildCache();
        $themes = Theme::all();
        $theme_list = [];
        foreach ($themes as $t) {
            if (!$t || !$t->name || $t->name === 'false') {
                continue;
            }
            $theme_list[] = $t->name;
        }

        return $theme_list;
    }

    /**
     * Return the currency list
     *
     * @return array
     */
    private function getCurrencyList(): array
    {
        $curr = [];
        foreach (config('money') as $currency => $attrs) {
            $name = $attrs['name'].' ('.$attrs['symbol'].'/'.$currency.')';
            $curr[$currency] = $name;
        }

        return $curr;
    }

    /**
     * Display the settings. Group them by the setting group
     */
    public function index()
    {
        $settings = Setting::where('type', '!=', 'hidden')->orderBy('order')->get();
        $settings = $settings->groupBy('group');

        return view('admin.settings.index', [
            'currencies'       => $this->getCurrencyList(),
            'grouped_settings' => $settings,
            'themes'           => $this->getThemes(),
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

        $this->financeSvc->changeJournalCurrencies();

        flash('Settings saved!');

        return redirect('/admin/settings');
    }
}
