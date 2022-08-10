<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Mapper;

use Closure;
use GetLaminas\Blog\BlogPost;
use GetLaminas\Blog\CreateBlogPostFromDataArray;
use Laminas\Paginator\Adapter\AdapterInterface;
use PDO;
use PDOStatement;
use RuntimeException;

use function array_map;
use function array_merge;

class PdoPaginator implements AdapterInterface
{
    use CreateBlogPostFromDataArray;

    /** @var int */
    protected $count;

    /** @var array<string, mixed> */
    protected $params;

    /** @var PDOStatement */
    protected $select;

    public function __construct(PDOStatement $select, PDOStatement $count, array $params = [])
    {
        $this->select = $select;
        $this->count  = $count;
        $this->params = $params;
    }

    /**
     * @param int $offset
     * @param int $itemCountPerPage
     * @return BlogPost[]
     */
    public function getItems($offset, $itemCountPerPage): array
    {
        $params = array_merge($this->params, [
            ':offset' => $offset,
            ':limit'  => $itemCountPerPage,
        ]);

        $result = $this->select->execute($params);

        if (! $result) {
            throw new RuntimeException('Failed to fetch items from database');
        }

        return array_map(
            Closure::fromCallable([$this, 'createBlogPostFromDataArray']),
            $this->select->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function count(): int
    {
        $result = $this->count->execute($this->params);
        if (! $result) {
            throw new RuntimeException('Failed to fetch count from database');
        }
        return (int) $this->count->fetchColumn();
    }
}
