<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Camps extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'camps';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [

        'organizationId',
        'name',
        'address',
        'startDate',
        'endDate',
        'latitude',
        'longitude',
        'attendees',
        'pictures',
        'location'

    ];
    // protected $hidden = [];
    // protected $dates = [];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organizations::class, 'organizationId');
    }

    protected function location(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return json_encode([
                    'lat' => $attributes['latitude'],
                    'lng' => $attributes['longitude'],
                    'formatted_address' => $attributes['address'] ?? ''
                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
            },
            set: function ($value) {
                $location = json_decode($value);
                return [
                    'lat' => $location->lat,
                    'lng' => $location->lng,
                    'full_address' => $location->formatted_address ?? ''
                ];
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
