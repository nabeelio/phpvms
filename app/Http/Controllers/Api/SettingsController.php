<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Repositories\SettingRepository;
use App\Http\Resources\Setting as SettingResource;

class SettingsController extends RestController
{
    protected $settingRepo;

    public function __construct(SettingRepository $settingRepo) {
        $this->settingRepo = $settingRepo;
    }

    /**
     * Return all the airlines, paginated
     */
    public function index(Request $request)
    {
        $settings = $this->settingRepo
            ->orderBy('order', 'asc')->get();

        return SettingResource::collection($settings);
    }
}
