<?php

namespace App\Http\Composers;

use App\Services\VersionService;
use Illuminate\View\View;

class VersionComposer
{
    protected $versionSvc;

    public function __construct(VersionService $versionSvc)
    {
        $this->versionSvc = $versionSvc;
    }

    public function compose(View $view)
    {
        $view->with('version', $this->versionSvc->getCurrentVersion(false));
        $view->with('version_full', $this->versionSvc->getCurrentVersion(true));
    }
}
