<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function searchBar(string $name): array
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->where('b.title LIKE :name')
            ->orWhere('b.author LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('b.title', 'ASC')
            ->getQuery();

        return $queryBuilder->getResult();
    }

    public function findAvailableBooks(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT b
            FROM App\Entity\Book b
            JOIN App\Entity\Loan l
            WHERE b.loans IS EMPTY
            OR l.book = b.id
            AND l.isReturned = true
            AND l.dateEnd < CURRENT_TIMESTAMP()
        ");

        return $query->getResult();
    }

}
