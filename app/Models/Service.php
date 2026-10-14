<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'service_name',
        'category',
        'price_per_unit',
        'unit_type',
        'description',
    ];

    /**
     * Relasi ke OrderDetail (satu layanan bisa ada di banyak detail pesanan)
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'service_id');
    }
}
