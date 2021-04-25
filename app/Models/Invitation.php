<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_id',
        'to_id',
        'state',
    ];

    /**
     * Get the user that sent the invitation.
     */
    public function sentBy()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    /**
     * Get the user the invitation was sent for.
     */
    public function sentTo()
    {
        return $this->belongsTo(User::class, 'to_id');
    }
}
