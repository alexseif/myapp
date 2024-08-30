<?php

namespace App\Util;

use Doctrine\ORM\QueryBuilder;

/**
 * Class Paginator.
 */
class Paginator
{
    /**
     * @var string string
     */
    protected $path;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var int current page
     */
    protected $page = 0;

    /**
     * @var int total number of pages
     */
    protected $pages = 0;

    /**
     * @var int
     */
    protected $limit = 100;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $total = 0;

    /**
     * Paginator constructor.
     *
     * @param string $path
     * @param $queryBuilder
     * @param int $page
     * @param int $offset
     */
    public function __construct($path, QueryBuilder $queryBuilder, int $limit, $page = 0)
    {
        $this->path = $path;
        $this->page = $page;
        $this->limit = $limit;
        $this->queryBuilder = $queryBuilder;

        $this->setOffset();
        $this->setTotal();
        $this->setPages();
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): Paginator
    {
        $this->page = $page;

        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): Paginator
    {
        $this->limit = $limit;

        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    protected function setOffset(): Paginator
    {
        $this->offset = $this->getPage() * $this->getLimit();

        return $this;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * @param mixed $queryBuilder
     *
     * @return Paginator
     */
    public function setQueryBuilder($queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    /**
     * @return int
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @return Paginator
     */
    protected function setPages()
    {
        $this->pages = ceil($this->getTotal() / $this->getLimit());

        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    protected function setTotal(): Paginator
    {
        $this->total = $this->getQueryBuilder()
            ->select('COUNT(1)')
            ->getQuery()
            ->getSingleScalarResult();

        return $this;
    }

    /**
     * @return int
     */
    public function getNextPage()
    {
        return $this->getPage() + 1;
    }

    /**
     * @return int
     */
    public function getPrevPage()
    {
        return $this->getPage() - 1;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): Paginator
    {
        $this->path = $path;

        return $this;
    }
}
