<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Organizations extends Model
{
    use CrudTrait;
    use HasApiTokens, HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'organizations';
    protected $primaryKey = 'id';
    // public $timestamps = false;

    protected $fillable = [

        'phoneNumber',
        'email',
        'name',
        'address',
        'latitude',
        'longitude',
        'logo',
        'website',
        'location'

    ];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
        'password' => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function user(): hasMany
    {
        return $this->hasMany(User::class, 'organizationId', 'id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donations::class);
    }

    public function camps(): HasMany
    {
        return $this->hasMany(Camps::class);
    }

    protected function location(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return json_encode([
                    'lat' => $attributes['latitude'],
                    'lng' => $attributes['longitude'],
                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
            },
            set: function ($value) {
                $location = json_decode($value);
                return [
                    'lat' => $location->lat,
                    'lng' => $location->lng,
                ];
            }
        );
    }

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
