<?php

namespace App\Repositories;

use App\Models\Kvp;
use Illuminate\Support\Facades\Cache;

class KvpRepository
{
    public function __construct()
    {
    }

    /**
     * @param $key
     * @param null $default
     *
     * @return array|string|null
     */
    public function retrieve($key, $default = null)
    {
        return $this->get($key, $default);
    }

    /**
     * Get a value from the KVP store
     *
     * @param string $key
     * @param mixed  $default default value to return
     *
     * @return array|string|null
     */
    public function get($key, $default = null)
    {
        $value = Kvp::where(['key' => $key])->first(['value']);
        return optional($value)->value ?? $default;
    }

    /**
     * @alias store($key,$value)
     *
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    public function save(string $key, $value): void
    {
        Kvp::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Save a value to the KVP store
     *
     * @param string $key
     * @param string $value
     */
    public function store($key, $value): void
    {
        $this->save($key, $value);
    }
}
