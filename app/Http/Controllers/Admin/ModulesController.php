<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Services\ModuleService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ModulesController extends Controller
{
    private ModuleService $moduleSvc;

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
     * @return Application|Factory|View
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
     * @return Application|RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
        $this->moduleSvc->installModule($request->file('module_file'));
        return $this->index();
    }

    /**
     * Show the form for editing the specified Module.
     *
     * @param $id
     *
     * @return Application|Factory|View
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
     * @return Application|RedirectResponse|Redirector
     */
    public function update($id, Request $request)
    {
        $this->moduleSvc->updateModule($id, $request->has('enabled'));
        flash()->success('Module Status Changed!');
        return redirect(route('admin.modules.index'));
    }

    /**
     * Enabling Module Present in the Modules Folder
     *
     * @param Request $request
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function enable(Request $request)
    {
        $moduleName = $request->input('name');

        try {
            $this->moduleSvc->addModule($moduleName);
        } catch (\Exception $e) {
            Log::error('Error activating module '.$moduleName);
        }

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
            flash()->success('Module Deleted Successfully!');
            return redirect(route('admin.modules.index'));
        }
        flash()->error('Verification Failed!');
        return redirect(route('admin.modules.edit', $id));
    }
}
