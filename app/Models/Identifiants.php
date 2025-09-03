<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Identifiants extends Model
{
    use HasFactory;

    protected $table = 'identifiants';

    protected $fillable = [
        'nom_utilisateur',
        'email',
        'mot_de_passe',
        'service',
        'description',
        'url_service',
        'statut',
        'user_id',
        'dossier_id',

    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relation avec le dossier
     public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

      public function notification()
    {
        return $this->hasMany(Notification::class,'identifiants_id');
    }
}