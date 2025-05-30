<?php

namespace App\Models\Region;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{
    use HasUuids, SoftDeletes;
    protected $guarded = ['id'];
}
