<?php

namespace App\Domain\Orders\ReadModels;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'line_items' => 'array',
        'total_in_cents' => 'integer',
    ];
}
