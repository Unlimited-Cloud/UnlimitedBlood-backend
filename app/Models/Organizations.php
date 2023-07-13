<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
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
