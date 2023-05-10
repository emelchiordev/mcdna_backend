<?php

namespace App\State;

use App\Entity\Products;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\CollectionOperationInterface;

class ProductPromotionPaginatedProvider implements ProviderInterface
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
                ->setMaxResults(5)
                ->getQuery();
            return $queryBuilder->getResult();
        }
    }
}
