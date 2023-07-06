<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
        'donationDate' => 'date:Y-m-d',
    ];

    public function organizations(): BelongsTo
    {
        return $this->belongsTo(Organizations::class);
    }

    public function camp(): belongsTo
    {
        return $this->belongsTo(Camps::class, 'campId', 'id');
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
