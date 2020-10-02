<?php

namespace App\Support\Modules;

use Illuminate\Config\Repository as Config;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Nwidart\Modules\Contracts\ActivatorInterface;
use Nwidart\Modules\Json;
use Nwidart\Modules\Module;

class DatabaseActivator implements ActivatorInterface
{
    /**
     * Laravel config instance
     *
     * @var Config
     */
    private $config;

    /**
     * @var Filesystem
     */
    private $files;

    /**
     * The module path.
     *
     * @var string|null
     */
    protected $path;

    /**
     * The scanned paths.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * Array of modules activation statuses
     *
     * @var array
     */
    private $modulesStatuses;

    public function __construct(Container $app, $path = null)
    {
        $this->config = $app['config'];
        $this->files = $app['files'];
        $this->modulesStatuses = $this->getModulesStatuses();
        $this->path = $path;
    }

    /**
     * Get modules statuses, from the database
     *
     * @return array
     */
    private function getModulesStatuses(): array
    {
        $this->scan();
        $modules = \App\Models\Module::all();
        $retVal = [];
        foreach ($modules as $i) {
            $retVal[$i->name] = $i->enabled;
        }
        return $retVal;
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        (new \App\Models\Module())->truncate();
        $this->modulesStatuses = [];
    }

    /**
     * {@inheritdoc}
     */
    public function enable(Module $module): void
    {
        $this->setActiveByName($module->getName(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function disable(Module $module): void
    {
        $this->setActiveByName($module->getName(), false);
    }

    /**
     * {@inheritdoc}
     */
    public function hasStatus(Module $module, bool $status): bool
    {
        if (!isset($this->modulesStatuses[$module->getName()])) {
            return $status === false;
        }

        return $this->modulesStatuses[$module->getName()] === $status;
    }

    /**
     * {@inheritdoc}
     */
    public function setActive(Module $module, bool $active): void
    {
        $this->setActiveByName($module->getName(), $active);
    }

    /**
     * {@inheritdoc}
     */
    public function setActiveByName(string $name, bool $status): void
    {
        $this->modulesStatuses[$name] = $status;
        $this->writeDB($name, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Module $module): void
    {
        $module_name = $module->getName();
        if (!isset($this->modulesStatuses[$module_name])) {
            return;
        }
        unset($this->modulesStatuses[$module_name]);
        $this->writeDB($module_name, false, 1);
    }

    /**
     * Writes the activation statuses in a file, as json
     *
     * @param $name
     * @param $status
     * @param string $delete
     */
    private function writeDB($name, $status, $delete = ''): void
    {
        if (!empty($delete)) {
            (new \App\Models\Module())->where([
                'name' => $name,
            ])->delete();
        } else {
            (new \App\Models\Module())->where([
                'name' => $name,
            ])->update([
                'status' => $status,
            ]);
        }
    }

    /**
     * Get & scan all modules.
     *
     * @return array
     */
    public function scan()
    {
        $paths = $this->getScanPaths();

        $modules = [];

        foreach ($paths as $key => $path) {
            $manifests = $this->getFiles()->glob("{$path}/module.json");

            is_array($manifests) || $manifests = [];

            foreach ($manifests as $manifest) {
                $name = Json::make($manifest)->get('name');

                $modules[$name] = $this->createModule($name);
            }
        }

        return $modules;
    }

    public function getPath(): string
    {
        return base_path('modules/*');
    }

    /**
     * Get all additional paths.
     *
     * @return array
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Get scanned modules paths.
     *
     * @return array
     */
    public function getScanPaths(): array
    {
        $paths = $this->paths;

        $paths[] = $this->getPath();

        $paths = array_merge($paths, [
            base_path('vendor/*/*'),
            base_path('modules/*'),
        ]);

        $paths = array_map(function ($path) {
            return Str::endsWith($path, '/*') ? $path : Str::finish($path, '/*');
        }, $paths);

        return $paths;
    }

    /**
     * Get laravel filesystem instance.
     *
     * @return Filesystem
     */
    public function getFiles(): Filesystem
    {
        return $this->files;
    }


    protected function createModule($name)
    {
        if (!(new \App\Models\Module())->where('name', $name)->exists()) {
            (new \App\Models\Module())->create([
                'name'    => $name,
                'enabled' => 0,
                'is_new'  => 1,
            ]);
        }
    }
}
