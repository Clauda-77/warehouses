<?php

namespace App\Enums;

enum ItemType: string
{
    case NEW = 'new';
    case USED = 'used';
    case ADJUSTMENT = 'adjustment';
 
    public function label(): string
    {
        return match($this) {
            self::NEW        => 'جديد',
            self::USED       => 'قديم',
            self::ADJUSTMENT => 'منسق',
        };
    }

 
    public function isNew(): bool
    {
        return $this === self::NEW;
    }

     
    public function isUsed(): bool
    {
        return $this === self::USED;
    }

  
    public function isAdjustment(): bool
    {
        return $this === self::ADJUSTMENT;
    }
}