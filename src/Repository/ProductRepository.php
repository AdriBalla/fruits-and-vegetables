<?php

namespace App\Repository;

use App\DTO\Enum\ProductType;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): Product
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $entity;
    }

    public function remove(ProductType $type, int $id): bool
    {
        return (bool) $this->createQueryBuilder('p')
            ->where('p.type = :type')
            ->andWhere('p.id=:id')
            ->setParameters(['type' => $type, 'id' => $id])
            ->setMaxResults(1)
            ->delete()
            ->getQuery()
            ->execute();
    }

    public function truncate(): void
    {
        $this->createQueryBuilder('p')
            ->delete()
            ->getQuery()
            ->execute();
    }

    /**
     * @return Product[]
     */
    public function list(ProductType $type, ?int $page = null, ?int $size = null): array
    {
        $page = $page ?? 0;
        $size = $size ?? 100;

        return $this->createQueryBuilder('p')
            ->where('p.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->setFirstResult($page * $size)
            ->setMaxResults($size)
            ->getResult();
    }
}
