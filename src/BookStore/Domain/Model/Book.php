<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Model;

use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Discount;
use App\BookStore\Domain\ValueObject\Price;

class Book
{
    private BookId $id;

    public function __construct(
        private BookName $name,
        private BookDescription $description,
        private Author $author,
        private BookContent $content,
        private Price $price,
        ?BookId $id = null,
    ) {
        $this->id = $id ?? new BookId();
    }

    public function update(
        ?BookName $name = null,
        ?BookDescription $description = null,
        ?Author $author = null,
        ?BookContent $content = null,
        ?Price $price = null,
    ): void {
        $this->name = $name ?? $this->name;
        $this->description = $description ?? $this->description;
        $this->author = $author ?? $this->author;
        $this->content = $content ?? $this->content;
        $this->price = $price ?? $this->price;
    }

    public function applyDiscount(Discount $discount): static
    {
        $this->price = $this->price->applyDiscount($discount);

        return $this;
    }

    public function id(): BookId
    {
        return $this->id;
    }

    public function name(): BookName
    {
        return $this->name;
    }

    public function description(): BookDescription
    {
        return $this->description;
    }

    public function author(): Author
    {
        return $this->author;
    }

    public function content(): BookContent
    {
        return $this->content;
    }

    public function price(): Price
    {
        return $this->price;
    }
}
