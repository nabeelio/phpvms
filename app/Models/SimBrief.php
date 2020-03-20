<?php

namespace App\Models;

use App\Contracts\Model;
use Illuminate\Support\Collection;

/**
 * @property string      $id        The Simbrief OFP ID
 * @property int         $user_id   The user that generated this
 * @property string      $flight_id Optional, if attached to a flight, removed if attached to PIREP
 * @property string      $pirep_id  Optional, if attached to a PIREP, removed if attached to flight
 * @property string      $ofp_xml
 * @property string      $ofp_html
 * @property Collection  $images
 * @property Collection  $files
 * @property Flight      $flight
 * @property User        $user
 * @property SimBriefXML $xml
 * @property string      $acars_flightplan_url
 */
class SimBrief extends Model
{
    public $table = 'simbrief';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'flight_id',
        'pirep_id',
        'ofp_xml',
        'created_at',
        'updated_at',
    ];

    /** @var \App\Models\SimBriefXML Store a cached version of the XML object */
    private $xml_instance;

    /**
     * Return a SimpleXML object of the ofp_xml
     *
     * @return \App\Models\SimBriefXML|null
     */
    public function getXmlAttribute(): SimBriefXML
    {
        if (empty($this->attributes['ofp_xml'])) {
            return null;
        }

        if (!$this->xml_instance) {
            $this->xml_instance = simplexml_load_string(
                $this->attributes['ofp_xml'],
                SimBriefXML::class
            );
        }

        return $this->xml_instance;
    }

    /**
     * Get the URL to the ACARS flightplan. Use like
     * echo $simbrief->acars_flightplan_url
     *
     * @return string
     */
    public function getAcarsFlightplanUrlAttribute()
    {
        $xml = $this->getXmlAttribute();
        if (!empty($xml->fms_downloads->vma)) {
            return $xml->fms_downloads->directory.$xml->fms_downloads->vma->link;
        }

        return '';
    }

    /**
     * Returns a list of images
     */
    public function getImagesAttribute(): Collection
    {
        return $this->xml->getImages();
    }

    /**
     * Return all of the flight plans
     */
    public function getFilesAttribute(): Collection
    {
        return $this->xml->getFlightPlans();
    }

    /*
     * Relationships
     */

    public function flight()
    {
        if (!empty($this->attributes['flight_id'])) {
            return $this->belongsTo(Flight::class, 'flight_id');
        }

        if (!empty($this->attributes['pirep_id'])) {
            return $this->belongsTo(Pirep::class, 'pirep_id');
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
