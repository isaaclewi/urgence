<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscussionMessage extends Model
{
    use HasFactory;

    protected $table = 'discussion_messages';

    /**
     * Champs autorisés à l’insertion
     */
    protected $fillable = [
        'discussion_space_id',
        'sender_id',
        'sender_type',
        'message_type',
        'message',
        'file_path',
        'file_name',
        'file_mime',
        'file_size',
        'is_deleted',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    /**
     * Relation : espace de discussion
     */
    public function space()
    {
        return $this->belongsTo(DiscussionSpace::class, 'discussion_space_id');
    }

    /**
     * Auteur du message (polymorphique simple)
     */
    public function sender()
    {
        return match ($this->sender_type) {
            'citoyen' => Citoyens::find($this->sender_id),
            'agent'   => Admins::find($this->sender_id),
            'service' => Services::find($this->sender_id),
            default   => null,
        };
    }

    public function admin()
    {
        return $this->belongsTo(Admins::class, 'sender_id');
    }

    public function senderName()
    {
        return $this->admin?->name ?? 'Admin';
    }

    public function senderAvatar()
    {
        return '🛡'; // ou autre emoji/icone que tu veux
    }


    public function service()
    {
        return $this->belongsTo(Services::class, 'sender_id');
    }
    public function serviceName()
    {
        return $this->service?->name ?? 'Service';
    }
    public function serviceAvatar()
    {
        return '🏥'; // ou autre emoji/icone que tu veux
    }

    public function citoyen()
    {
        return $this->belongsTo(Citoyens::class, 'sender_id');
    }
    public function citoyenName()
    {
        return $this->citoyen?->name ?? 'Citoyen';
    }
    public function citoyenAvatar()
    {
        return '👤'; // ou autre emoji/icone que tu veux
    }
}
