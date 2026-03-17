<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'ticket_type_id', 'order_item_id',
        'ticket_code', 'qr_code_payload', 'status', 'checked_in_at',
    ];

    protected function casts(): array
    {
        return ['status' => TicketStatus::class, 'checked_in_at' => 'datetime'];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function participant()
    {
        return $this->hasOne(Participant::class);
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }
}
