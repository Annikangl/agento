<?php

namespace App\Models\User;

use App\Enums\User\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance_id',
        'amount',
        'type',
    ];

    protected $casts = [
        'type' => TransactionType::class,
    ];
}
