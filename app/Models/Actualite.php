<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actualite extends Model
{
    use HasFactory;

    protected $table = 'actualites';

    protected $fillable = [
        'auteur_nom',
        'contenu',
        'date_publication',
        'source',
        'type_media',
        'url_media',
        'image',
        'agent_id',
        'service_id',
    ];

    /**
     * Une actualité peut appartenir à un agent (admin).
     */
    public function agent()
    {
        return $this->belongsTo(Admins::class, 'agent_id');
    }

    /**
     * Une actualité peut appartenir à un service.
     */
    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }
}
