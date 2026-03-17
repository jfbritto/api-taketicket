<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantFieldValue extends Model
{
    use HasFactory;

    protected $fillable = ['participant_id', 'custom_field_id', 'value'];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function customField()
    {
        return $this->belongsTo(CustomField::class);
    }
}
