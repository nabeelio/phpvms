<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Http\Request;
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
     * @return mixed
     */
    public function index()
    {
        $modules = Module::all()->sortByDesc('id');
        $new_modules = $this->moduleSvc->scan();
        return view('admin.modules.index', [
            'modules'     => $modules,
            'new_modules' => $new_modules,
        ]);
    }

    public function create()
    {
        return view('admin.modules.create');
    }

    public function store(Request $request)
    {
        $array = [
            'file'    => $request->file('module_file'),
            'enabled' => $request->has('enabled'),
        ];

        $store = $this->moduleSvc->createModule($array);

        if ($store == true) {
            Flash::success('Module Installed Successfully!');
        }
        Flash::error('Something Went Wrong! Please check the structure again or the module already exists!');
        return redirect(route('admin.modules.index'));
    }

    public function edit($id)
    {
        $module = (new Module())->find($id);
        return view('admin.modules.edit', [
            'module' => $module,
        ]);
    }

    public function update($id, Request $request)
    {
        $this->moduleSvc->updateModule($id, $request->has('enabled'));
        Flash::success('Module Status Changed!');
        return redirect(route('admin.modules.index'));
    }

    public function enable(Request $request)
    {
        $this->moduleSvc->enableModule($request->input('name'));
        Flash::success('Module Enabled!');
        return redirect(route('admin.modules.index'));
    }

    public function destroy($id, Request $request)
    {
        $delete = $this->moduleSvc->deleteModule($id, $request->all());
        if ($delete == true) {
            Flash::success('Module Deleted Successfully!');
            return redirect(route('admin.modules.index'));
        }
        Flash::error('Verification Failed!');
        return redirect(route('admin.modules.edit', $id));
    }
}
