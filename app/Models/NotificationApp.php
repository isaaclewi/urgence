<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationApp extends Model
{
    protected $table = 'notifications_app';

    protected $fillable = [
        'destinataire_type', 'destinataire_id', 'categorie', 'niveau',
        'titre', 'message', 'lien', 'lu_at',
    ];

    protected $casts = ['lu_at' => 'datetime'];
}
