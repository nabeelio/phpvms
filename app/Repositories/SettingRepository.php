<?php

namespace App\Repositories;

use Illuminate\Support\Carbon;
use Prettus\Repository\Contracts\CacheableInterface;

use App\Models\Setting;
use App\Repositories\Traits\CacheableRepository;

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

    public function store($key, $value)
    {
        $setting = $this->findWhere(['key' => $key], ['id'])->first();
        if (!$setting) {
            return null;
        }

        $this->update(['value' => $value], $setting->id);
    }
}
