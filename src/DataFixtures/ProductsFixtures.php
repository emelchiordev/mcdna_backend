<?php

namespace App\DataFixtures;

use DateTimeImmutable;
use App\Entity\Category;
use App\Entity\Products;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpFoundation\File\File;

class ProductsFixtures extends Fixture
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $objectManager)
    {
        for ($i = 0; $i < 5; $i++) {
            $category = (new Category())->setLibelle("Category $i");
            $objectManager->persist($category);
        }
        $objectManager->flush();

        $category = $this->entityManager->getRepository(Category::class)->findAll();
        for ($i = 0; $i < 10; $i++) {
            $product = (new Products())
                ->setLabel("Product $i")
                ->setLabel("Product label number $i")
                ->setPrice('50')
                ->setImageName("image")
                ->setImageFile(new File("test", false))
                ->setCategory($category[0])
                ->setDescription("Description product number $i");
            $objectManager->persist($product);
        }
        $objectManager->flush();
    }
}
