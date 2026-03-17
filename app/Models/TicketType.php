<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'name', 'description', 'price',
        'quantity', 'available', 'sale_start', 'sale_end', 'max_per_user',
    ];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'sale_start' => 'datetime', 'sale_end' => 'datetime'];
    }

    public function event() { return $this->belongsTo(Event::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }

    public function isOnSale(): bool
    {
        $now = now();
        return $now->between($this->sale_start, $this->sale_end) && $this->available > 0;
    }
}
