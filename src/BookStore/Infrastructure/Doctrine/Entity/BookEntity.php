<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Doctrine\Entity;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\AbstractUid;

#[ORM\Entity]
#[ORM\Table(name: 'book')]
class BookEntity
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid')]
        private readonly AbstractUid $id,
        #[ORM\Column(length: 255)]
        private string $name,
        #[ORM\Column(length: 1023)]
        private string $description,
        #[ORM\Column(length: 255)]
        private string $author,
        #[ORM\Column(length: 65535)]
        private string $content,
        #[ORM\Column(options: ['unsigned' => true])]
        private int $price,
    ) {
    }

    public static function fromModel(Book $model): self
    {
        return new self(
            id: $model->id()->value,
            name: $model->name()->value,
            description: $model->description()->value,
            author: $model->author()->value,
            content: $model->content()->value,
            price: $model->price()->amount,
        );
    }

    public function toModel(): Book
    {
        $book = new Book(
            id: new BookId($this->id),
            name: new BookName($this->name),
            description: new BookDescription($this->description),
            author: new Author($this->author),
            content: new BookContent($this->content),
            price: new Price($this->price),
        );

        return $book;
    }

    public function updateFromModel(Book $model): void
    {
        $this->name = $model->name()->value;
        $this->description = $model->description()->value;
        $this->author = $model->author()->value;
        $this->content = $model->content()->value;
        $this->price = $model->price()->amount;
    }
}
