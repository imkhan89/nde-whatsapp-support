<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'customer_id',
        'wa_message_id',
        'direction',
        'message',
        'is_read',
        'status',
    ];


    protected $casts = [
        'is_read' => 'boolean',
    ];


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}