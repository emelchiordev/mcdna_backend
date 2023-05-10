<?php

namespace App\Tests\Entity;

use DateTime;
use DateTimeImmutable;
use App\Entity\Category;
use App\Entity\Products;
use App\Entity\Promotions;
use PHPUnit\Framework\TestCase;
use App\Repository\ProductsRepository;

final class ProductsTest extends TestCase
{

    /*
    public function testAddProductOnDatabase()
    {
        $productsRepository = $this->createMock(ProductsRepository::class);

        $category = new Category();
        $category->setLibelle("Produits sucrés");

        $product = new Products();
        $product
            ->setLabel('Barre de chocolat')
            ->setPrice('150')
            ->setDescription("une barre de chocolat")
            ->setCategory($category);

            productsRepository->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $result = productsRepository->save($product);

        $this->assertTrue($result);
    }*/

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

    public function testDiscountPrice()
    {
        $promotion = new Promotions();
        $promotion
            ->setPercentage(20)
            ->setStartDate(new DateTimeImmutable('-1 day'))
            ->setEndDate(new DateTimeImmutable('+1 day'));

        $product = new Products();
        $product
            ->setPrice('100')
            ->addPromotion($promotion);

        $this->assertSame(80.0, $product->getDiscountPrice()[0]);
    }
}
