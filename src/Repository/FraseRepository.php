<?php

namespace App\Repository;

use App\Entity\Frase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class FraseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Frase::class);
    }

    public function findOneForMostra(int $id): ?Frase
    {
        return $this->createQueryBuilder('f')
            ->addSelect('c', 'd', 'e', 't', 'te')
            ->innerJoin('f.contesto', 'c')
            ->innerJoin('f.direzione', 'd')
            ->innerJoin('f.espressione', 'e')
            ->leftJoin('f.traduzioni', 't')
            ->leftJoin('t.espressione', 'te')
            ->andWhere('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findFirstIdByContestoAndDirezione(int $contestoId, int $direzioneId): ?int
    {
        $res = $this->createQueryBuilder('f')
            ->select('f.id')
            ->andWhere('f.contesto = :cid')
            ->andWhere('f.direzione = :did')
            ->setParameter('cid', $contestoId)
            ->setParameter('did', $direzioneId)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $res ? (int)$res['id'] : null;
    }

    public function findPrevNextIds(int $currentId, int $contestoId, int $direzioneId): array
    {
        // prev
        $prev = $this->createQueryBuilder('f')
            ->select('f.id')
            ->andWhere('f.contesto = :cid')
            ->andWhere('f.direzione = :did')
            ->andWhere('f.id < :cur')
            ->setParameter('cid', $contestoId)
            ->setParameter('did', $direzioneId)
            ->setParameter('cur', $currentId)
            ->orderBy('f.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        // next
        $next = $this->createQueryBuilder('f')
            ->select('f.id')
            ->andWhere('f.contesto = :cid')
            ->andWhere('f.direzione = :did')
            ->andWhere('f.id > :cur')
            ->setParameter('cid', $contestoId)
            ->setParameter('did', $direzioneId)
            ->setParameter('cur', $currentId)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return [
            'prev' => $prev ? (int)$prev['id'] : null,
            'next' => $next ? (int)$next['id'] : null,
        ];
    }

    /**
     * @return Frase[]
     */
    public function findAllForItenList(): array
    {
        return $this->createQueryBuilder('f')
            ->addSelect('c', 'l', 'd', 'e', 't', 'te')
            ->innerJoin('f.contesto', 'c')
            ->innerJoin('f.livello', 'l')
            ->innerJoin('f.direzione', 'd')
            ->innerJoin('f.espressione', 'e')
            ->leftJoin('f.traduzioni', 't')
            ->leftJoin('t.espressione', 'te')
            ->andWhere('d.descrizione = :desc')
            ->setParameter('desc', 'Italiano -> Inglese')
            ->orderBy('c.descrizione', 'ASC')
            ->addOrderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
