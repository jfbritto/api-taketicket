<?php

namespace App\Models;

use App\Enums\CustomFieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'label', 'type', 'required', 'options', 'position'];

    protected function casts(): array
    {
        return ['type' => CustomFieldType::class, 'required' => 'boolean', 'options' => 'array'];
    }

    public function event() { return $this->belongsTo(Event::class); }
    public function values() { return $this->hasMany(ParticipantFieldValue::class); }
    public function hasValues(): bool { return $this->values()->exists(); }
}
