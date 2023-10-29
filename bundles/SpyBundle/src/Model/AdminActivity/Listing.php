<?php

namespace  SpyBundle\Model\AdminActivity;

use Pimcore\Model;
use Pimcore\Model\Paginator\PaginateListingInterface;

class Listing extends Model\Listing\AbstractListing implements PaginateListingInterface
{
    /**
     * List of Votes.
     */
    public ?array $data = null;

    public ?string $locale = null;

    public static function create(): Listing
    {
        return new self();
    }

    /**
     * get total count.
     */
    public function count(): int
    {
        return $this->getTotalCount();
    }

    /**
     * get all items.
     */
    public function getItems(int $offset, int $itemCountPerPage): array
    {
        $this->setOffset($offset);
        $this->setLimit($itemCountPerPage);

        return $this->load();
    }

    /**
     * Get Paginator Adapter.
     *
     * @return $this
     */
    public function getPaginatorAdapter(): static
    {
        return $this;
    }

    /**
     * Set Locale.
     */
    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * Get Locale.
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * Methods for Iterator.
     */

    /**
     * Rewind.
     */
    public function rewind(): void
    {
        $this->getData();
        reset($this->data);
    }

    /**
     * current.
     */
    public function current(): mixed
    {
        $this->getData();

        return current($this->data);
    }

    /**
     * key.
     */
    public function key(): int|null|string
    {
        $this->getData();

        return key($this->data);
    }

    /**
     * next.
     */
    public function next(): void
    {
        $this->getData();
        next($this->data);
    }

    /**
     * valid.
     */
    public function valid(): bool
    {
        $this->getData();

        return $this->current() !== false;
    }
}
