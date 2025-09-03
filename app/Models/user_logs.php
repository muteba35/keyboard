<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class user_logs extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'related_id',
        'related_type',
    ];
    
    //L’utilisateur qui a déclenché le log.

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     //Élément lié au log (polymorphique : Identifiant, Dossier, etc.)
    public function related()
    {
        return $this->morphTo(null, 'related_type', 'related_id');
    }
}
