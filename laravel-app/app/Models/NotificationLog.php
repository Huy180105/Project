<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    public const TYPE_NEAR_TURN = 'near_turn';
    public const TYPE_CALLING = 'calling';
    public const TYPE_MISSED = 'missed';
    public const TYPE_COMPLETED = 'completed';

    public const CHANNEL_LOCAL = 'local';
    public const CHANNEL_SMS = 'sms';
    public const CHANNEL_ZALO = 'zalo';
    public const CHANNEL_N8N_WEBHOOK = 'n8n_webhook';

    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'queue_ticket_id',
        'patient_name',
        'patient_phone',
        'type',
        'channel',
        'title',
        'message',
        'status',
        'payload',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function queueTicket(): BelongsTo
    {
        return $this->belongsTo(QueueTicket::class);
    }
}
