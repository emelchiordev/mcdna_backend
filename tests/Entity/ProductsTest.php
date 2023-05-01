<?php

namespace App\Tests\Entity;

use App\Entity\Products;
use PHPUnit\Framework\TestCase;

final class ProductsTest extends TestCase
{
    public function testGetLabel()
    {
        $product = new Products();
        $product->setLabel('Barre de chocolat');
        $this->assertSame('Barre de chocolat', $product->getLabel());
    }

    public function testGetPrice()
    {
        $product = new Products();
        $product->setPrice('45.99');
        $this->assertSame('45.99', $product->getPrice());
    }
}
