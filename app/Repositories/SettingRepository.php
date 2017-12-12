<?php

namespace App\Repositories;

use Log;
use Illuminate\Support\Carbon;
use Prettus\Repository\Contracts\CacheableInterface;

use App\Models\Setting;
use App\Repositories\Traits\CacheableRepository;
use Prettus\Validator\Exceptions\ValidatorException;

class SettingRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    public function model()
    {
        return Setting::class;
    }

    /**
     * Get a setting, reading it from the cache possibly
     * @param string $key
     * @return mixed
     */
    public function retrieve($key)
    {
        $key = strtolower($key);
        $setting = $this->findWhere(['key' => $key], ['type', 'value'])->first();

        if(!$setting) {
            return null;
        }

        # cast some types
        switch($setting->type) {
            case 'bool':
            case 'boolean':
                return (bool) $setting->value;
                break;
            case 'date':
                return Carbon::parse($setting->value);
                break;
            case 'int':
            case 'integer':
                return (int) $setting->value;
                break;
            default:
                return $setting->value;
        }
    }

    /**
     * Update an existing setting with a new value. Doesn't create
     * a new setting
     * @param $key
     * @param $value
     * @return null
     */
    public function store($key, $value)
    {
        $setting = $this->findWhere(['key' => $key], ['id'])->first();
        if (!$setting) {
            return null;
        }

        try {
            $this->update(['value' => $value], $setting->id);
        } catch (ValidatorException $e) {
            Log::error($e->getMessage(), $e->getTrace());
        }

        return $value;
    }
}
