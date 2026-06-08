<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscussionSpace extends Model
{
    use HasFactory;

    protected $table = 'discussion_spaces';

    /**
     * Champs autorisés à l’insertion
     */
    protected $fillable = [
        'title',
        'description',
        'type',
        'is_active',
        'created_by',
        'service_id',
        'citizens_can_post',
        'moderators_can_post',
        'agents_can_post',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'citizens_can_post' => 'boolean',
        'moderators_can_post' => 'boolean',
        'agents_can_post' => 'boolean',
    ];


    /**
     * Agent créateur de l’espace (admin)
     */
    public function creator()
    {
        return $this->belongsTo(Admins::class, 'created_by');
    }

    /**
     * Service responsable / modérateur
     */
    public function service()
    {
        return $this->belongsTo(Services::class);
    }
}
