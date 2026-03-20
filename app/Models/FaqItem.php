<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'question',
    'answer',
    'sort_order',
])]
class FaqItem extends Model {}
