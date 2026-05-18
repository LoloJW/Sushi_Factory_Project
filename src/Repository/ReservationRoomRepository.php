<?php

namespace App\Repository;

use App\Entity\ReservationRoom;
use App\Entity\Rooms;
use App\Entity\User;
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
            ->setmaxResults(1)
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

    public function findReservationsWhereUserIsInvited(User $user): array
    {
        return $this->createQueryBuilder('reserve')
            ->where('reserve.user = :user OR :user MEMBER OF reserve.userInvites')
            ->andWhere('reserve.reservedFor = :today')
            ->andWhere('reserve.timeEnd > :now')
            ->orderBy('reserve.timeStart', 'ASC')
            ->setMaxResults(3)
            ->setParameter('user', $user)
            ->setParameter('today', new \DateTimeImmutable('today'))
            ->setParameter('now', new \DateTimeImmutable())
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
