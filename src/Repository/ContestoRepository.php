<?php

namespace App\Repository;

use App\Entity\Contesto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ContestoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contesto::class);
    }

    /**
     * @return array<int, array{contesto: Contesto, frasiCount: int}>
     */
    public function findContestiWithFrasiCountByDirezione(int $direzioneId): array
    {
        // Ritorna array di righe: ['contesto' => Contesto, 'frasiCount' => 123]
        return $this->createQueryBuilder('c')
            ->select('c AS contesto, COUNT(f.id) AS frasiCount')
            ->innerJoin('App\Entity\Frase', 'f', 'WITH', 'f.contesto = c')
            ->andWhere('f.direzione = :dirId')
            ->setParameter('dirId', $direzioneId)
            ->groupBy('c.id')
            ->orderBy('c.descrizione', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
