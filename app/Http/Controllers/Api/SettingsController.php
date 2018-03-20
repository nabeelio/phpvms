<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Setting as SettingResource;
use App\Interfaces\Controller;
use App\Repositories\SettingRepository;
use Illuminate\Http\Request;

/**
 * Class SettingsController
 * @package App\Http\Controllers\Api
 */
class SettingsController extends Controller
{
    private $settingRepo;

    /**
     * SettingsController constructor.
     * @param SettingRepository $settingRepo
     */
    public function __construct(
        SettingRepository $settingRepo
    ) {
        $this->settingRepo = $settingRepo;
    }

    /**
     * Return all the airlines, paginated
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $settings = $this->settingRepo
            ->orderBy('order', 'asc')->get();

        return SettingResource::collection($settings);
    }
}
