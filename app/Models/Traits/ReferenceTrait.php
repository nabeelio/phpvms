<?php

namespace App\Models\Traits;

/**
 * Trait ReferenceTrait
 *
 * @property \App\Contracts\Model $ref_model
 * @property mixed                $ref_model_id
 */
trait ReferenceTrait
{
    /**
     * @param \App\Contracts\Model $object
     *
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
     *
     * @return \App\Contracts\Model|$this|null
     */
    public function getReferencedObject()
    {
        if (!$this->ref_model || !$this->ref_model_id) {
            return;
        }

        if ($this->ref_model === __CLASS__) {
            return $this;
        }

        try {
            $klass = new $this->ref_model();
            $obj = $klass->find($this->ref_model_id);
            return $obj;
        } catch (\Exception $e) {
            return;
        }
    }
}
