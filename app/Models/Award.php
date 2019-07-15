<?php

namespace App\Models;

use App\Contracts\Model;

/**
 * The Award model
 *
 * @property mixed      id
 * @property mixed      ref_model
 * @property mixed|null ref_model_params
 */
class Award extends Model
{
    public $table = 'awards';

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'ref_model',
        'ref_model_params',
    ];

    public static $rules = [
        'name'             => 'required',
        'description'      => 'nullable',
        'image_url'        => 'nullable',
        'ref_model'        => 'required',
        'ref_model_params' => 'nullable',
    ];

    /**
     * Get the referring object
     *
     * @param self      $award
     * @param User|null $user
     *
     * @return null
     */
    public function getReference(self $award = null, User $user = null)
    {
        if (!$this->ref_model) {
            return;
        }

        try {
            return new $this->ref_model($award, $user);
        } catch (\Exception $e) {
            return;
        }
    }
}
