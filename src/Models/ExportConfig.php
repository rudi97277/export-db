<?php

namespace Rudi97277\ExportDb\Models;

use Illuminate\Database\Eloquent\Model;

class ExportConfig extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'headers' => 'array',
        'formatter' => 'array',
        'validator' => 'array',
        'default' => 'array'
    ];
}
