<?php

namespace App\Repository;

use App\Entity\Actor;
use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    //    /**
    //     * @return Movie[] Returns an array of Movie objects
    //     */
       public function findByExampleField($value): array
       {
            return $this->createQueryBuilder('m')
            ->where('m.title LIKE :query')
            ->setParameter('query', '%' . $value . '%')
            ->orderBy('m.id','DESC')
            ->getQuery()
            ->getResult()
            ;
       }

       public function findBySomeField($title, $genre): array
       {
            return $this->createQueryBuilder('m')
               ->andWhere('m.title LIKE :title')
               ->andWhere('m.genre = :genre')
               ->setParameter('title', '%' . $title . '%')
               ->setParameter('genre', $genre)
               ->getQuery()
               ->getResult()
           ;
       }

       public function findByActor(Actor $actor)
        {
            return $this->createQueryBuilder('m')
                ->innerJoin('m.actors', 'a')  // 'actors' est le nom de la relation dans Movie
                ->where('a = :actor')
                ->setParameter('actor', $actor)
                ->orderBy('m.id', 'DESC')
                ->getQuery()
                ->getResult();
        }
}
