<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchConsoleMetric extends Model
{
    protected $fillable = [
        'site_url',
        'metric_date',
        'search_type',
        'dimension_type',
        'page',
        'query',
        'country',
        'device',
        'dimension_key_hash',
        'clicks',
        'impressions',
        'ctr',
        'position',
        'synced_at',
    ];

    protected $casts = [
        'metric_date' => 'date',
        'clicks' => 'integer',
        'impressions' => 'integer',
        'ctr' => 'float',
        'position' => 'float',
        'synced_at' => 'datetime',
    ];
}
