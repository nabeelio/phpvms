<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Laracasts\Flash\Flash;

class ModulesController extends Controller
{
    private $moduleSvc;

    public function __construct(ModuleService $moduleSvc)
    {
        $this->moduleSvc = $moduleSvc;
    }

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
        return view('admin.modules.index', [
            'modules' => $modules,
        ]);
    }

    public function create()
    {
        return view('admin.modules.create');
    }

    public function store(Request $request)
    {
        $array = [];

        array_push($array, $request->file('module_file'));
        array_push($array, $request->has('enabled'));

        $store = $this->moduleSvc->createModule($array);

        if ($store) {
            Flash::success('Module Installed Successfully!');
        } else {
            Flash::error('Something Went Wrong! Please check the structure again or the module already exists!');
        }
        return redirect(route('admin.modules.index'));
    }

    public function edit($id)
    {
        $module = Module::find($id);
        return view('admin.modules.edit', [
            'module' => $module,
        ]);
    }

    public function update($id, Request $request)
    {
        $module = Module::find($id);
        $module->update([
            'enabled' => $request->has('enabled') ? 1 : 0,
        ]);
        Flash::success('Module Status Changed!');
        return redirect(route('admin.modules.index'));
    }

    public function destroy($id, Request $request)
    {
        $module = Module::find($id);
        if ($request->input('verify') === $module->name) {
            $module->delete();
            $moduleDir = base_path().'/modules/'.$module->name;
            if (File::exists($moduleDir)) {
                File::deleteDirectory($moduleDir);
                Flash::success('Module Deleted Successfully!');
            } else {
                Flash::error('Module Folder does not exists!');
            }
            return redirect(route('admin.modules.index'));
        }
        Flash::error('Verification Failed!');
        return redirect(route('admin.modules.edit', $id));
    }
}
