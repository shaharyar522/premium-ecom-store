<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUSES = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
        'returned' => 'Returned',
    ];

    /** Steps shown on the customer tracking timeline. */
    public const FLOW = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function events()
    {
        return $this->hasMany(OrderEvent::class)->orderBy('id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function setStatus(string $status, ?string $note = null): void
    {
        $this->update(['status' => $status] + ($status === 'delivered' && $this->payment_method === 'cod' ? ['payment_status' => 'paid'] : []));
        $this->events()->create(['status' => $status, 'note' => $note]);
    }
}
