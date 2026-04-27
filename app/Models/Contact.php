<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'ip_address',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Scope pour les messages non lus
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Marquer comme lu
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
            'status' => 'read',
        ]);
    }
}