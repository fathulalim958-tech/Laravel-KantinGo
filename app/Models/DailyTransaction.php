<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'canteen_id',
        'date',
        'payment_method',
        'total_revenue',
        'transaction_count',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_revenue' => 'integer',
            'transaction_count' => 'integer',
        ];
    }

    public function canteen(): BelongsTo
    {
        return $this->belongsTo(Canteen::class);
    }
}
