<?php

namespace App\Models\Traits;

/**
 * Trait ReferenceTrait
 * @property \App\Interfaces\Model $ref_model
 * @property mixed $ref_model_id
 * @package App\Models\Traits
 */
trait ReferenceTrait
{
    /**
     * @param \App\Interfaces\Model $object
     * @return self
     */
    public function referencesObject($object)
    {
        $this->ref_model = \get_class($object);
        $this->ref_model_id = $object->id;
        $this->save();

        return $this;
    }

    /**
     * Return an instance of the object or null
     * @return \App\Interfaces\Model|null
     */
    public function getReferencedObject()
    {
        if ($classname = $this->ref_model) {
            $klass = new $this->ref_model;

            return $klass->find($this->ref_model_id);
        }

        return null;
    }
}
