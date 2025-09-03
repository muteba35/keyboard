<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dossier extends Model
{
    protected $fillable = [
        'nom',
        'user_id',
        'description',
    ];

    public function identifiants()
    {
         return $this->belongsTo(identifiants::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}