<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionMember extends Model
{
    protected $table = 'discussion_members';

    protected $fillable = [
        'discussion_space_id', 'member_type', 'member_id', 'role',
        'can_post', 'status', 'last_read_message_id', 'invited_by',
    ];

    protected $casts = [
        'can_post' => 'boolean',
    ];

    public function space()
    {
        return $this->belongsTo(DiscussionSpace::class, 'discussion_space_id');
    }

    /** Le service lié (quand member_type = service). */
    public function service()
    {
        return $this->belongsTo(Services::class, 'member_id');
    }

    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }
}
