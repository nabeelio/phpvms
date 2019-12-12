<?php

namespace Modules\Importer\Utils;

use App\Contracts\Service;
use Spatie\Valuestore\Valuestore;

class IdMapper extends Service
{
    private $valueStore;

    public function __construct()
    {
        $this->valueStore = Valuestore::make(storage_path('app/legacy_migration.json'));
    }

    /**
     * Create a new mapping between an old ID and the new one
     *
     * @param string $entity Name of the entity (e,g table)
     * @param string $old_id
     * @param string $new_id
     */
    public function addMapping($entity, $old_id, $new_id)
    {
        $key_name = $entity.'_'.$old_id;
        if (!$this->valueStore->has($key_name)) {
            $this->valueStore->put($key_name, $new_id);
        }
    }

    /**
     * Return the ID for a mapping
     *
     * @param $entity
     * @param $old_id
     *
     * @return bool
     */
    public function getMapping($entity, $old_id)
    {
        $key_name = $entity.'_'.$old_id;
        if (!$this->valueStore->has($key_name)) {
            return 0;
        }

        return $this->valueStore->get($key_name);
    }
}
