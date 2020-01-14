<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace GetLaminas\Blog\Mapper;

use GetLaminas\Blog\BlogPost;
use GetLaminas\Blog\CreateBlogPostFromDataArray;
use PDO;
use Laminas\Paginator\Paginator;

use function array_map;
use function array_reduce;
use function explode;
use function sprintf;
use function str_replace;
use function trim;

class PdoMapper implements MapperInterface
{
    use CreateBlogPostFromDataArray;

    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo    = $pdo;
    }

    public function fetch(string $id) : ?BlogPost
    {
        $select = $this->pdo->prepare('SELECT * from posts WHERE id = :id');
        if (! $select->execute([':id' => $id])) {
            return null;
        }

        $post = $select->fetch();
        return $post ? $this->createBlogPostFromDataArray($post) : null;
    }

    public function fetchAll() : Paginator
    {
        $select = 'SELECT * FROM posts WHERE draft = 0 AND public = 1 ORDER BY created DESC LIMIT :offset, :limit';
        $count  = 'SELECT COUNT(id) FROM posts WHERE draft = 0 AND public = 1';
        return $this->preparePaginator($select, $count);
    }

    public function fetchAllByAuthor(string $author) : Paginator
    {
        $select = 'SELECT * FROM posts '
            . 'WHERE draft = 0 AND public = 1 AND author = :author '
            . 'ORDER BY created '
            . 'DESC LIMIT :offset, :limit';
        $count  = 'SELECT COUNT(id) FROM posts WHERE draft = 0 AND public = 1 AND author = :author';
        return $this->preparePaginator($select, $count, [':author' => $author]);
    }

    public function fetchAllByTag(string $tag) : Paginator
    {
        $select = 'SELECT * FROM posts '
            . 'WHERE draft = 0 AND public = 1 AND tags LIKE :tag '
            . 'ORDER BY created '
            . 'DESC LIMIT :offset, :limit';
        $count  = 'SELECT COUNT(id) FROM posts WHERE draft = 0 AND public = 1 AND tags LIKE :tag';
        return $this->preparePaginator($select, $count, [':tag' => sprintf('%%|%s|%%', $tag)]);
    }

    public function search(string $toMatch) : array
    {
        $select = $this->pdo->prepare('SELECT id, title from search WHERE search MATCH :query');
        if (! $select->execute([':query' => $toMatch])) {
            return null;
        }

        return $select->fetchAll();
    }

    private function preparePaginator(string $select, string $count, array $params = []) : Paginator
    {
        $select = $this->pdo->prepare($select);
        $count  = $this->pdo->prepare($count);
        return new Paginator(new PdoPaginator(
            $select,
            $count,
            $params
        ));
    }
}
