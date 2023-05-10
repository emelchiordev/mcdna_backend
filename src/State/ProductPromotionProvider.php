<?php

namespace App\State;

use App\Entity\Products;
use Doctrine\ORM\EntityManager;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\State\Pagination\PaginatorInterface;
use App\Entity\Promotions;

class ProductPromotionProvider implements ProviderInterface
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            $repository = $this->entityManager->getRepository(Products::class);
            $queryBuilder = $repository->createQueryBuilder('p')
                ->join('p.promotions', 'promo')
                ->where('promo.startDate <= :now')
                ->andWhere('promo.endDate >= :now')
                ->setParameter('now', new \DateTime())
                ->getQuery();
            return $queryBuilder->getResult();
        }
    }
}
