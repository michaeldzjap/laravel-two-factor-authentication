<?php

namespace MichaelDzjap\TwoFactorAuth;

use App\User;
use Illuminate\Database\Eloquent\Model;

class TwoFactorAuth extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    /**
     * The primary key of the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Get the user that owns the two-factor auth.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
