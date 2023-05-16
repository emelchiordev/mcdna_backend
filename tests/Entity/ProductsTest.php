<?php

namespace App\Tests\Entity;

use DateTime;
use DateTimeImmutable;
use App\Entity\Category;
use App\Entity\Products;
use App\Entity\Promotions;
use PHPUnit\Framework\TestCase;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\File\File;

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
    public function testGetDescription(): void
    {
        $product = new Products();
        $product->setDescription('Product Description');

        $this->assertSame('Product Description', $product->getDescription());
    }

    public function testSetDescription(): void
    {
        $product = new Products();
        $product->setDescription('Initial Description');

        $this->assertSame('Initial Description', $product->getDescription());

        $product->setDescription('Updated Description');
        $this->assertSame('Updated Description', $product->getDescription());
    }

    public function testGetCreateAt(): void
    {
        $product = new Products();
        $product->setCreateAt(new DateTimeImmutable("now"));
        $createdAt = $product->getCreateAt();

        $this->assertInstanceOf(\DateTimeImmutable::class, $createdAt);
    }

    public function testSetCreateAt(): void
    {
        $product = new Products();
        $product->setCreateAt();

        $createdAt = $product->getCreateAt();

        $this->assertInstanceOf(\DateTimeImmutable::class, $createdAt);
    }
    public function testGetImageFile(): void
    {
        $product = new Products();
        $imageFile = $product->getImageFile();

        $this->assertNull($imageFile);
    }

    public function testSetImageFile(): void
    {
        $product = new Products();
        $file = new File('./src/images/products/images.jpg');
        $product->setImageFile($file);

        $imageFile = $product->getImageFile();

        $this->assertInstanceOf(File::class, $imageFile);
    }

    public function testGetImageName(): void
    {
        $product = new Products();
        $imageName = $product->getImageName();

        $this->assertNull($imageName);
    }

    public function testSetImageName(): void
    {
        $product = new Products();
        $imageName = 'image.jpg';
        $product->setImageName($imageName);

        $updatedImageName = $product->getImageName();

        $this->assertSame($imageName, $updatedImageName);
    }
}
