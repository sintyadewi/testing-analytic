<?php

namespace App\Models;

use App\Enums\AnalyticTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'total',
        'daily_date',
        'weekly_date',
        'monthly_date'
    ];

    protected $casts = [
        'type' => AnalyticTypeEnum::class,
    ];
}
