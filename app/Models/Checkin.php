<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'checked_by', 'device', 'checked_at'];

    protected function casts(): array
    {
        return ['checked_at' => 'datetime'];
    }

    public function ticket() { return $this->belongsTo(Ticket::class); }
    public function checkedByUser() { return $this->belongsTo(User::class, 'checked_by'); }
}
