<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use Nwidart\Modules\Json;

class ModulesController extends Controller
{
    private $moduleSvc;

    protected $paths = [];
    /**
     * @var Filesystem
     */
    private $files;

    public function __construct(ModuleService $moduleSvc, Filesystem $files)
    {
        $this->moduleSvc = $moduleSvc;
        $this->files = $files;
    }

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        //$this->scan();
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
        $array = [
            'file'    => $request->file('module_file'),
            'enabled' => $request->has('enabled') ? 1 : 0,
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
        $module = Module::find($id);
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
//    public function scan()
//    {
//        $paths = $this->paths;
//
//        $paths[] = base_path('Modules');
//
//        $paths = array_merge($paths, [
//            base_path('vendor/*/*'),
//            base_path('modules/*'),
//        ]);
//
//        $paths = array_map(function ($path) {
//            return Str::endsWith($path, '/*') ? $path : Str::finish($path, '/*');
//        }, $paths);
//
//        $modules = [];
//
//        foreach ($paths as $key => $path) {
//            $manifests = $this->files->glob("{$path}/module.json");
//
//            is_array($manifests) || $manifests = [];
//
//            foreach ($manifests as $manifest) {
//                $name = Json::make($manifest)->get('name');
//                $modules[$name] = $this->createModule($name);
//            }
//        }
//
//        return $modules;
//    }
//
//    public function createModule($name)
//    {
//        if (!(new Module())->where('name', $name)->exists()) {
//            (new Module())->create([
//                'name'    => $name,
//                'enabled' => 1,
//            ]);
//        }
//    }
}
