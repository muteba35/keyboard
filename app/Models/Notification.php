<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    

    protected $fillable = [
        'user_id',
        'identifiant_id',
        'type',
        'message',
        'est_lu',
    ];

    /**
     * L'utilisateur qui a reçu la notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * L'identifiant concerné par cette notification.
     */
    public function identifiants()
    {
        return $this->belongsTo(Identifiants::class);
    }
}