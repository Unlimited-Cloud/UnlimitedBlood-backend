<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Donations extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    public $incrementing = false;

    // public $timestamps = false;
    protected $table = 'donations';
    protected $fillable = [

        'phoneNumber',
        'bloodType',
        'donationType',
        'quantity',
        'donationDate',
        'organizationId',
        'campId',
        'upperBP',
        'lowerBP',
        'weight',
        'notes',

    ];
    protected $primaryKey = 'phoneNumber';
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
        'donationDate' => 'date:Y-m-d',
    ];

    public function camp(): HasOne
    {

        return $this->hasOne(Camps::class);

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
