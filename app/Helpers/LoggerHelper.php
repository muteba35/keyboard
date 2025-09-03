<?php

namespace App\Helpers;

use App\Models\user_logs;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class LoggerHelper
{
    /**
     * Enregistre une action dans les logs.
     *
     * @param string $action Action exécutée (ex: 'ajout', 'modification', 'suppression')
     * @param string|null $description Détails de l’action (ex: identifiant modifié)
     * @param string|null $related_type Type d’élément concerné (ex: 'Identifiants')
     * @param int|null $related_id ID de l’élément concerné
     */
    public static function logAction(string $action, ?string $description = null, ?string $related_type = null, ?int $related_id = null)
    {
        $user = Auth::user();
        if (!$user) return;

        user_logs::create([
            'user_id'      => $user->id,
            'action'       => $action,
            'description'  => $description,
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::header('User-Agent'),
            'related_type' => $related_type,
            'related_id'   => $related_id,
        ]);
    }
}