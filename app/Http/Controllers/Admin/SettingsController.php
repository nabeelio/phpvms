<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Http\Requests;

use Redirect;
use Titan\Controllers\TitanAdminController;

use Illuminate\Http\Request;

class SettingsController extends BaseController
{
	/**
	 * Display a listing of setting.
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->view('admins.settings.index')->with('items', Setting::all());
	}

	/**
	 * Update the specified setting in storage.
	 *
	 * @param Setting  $setting
     * @param Request    $request
     * @return Response
     */
    public function update(Setting $setting, Request $request)
	{
		$this->validate($request, Setting::$rules, Setting::$messages);

        $this->updateEntry($setting, $request->all());

        return redirect("/admin/settings");
	}

}
