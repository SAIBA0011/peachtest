<?php

namespace App;

use App\Card;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'primary_card'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function primaryCard()
    {
        return $this->hasOne(Card::class, 'id', 'primary_card');
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }
}
