<?php

namespace Modules\ModulesManager\Http\Controllers\Admin;

use App\Contracts\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Madnest\Madzipper\Madzipper;
use Modules\ModulesManager\Models\ModuleManager;

/**
 * Admin controller
 */
class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $modules = ModuleManager::all();
        return view('modulesmanager::admin.index', [
            'modules' => $modules
        ]);
    }

    public function addModule(Request $request)
    {
        $zipper = new Madzipper;

        $file = $request->file('module_file');

        $module = Str::replaceLast('.zip', '', $file->getClientOriginalName());

        $moduleFolder = $zipper->make($file)->folder($module);

        $files = $moduleFolder->listFiles();

        $check = count($files);

        $check_b = Str::containsAll(implode(',', $files), [
            'module.json',
            'composer.json',
            'Routes',
            'Controllers',
            'Providers'
        ]);

        if($check < 0 && $check_b != true)
        {
            return redirect()->back()->with('error', 'Error! Please note that the Module folder name and zip name should be same.');
        } else {
            $toExtract = base_path().'/modules/'.$module;
            if(File::exists($toExtract))
            {
                return redirect()->back()->with('error', 'Module Already Exists!');
            }
            $moduleFolder->extractTo($toExtract);
            Artisan::call('module:enable '. $module);
            Artisan::call('config:cache');

            ModuleManager::create([
                'module_name' => $module,
                'enabled' => $request->has('enabled') ? 1 : 0,
            ]);
            return redirect()->back()->with('success', 'Module Installed Successfully!');
        }

    }

    public function editModule(Request $request)
    {
        ModuleManager::where('module_name', $request->input('module_name'))->update([
            'enabled' => $request->enabled == 1 ? 0 : 1
        ]);
        return redirect()->back()->with('success', 'Module Status Changed!');
    }

    public function deleteModule(Request $request)
    {
        $module = $request->input('module_name');
        $moduleDir = base_path().'/modules/'.$module;
        if(File::exists($moduleDir))
        {
            Artisan::call('module:delete '. $module);
            ModuleManager::where('module_name', $module)->delete();
            return redirect()->back()->with('success', 'Module Deleted Successfully!');
        } else {
            return redirect()->back()->with('error', 'Module does not exists!');
        }
    }
}
