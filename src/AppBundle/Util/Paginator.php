<?php


namespace AppBundle\Util;


use Doctrine\ORM\QueryBuilder;

/**
 * Class Paginator
 * @package AppBundle\Util
 */
class Paginator
{
    /**
     * @var string $path string
     */
    protected $path;

    /**
     * @var QueryBuilder $queryBuilder
     */
    protected $queryBuilder;

    /**
     * @var int $page current page
     */
    protected $page = 0;

    /**
     * @var int $pages total number of pages
     */
    protected $pages = 0;

    /**
     * @var int $limit
     */
    protected $limit = 100;

    /**
     * @var int $offset
     */
    protected $offset = 0;

    /**
     * @var int $total
     */
    protected $total = 0;

    /**
     * Paginator constructor.
     * @param string $path
     * @param $queryBuilder
     * @param int $page
     * @param int $limit
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


    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     * @return Paginator
     */
    public function setPage(int $page): Paginator
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return Paginator
     */
    public function setLimit(int $limit): Paginator
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return Paginator
     */
    protected function setOffset(): Paginator
    {

        $this->offset = $this->getPage() * $this->getLimit();
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * @param mixed $queryBuilder
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

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return Paginator
     */
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

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Paginator
     */
    public function setPath(string $path): Paginator
    {
        $this->path = $path;
        return $this;
    }


}