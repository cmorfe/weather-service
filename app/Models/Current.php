<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Current extends Model
{

  use HasFactory;

  protected $primaryKey = 'query';

  protected $keyType = 'string';

  protected $fillable
    = [
      'query',
      'weather_info',
    ];

  protected static function booted()
  {
    static::addGlobalScope('recent', function (Builder $builder) {
      $builder->whereDate('updated_at', '>', now()->subHour());
    });
  }

}
