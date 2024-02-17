<?php

namespace App\Repository;

use App\Entity\Nasa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Nasa>
 *
 * @method Nasa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nasa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Nasa[]    findAll()
 * @method Nasa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NasaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nasa::class);
    }

//    /**
//     * @return Nasa[] Returns an array of Nasa objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Nasa
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
