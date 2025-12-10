<?php

namespace App;

enum ProductStatus: string
{
    case Active = 'active';
    case Sold = 'sold';
    case Archived = 'archived';
    case Suspended = 'suspended';
}
