<?php
namespace App\Enums;
enum StatusBayar: string
{
    case PENDING = 'pending';
    case DP_PAID = 'dp_paid';
    case PAID    = 'paid';
}
