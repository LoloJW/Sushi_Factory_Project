<?php

namespace App\Repository;

use App\Entity\ReservationRoom;
use App\Entity\Rooms;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReservationRoom>
 */
class ReservationRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationRoom::class);
    }

    public function findConflict(Rooms $room, \DateTime $timeStart, \DateTime $timeEnd, \DateTime $date): ?ReservationRoom
    {
        return $this->createQueryBuilder('reserve')
            ->where('reserve.room = :room')
            ->andWhere('reserve.reservedFor = :date')
            ->andWhere('reserve.timeStart < :timeEnd')
            ->andWhere('reserve.timeEnd > :timeStart')
            ->setParameter('room', $room)
            ->setParameter('date', $date)
            ->setParameter('timeStart', $timeStart)
            ->setParameter('timeEnd', $timeEnd)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByDate(\DateTime $date): array
    {
        return $this->createQueryBuilder('reserve')
            ->where('reserve.reservedFor = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
    //    public function findOneBySomeField($value): ?ReservationRoom
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
