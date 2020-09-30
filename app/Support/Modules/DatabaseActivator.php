<?php

namespace App\Support\Modules;

use Illuminate\Config\Repository as Config;
use Illuminate\Container\Container;
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
     * Array of modules activation statuses
     *
     * @var array
     */
    private $modulesStatuses;

    public function __construct(Container $app)
    {
        $this->config = $app['config'];
        $this->modulesStatuses = $this->getModulesStatuses();
    }

    /**
     * Get modules statuses, from the database
     * @return array
     */
    private function getModulesStatuses(): array
    {
        $modules = \App\Models\Module::all();
        $retVal = [];
        foreach ($modules as $i) {
            $retVal[$i->name] = $i->enabled;
        }
        return $retVal;
    }

    /**
     * @inheritDoc
     */
    public function reset(): void
    {
        (new \App\Models\Module)->truncate();
        $this->modulesStatuses = [];
    }

    /**
     * @inheritDoc
     */
    public function enable(Module $module): void
    {
        $this->setActiveByName($module->getName(), true);
    }

    /**
     * @inheritDoc
     */
    public function disable(Module $module): void
    {
        $this->setActiveByName($module->getName(), false);
    }

    /**
     * @inheritDoc
     */
    public function hasStatus(Module $module, bool $status): bool
    {
        if (!isset($this->modulesStatuses[$module->getName()])) {
            return $status === false;
        }

        return $this->modulesStatuses[$module->getName()] === $status;
    }

    /**
     * @inheritDoc
     */
    public function setActive(Module $module, bool $active): void
    {
        $this->setActiveByName($module->getName(), $active);
    }

    /**
     * @inheritDoc
     */
    public function setActiveByName(string $name, bool $status): void
    {
        $this->modulesStatuses[$name] = $status;
        $this->writeDB($name, $status);
    }

    /**
     * @inheritDoc
     */
    public function delete(Module $module): void
    {
        $module_name = $module->getName();
        if (!isset($this->modulesStatuses[$module_name])) {
            return;
        }
        unset($this->modulesStatuses[$module_name]);
        $this->writeDB($module_name, false, 0);
    }

    /**
     * Writes the activation statuses in a file, as json
     * @param $name
     * @param $status
     * @param null $delete
     */
    private function writeDB($name, $status, $delete = null): void
    {
        if($delete == 0)
        {
            (new \App\Models\Module)->where([
                'name' => $name
            ])->delete();
        }
        (new \App\Models\Module)->where([
            'name' => $name
        ])->update([
            'status' => $status
        ]);
    }


}
