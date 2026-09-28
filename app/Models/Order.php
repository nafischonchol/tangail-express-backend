<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'phone',
        'address',
        'raw_text_list',
        'image_list_path',
        'voice_list_path',
        'total_amount',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    protected $appends = [
        'image_url',
        'voice_url',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_list_path) {
            return null;
        }

        return Storage::disk('public')->url($this->image_list_path);
    }

    public function getVoiceUrlAttribute(): ?string
    {
        if (!$this->voice_list_path) {
            return null;
        }

        return Storage::disk('public')->url($this->voice_list_path);
    }
}
