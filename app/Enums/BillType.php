<?php

namespace App\Enums;

enum BillType: string
{
    case PURCHASE = 'purchase';
    case TRANSFER = 'transfer';
    case ADJUSTMENT = 'adjustment';
    case RETURN = 'return';


    public function label(): string
    {
        return match ($this) {
            self::PURCHASE => 'استلام', //مع سعر 

            self::ADJUSTMENT => 'تسليم', // بلا سعر 
            self::TRANSFER => 'تركيب  وتنسيق',   // مع سعر 
            self::RETURN => 'إدخال', // بلا سعر 

        };
    }

    public function isIncoming(): bool
    {
        return in_array($this, [self::RETURN, self::PURCHASE]);
    }

    public function isOutgoing(): bool
    {
        return in_array($this, [self::ADJUSTMENT, self::TRANSFER]);
    }
}
