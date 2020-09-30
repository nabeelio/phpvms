<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use Madnest\Madzipper\Madzipper;
use App\Models\Module;

class ModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index()
    {
        $modules = Module::all();
        return view('admin.modulesmanager.index', [
            'modules' => $modules
        ]);
    }

    public function addModule(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin.modulesmanager.add');
        }
        elseif ($request->isMethod('post')) {
            $this->addModulePost($request);
        }
        Flash::success('Module Installed Successfully!');
        return redirect(route('admin.modulesmanager.index'));
    }

    public function addModulePost($request)
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

            Module::create([
                'name' => $module,
                'enabled' => $request->has('enabled') ? 1 : 0,
            ]);
            return true;
        }
    }

    public function editModule($id, Request $request)
    {
        $module = Module::find($id);
        if ($request->isMethod('get')) {
            return view('admin.modulesmanager.edit',[
                'module' => $module
            ]);
        }
        elseif ($request->isMethod('post')) {
            $module->update([
                'enabled' => $request->has('enabled') ? 1 : 0
            ]);
        }
        Flash::success('Module Status Changed!');
        return $this->index();
    }

    public function deleteModule($id, Request $request)
    {
        $module = Module::find($id);
        if($request->input('verify') === $module->name)
        {
            $moduleDir = base_path().'/modules/'.$module->name;
            if(File::exists($moduleDir))
            {
                Artisan::call('module:delete '. $module->name);
                $module->delete();
                Flash::success('Module Deleted Successfully!');
            } else {
                Flash::error('Module does not exists!');
            }
            return $this->index();
        } else {
            Flash::error('Verification Failed!');
            return redirect(route('admin.modulesmanager.edit', $id));
        }
    }

}
