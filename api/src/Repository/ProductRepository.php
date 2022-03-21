<?php

namespace App\Repository;

use App\Entity\Product;
use App\Service\CurrencyConversor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private CurrencyConversor $currencyConversor;

    public function __construct(ManagerRegistry $registry, CurrencyConversor $currencyConversor)
    {
        parent::__construct($registry, Product::class);
        $this->currencyConversor = $currencyConversor;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Product $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Product $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getFeaturedProducts(bool $featured = true): array
    {
        return $this
            ->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->select('p.id, p.name, p.price, p.currency, c.name category_name')
            ->where('p.featured = :featured')
            ->setParameter('featured', $featured)
            ->getQuery()
            ->getArrayResult();
    }

    public function getFeaturedProductsCurrencyConverted(string $to, bool $featured = true): array
    {
        $featuredProducts = $this->getFeaturedProducts($featured);
        for ($i = 0; $i < count($featuredProducts); $i++) {
            $product                = $featuredProducts[$i];
            $product['price']       = $this->currencyConversor->convert($product['price'], $product['currency'], $to);
            $product['currency']    = $to;
            $featuredProducts[$i]   = $product;
        }
        return $featuredProducts;
    }
}
