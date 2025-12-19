<?php

namespace App\Enums;

enum ProductStatus: string
{
    case Active = 'active';
    case Sold = 'sold';
    case Archived = 'archived';     // Draft
    case Suspended = 'suspended';   // Admin Suspend

    public function label(): string
    {
        return match($this) {
            self::Active => 'Aktif',
            self::Sold => 'Terjual Habis',
            self::Archived => 'Draft',
            self::Suspended => 'Ditangguhkan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Active => 'green',
            self::Sold => 'gray',
            self::Archived => 'yellow',
            self::Suspended => 'red',
        };
    }
}
