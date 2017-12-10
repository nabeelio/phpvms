<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

class SettingRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    public function model()
    {
        return Setting::class;
    }

    /**
     * Get a setting, reading it from the cache
     * @param array $key
     * @return mixed|void
     */
    public function get($key)
    {

    }

    public function set($key, $value)
    {

    }
}
