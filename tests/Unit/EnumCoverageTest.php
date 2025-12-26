<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Enums\ProductCondition;
use App\Enums\ProductStatus;

class EnumCoverageTest extends TestCase
{
    public function test_product_condition_enum()
    {
        $this->assertEquals('new', ProductCondition::New->value);
        $this->assertEquals('used', ProductCondition::Used->value);
        
        $cases = ProductCondition::cases();
        $this->assertCount(2, $cases);
    }

    public function test_product_status_enum_label()
    {
        $this->assertEquals('Aktif', ProductStatus::Active->label());
        $this->assertEquals('Terjual Habis', ProductStatus::Sold->label());
        $this->assertEquals('Draft', ProductStatus::Archived->label());
        $this->assertEquals('Ditangguhkan', ProductStatus::Suspended->label());
    }

    public function test_product_status_enum_color()
    {
        $this->assertEquals('green', ProductStatus::Active->color());
        $this->assertEquals('gray', ProductStatus::Sold->color());
        $this->assertEquals('yellow', ProductStatus::Archived->color());
        $this->assertEquals('red', ProductStatus::Suspended->color());
    }
}
