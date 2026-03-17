<?php

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 'name', 'email', 'phone', 'document', 'birth_date', 'gender',
    ];

    protected function casts(): array
    {
        return ['birth_date' => 'date', 'gender' => Gender::class];
    }

    public function ticket() { return $this->belongsTo(Ticket::class); }
    public function fieldValues() { return $this->hasMany(ParticipantFieldValue::class); }
}
