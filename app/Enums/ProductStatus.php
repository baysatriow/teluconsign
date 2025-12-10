<?php

namespace App\Enums;

enum ProductStatus: string
{
    case Active = 'active';
    case Sold = 'sold';
    case Archived = 'archived';
    case Suspended = 'suspended';
}
