<?php

namespace App\Repository;

use App\Entity\Filter;
use App\Entity\Output;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<Output>
 *
 * @method Output|null find($id, $lockMode = null, $lockVersion = null)
 * @method Output|null findOneBy(array $criteria, array $orderBy = null)
 * @method Output[]    findAll()
 * @method Output[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutputRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Output::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Output $entity, bool $flush = true): void
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
    public function remove(Output $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findByFilter(Filter $filter, User $user): array
    {
        $cqb = $this->createQueryBuilder('o');
        $dateNow = new \DateTime("now");

        if ($filter->getCampus()) {
            $cqb->andWhere('o.campus = :campus')
                ->setParameter('campus', $filter->getCampus());
        };

        if ($filter->getSearch()) {
            $cqb->andWhere('o.name LIKE :search')
                ->setParameter('search', '%'.$filter->getSearch().'%');
        };

        if ($filter->getStartdatetime()) {
            $cqb->andWhere('o.startdatetime >= :startdate')
                ->setParameter('startdate', $filter->getStartdatetime());
        };

        if ($filter->getEnddatetime()) {
            $cqb->andWhere('o.startdatetime <= :enddate')
                ->setParameter('enddate', $filter->getEnddatetime());
        };

        if ($filter->getOrganize()) {
            $cqb->andWhere('o.organizer = :organizer')
                ->setParameter('organizer', $user);
        };

        if ($filter->getRegistered()) {
                $cqb->andWhere(':registered member of o.users')
                    ->setParameter('registered', $user);
            };

        if ($filter->getUnregistered()) {
            $cqb->andWhere(':unregistered not member of o.users')
                ->setParameter('unregistered', $user);
        };

        if ($filter->getPast()) {
            $cqb->andWhere('o.startdatetime < :datenow')
                ->setParameter('datenow', $dateNow);
        };


        return $cqb
            ->getQuery()
            ->getResult();
    }


    // /**
    //  * @return Output[] Returns an array of Output objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Output
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
