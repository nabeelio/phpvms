<?php

namespace App\Models;

/**
 * The Award model
 * @property mixed id
 * @property mixed ref_class
 * @property mixed|null ref_class_params
 * @package Award\Models
 */
class Award extends BaseModel
{
    public $table = 'awards';

    public $fillable = [
        'name',
        'description',
        'image_url',
        'ref_class',
        'ref_class_params',
    ];

    public static $rules = [
        'name'        => 'required',
        'description'  => 'nullable',
        'image_url'    => 'nullable',

        'ref_class' => 'required',
        'ref_class_params' => 'nullable'
    ];

    /**
     * Get the referring object
     * @param Award|null $award
     * @param User|null $user
     * @return null
     */
    public function getReference(Award $award=null, User $user=null)
    {
        if (!$this->ref_class) {
            return null;
        }

        try {
            return new $this->ref_class($award, $user);
        } catch (\Exception $e) {
            return null;
        }
    }
}
