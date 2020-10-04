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
     * Display a listing of the Module.
     *
     * @return mixed
     */
    public function index()
    {
        $modules = $this->moduleSvc->getAllModules();
        $new_modules = $this->moduleSvc->scan();
        return view('admin.modules.index', [
            'modules'     => $modules,
            'new_modules' => $new_modules,
        ]);
    }

    /**
     * Show the form for creating a new Module.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.modules.create');
    }

    /**
     * Store a newly Uploaded Module in the Storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $store = $this->moduleSvc->installModule($request->file('module_file'));

        if ($store) {
            Flash::success('Module Installed Successfully!');
        }
        Flash::error('Something Went Wrong! Please check the structure again or the module already exists!');
        return redirect(route('admin.modules.index'));
    }

    /**
     * Show the form for editing the specified Module.
     *
     * @param $id
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $module = $this->moduleSvc->getModule($id);
        return view('admin.modules.edit', [
            'module' => $module,
        ]);
    }

    /**
     * Update the specified Module in storage.
     *
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {
        $this->moduleSvc->updateModule($id, $request->has('enabled'));
        Flash::success('Module Status Changed!');
        return redirect(route('admin.modules.index'));
    }

    /**
     * Enabling Module Present in the Modules Folder
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function enable(Request $request)
    {
        $this->moduleSvc->addModule($request->input('name'));
        return redirect(route('admin.modules.index'));
    }

    /**
     * Verify and Remove the specified Module from storage.
     *
     * @param mixed   $id
     * @param Request $request
     *
     * @return mixed
     */
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
