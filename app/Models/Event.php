<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'organizer_id', 'title', 'slug', 'description',
        'location', 'address', 'city', 'state',
        'start_date', 'end_date', 'banner', 'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => EventStatus::class,
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function slugSource(): string
    {
        return 'title';
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function customFields()
    {
        return $this->hasMany(CustomField::class)->orderBy('position');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
