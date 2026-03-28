<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case UNDER_REVIEW = 'under_review';
    case BLOCKED = 'blocked';
}