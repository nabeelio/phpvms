<?php

namespace App\Support\Modules;

use Exception;
use Illuminate\Config\Repository as Config;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Nwidart\Modules\Contracts\ActivatorInterface;
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
        try {
            $modules = \App\Models\Module::all();
            $retVal = [];
            foreach ($modules as $i) {
                $retVal[$i->name] = $i->enabled;
            }
            return $retVal;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        (new \App\Models\Module())->truncate();
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
     * \Nwidart\Modules\Module instance passed
     * {@inheritdoc}
     */
    public function hasStatus(Module $module, bool $status): bool
    {
        try {
            $module = (new \App\Models\Module())->where('name', $module->getName());
            if ($module->exists()) {
                return $module->first()->enabled == 1;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
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
        $this->writeDB($name, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Module $module): void
    {
        $name = $module->getName();
        $this->writeDB($name, false, 1);
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
            $module = (new \App\Models\Module())->where('name', $name);
            if ($module->exists()) {
                $module->update([
                    'status' => $status,
                ]);
            }
        }
    }
}
