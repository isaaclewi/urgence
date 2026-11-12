<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionMessage extends Model
{
    use HasFactory;

    protected $table = 'discussion_messages';

    protected $fillable = [
        'discussion_space_id',
        'sender_id',
        'sender_type',      // citoyen | agent (administration) | service
        'message_type',
        'message',
        'file_path',
        'file_name',
        'file_mime',
        'file_size',
        'is_deleted',
        'reply_to_id',
        'edited_at',
        'deleted_at',
        'deleted_by_type',
        'deleted_by_id',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'edited_at'  => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function space()
    {
        return $this->belongsTo(DiscussionSpace::class, 'discussion_space_id');
    }

    /** Message auquel celui-ci répond. */
    public function replyTo()
    {
        return $this->belongsTo(self::class, 'reply_to_id');
    }
}
