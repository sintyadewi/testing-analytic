<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Database\Factories\OrderFactory;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'total',
        'status'
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'ORD-' . Str::random(32); // Example order number format
        });
    }

    protected static function newFactory(): Factory
    {
        return OrderFactory::new();
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(Analytic::class);
    }

    public function whereStatus(OrderStatusEnum $status): Builder
    {
        return $this->where('status', $status);
    }
}
