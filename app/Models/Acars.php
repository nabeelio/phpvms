<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Casts\DistanceCast;
use App\Models\Casts\FuelCast;
use App\Models\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string id
 * @property string pirep_id
 * @property int    type
 * @property string name
 * @property float  lat
 * @property float  lon
 * @property float  altitude
 * @property int    gs
 * @property int    ias
 * @property int    heading
 * @property int    order
 * @property int    nav_type
 */
class Acars extends Model
{
    use HashIdTrait;
    use HasFactory;

    public $table = 'acars';

    protected $keyType = 'string';

    public $fillable = [
        'id',
        'pirep_id',
        'type',
        'nav_type',
        'order',
        'name',
        'status',
        'log',
        'lat',
        'lon',
        'distance',
        'heading',
        'altitude',
        'altitude_agl',
        'altitude_msl',
        'vs',
        'gs',
        'ias',
        'transponder',
        'autopilot',
        'fuel_flow',
        'sim_time',
        'created_at',
        'updated_at',
    ];

    public $incrementing = false;

    public $casts = [
        'type'         => 'integer',
        'order'        => 'integer',
        'nav_type'     => 'integer',
        'lat'          => 'float',
        'lon'          => 'float',
        'distance'     => DistanceCast::class,
        'heading'      => 'integer',
        'altitude'     => 'float',
        'altitude_agl' => 'float',
        'altitude_msl' => 'float',
        'vs'           => 'float',
        'gs'           => 'integer',
        'ias'          => 'integer',
        'transponder'  => 'integer',
        'fuel'         => FuelCast::class,
        'fuel_flow'    => 'float',
    ];

    public static $rules = [
        'pirep_id' => 'required',
    ];

    /**
     * Relationships
     */
    public function pirep(): BelongsTo
    {
        return $this->belongsTo(Pirep::class, 'pirep_id');
    }
}
