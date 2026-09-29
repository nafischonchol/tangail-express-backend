<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
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

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'status' => OrderStatus::class,
        ];
    }

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
        if (! $this->image_list_path) {
            return null;
        }

        return Storage::url($this->image_list_path);
    }

    public function getVoiceUrlAttribute(): ?string
    {
        if (! $this->voice_list_path) {
            return null;
        }

        return Storage::url($this->voice_list_path);
    }
}
