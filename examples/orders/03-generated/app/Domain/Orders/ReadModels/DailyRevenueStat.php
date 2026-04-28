<?php

namespace App\Domain\Orders\ReadModels;

use Illuminate\Database\Eloquent\Model;

class DailyRevenueStat extends Model
{
    protected $table = 'daily_revenue_stats';

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'total_revenue_in_cents' => 'integer',
        'order_count' => 'integer',
        'line_item_count' => 'integer',
    ];
}
