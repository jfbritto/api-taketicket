<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'user_id', 'name', 'slug', 'description', 'logo',
        'document', 'phone', 'address', 'city', 'state', 'postal_code',
        'asaas_account_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
