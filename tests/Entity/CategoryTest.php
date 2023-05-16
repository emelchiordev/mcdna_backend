<?php

use App\Entity\Category;
use App\Entity\Products;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;


class CategoryTest extends TestCase
{
    public function testGetId(): void
    {
        $category = new Category();
        $this->assertNull($category->getId());
    }

    public function testGetSetLibelle(): void
    {
        $category = new Category();
        $libelle = 'Category 1';

        $category->setLibelle($libelle);
        $this->assertEquals($libelle, $category->getLibelle());
    }

    public function testGetProducts(): void
    {
        $category = new Category();
        $this->assertInstanceOf(Collection::class, $category->getProducts());
    }

    public function testAddRemoveProduct(): void
    {
        $category = new Category();
        $product = new Products();

        $category->addProduct($product);
        $this->assertTrue($category->getProducts()->contains($product));
        $this->assertSame($category, $product->getCategory());

        $category->removeProduct($product);
        $this->assertFalse($category->getProducts()->contains($product));
        $this->assertNull($product->getCategory());
    }
}
