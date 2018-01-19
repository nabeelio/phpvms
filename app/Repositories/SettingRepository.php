<?php

namespace App\Repositories;

use Log;
use Illuminate\Support\Carbon;
use Prettus\Repository\Contracts\CacheableInterface;

use App\Models\Setting;
use App\Repositories\Traits\CacheableRepository;
use App\Exceptions\SettingNotFound;

use Prettus\Validator\Exceptions\ValidatorException;

class SettingRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    public $cacheMinutes = 1;

    public function model()
    {
        return Setting::class;
    }

    /**
     * Get a setting, reading it from the cache possibly
     * @param string $key
     * @return mixed
     * @throws SettingNotFound
     */
    public function retrieve($key)
    {
        $key = Setting::formatKey($key);
        $setting = $this->findWhere(['id' => $key], ['type', 'value'])->first();

        if(!$setting) {
            throw new SettingNotFound($key . ' not found');
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
            case 'number':
                return (int) $setting->value;
                break;
            case 'float':
                return (float) $setting->value;
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
        $key = Setting::formatKey($key);
        $setting = $this->findWhere(
            ['id' => $key],
            ['id', 'value'] # only get these columns
        )->first();

        if (!$setting) {
            return null;
        }

        try {
            if(\is_bool($value)) {
                $value = $value === true ? 1 : 0;
            }

            $this->update(['value' => $value], $setting->id);
        } catch (ValidatorException $e) {
            Log::error($e->getMessage(), $e->getTrace());
        }

        return $value;
    }
}
