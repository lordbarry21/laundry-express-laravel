<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    //

    protected $table = 'order_details';

    protected $guarded = ['id'];

    public function getSubtotalAttribute(): int
    {
        return (int) (($this->price ?? 0) * ($this->quantity ?? 1));
    }

    public function laundryItem()
    {
        return $this->belongsTo(LaundryItem::class, 'laundry_item_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
