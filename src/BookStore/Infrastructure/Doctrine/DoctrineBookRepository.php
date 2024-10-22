<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Doctrine;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Infrastructure\Doctrine\Entity\BookEntity;
use App\Shared\Infrastructure\Doctrine\DoctrineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends DoctrineRepository<Book>
 */
final class DoctrineBookRepository extends DoctrineRepository implements BookRepositoryInterface
{
    private const ENTITY_CLASS = BookEntity::class;
    private const ALIAS = 'book';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, self::ENTITY_CLASS, self::ALIAS);
    }

    public function add(Book $book): void
    {
        /** @var ?BookEntity */
        $entity = $this->em->find(self::ENTITY_CLASS, $book->id()->value);

        if (null !== $entity) {
            $entity->updateFromModel($book);
        } else {
            $entity = BookEntity::fromModel($book);
        }

        $this->em->persist($entity);
    }

    public function remove(Book $book): void
    {
        /** @var ?BookEntity */
        $entity = $this->em->find(self::ENTITY_CLASS, $book->id()->value);

        if (null !== $entity) {
            $this->em->remove($entity);
        }
    }

    public function ofId(BookId $id): ?Book
    {
        /** @var ?BookEntity */
        $entity = $this->em->find(self::ENTITY_CLASS, $id->value);

        return null !== $entity ? $entity->toModel() : null;
    }

    public function withAuthor(Author $author): static
    {
        return $this->filter(static function (QueryBuilder $qb) use ($author): void {
            $qb->where(sprintf('%s.author = :author', self::ALIAS))->setParameter('author', $author->value);
        });
    }

    public function withCheapestsFirst(): static
    {
        return $this->filter(static function (QueryBuilder $qb): void {
            $qb->orderBy(sprintf('%s.price', self::ALIAS), 'ASC');
        });
    }

    public function getIterator(): \Iterator
    {
        foreach (parent::getIterator() as $entity) {
            /** @var BookEntity $entity */
            yield $entity->toModel();
        }
    }
}
