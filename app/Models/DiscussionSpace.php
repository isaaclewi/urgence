<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionSpace extends Model
{
    use HasFactory;

    protected $table = 'discussion_spaces';

    protected $fillable = [
        'title',
        'description',
        'type',               // public (ouvert aux citoyens) | prive (membres seulement)
        'is_active',          // false = groupe archivé (lecture seule, admin uniquement)
        'created_by',
        'service_id',         // service responsable (devient modérateur)
        'citizens_can_post',
        'moderators_can_post',
        'agents_can_post',
        'services_can_post',  // les services membres peuvent-ils écrire ?
        'join_policy',        // invite | request
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'citizens_can_post'   => 'boolean',
        'moderators_can_post' => 'boolean',
        'agents_can_post'     => 'boolean',
        'services_can_post'   => 'boolean',
    ];

    /** Agent admin créateur du groupe. */
    public function creator()
    {
        return $this->belongsTo(Admins::class, 'created_by');
    }

    /** Service responsable. */
    public function service()
    {
        return $this->belongsTo(Services::class);
    }

    public function members()
    {
        return $this->hasMany(DiscussionMember::class, 'discussion_space_id');
    }

    public function messages()
    {
        return $this->hasMany(DiscussionMessage::class, 'discussion_space_id');
    }
}
