<?php

declare(strict_types=1);

namespace GetLaminas\Integration\Mapper;

use DateTimeImmutable;
use GetLaminas\Integration\CreateIntegrationFromArrayTrait;
use GetLaminas\Integration\Integration;
use Laminas\Paginator\Paginator;
use Override;
use PDO;

use function sprintf;

class PdoMapper implements MapperInterface
{
    use CreateIntegrationFromArrayTrait;

    public function __construct(private readonly PDO $pdo)
    {
    }

    #[Override]
    public function fetchAll(): Paginator
    {
        $select = 'SELECT * FROM packages ORDER BY downloads DESC LIMIT :offset, :limit';
        $count  = 'SELECT COUNT(id) FROM packages';
        return $this->preparePaginator($select, $count);
    }

    /**
     * @param  array<int, string> $keywords
     * @return Paginator<int, Integration>
     */
    #[Override]
    public function fetchAllByFilters(array $keywords, ?string $type = null, ?string $search = null): Paginator
    {
        $select = 'SELECT * FROM packages';
        $count  = 'SELECT COUNT(id) FROM packages';
        $values = [];

        foreach ($keywords as $i => $keyword) {
            $where   = (empty($values) ? ' WHERE (' : ' AND ') . ' keywords LIKE :keyword' . $i;
            $select .= $where;
            $count  .= $where;

            $values[':keyword' . $i] = '%' . $keyword . '%';
        }

        $select .= $values !== [] ? ')' : '';
        $count  .= $values !== [] ? ')' : '';

        if ($type !== null) {
            $where   = (empty($values) ? ' WHERE ' : ' AND ') . ' type LIKE :type';
            $select .= $where;
            $count  .= $where;

            $values[':type'] = '%' . $type . '%';
        }

        if ($search !== null) {
            $where             = (empty($values) ? ' WHERE (' : ' AND (')
                . 'name LIKE :search OR description LIKE :search OR keywords LIKE :search)';
            $select           .= $where;
            $count            .= $where;
            $values[':search'] = '%' . $search . '%';
        }

        $select .= ' ORDER BY downloads DESC LIMIT :offset, :limit';

        return $this->preparePaginator(
            $select,
            $count,
            $values
        );
    }

    #[Override]
    public function fetchAllByKeyword(string $keyword): Paginator
    {
        $select = 'SELECT * FROM packages '
            . 'WHERE keywords LIKE :keyword '
            . 'ORDER BY downloads '
            . 'DESC LIMIT :offset, :limit';
        $count  = 'SELECT COUNT(id) FROM packages WHERE keywords LIKE :keyword';
        return $this->preparePaginator($select, $count, [':tag' => sprintf('%%|%s|%%', $keyword)]);
    }

    #[Override]
    public function getPackagesTitles(): array
    {
        $select = $this->pdo->prepare('SELECT name from packages;');
        if (! $select->execute()) {
            return [];
        }

        return $select->fetchAll(PDO::FETCH_COLUMN);
    }

    #[Override]
    public function deletePackageByName(string $package): bool
    {
        $select = $this->pdo->prepare('DELETE from packages WHERE name = :name;');
        if (! $select->execute([':name' => $package])) {
            return false;
        }

        return true;
    }

    /**
     * @return Paginator<int, Integration>
     * @param array<string, mixed> $params
     */
    private function preparePaginator(string $select, string $count, array $params = []): Paginator
    {
        $select = $this->pdo->prepare($select);
        $count  = $this->pdo->prepare($count);
        return new Paginator(new PdoPaginator(
            $select,
            $count,
            $params
        ));
    }

    #[Override]
    public function fetchPackagesDueUpdates(DateTimeImmutable $updated): ?array
    {
        $select = $this->pdo->prepare('SELECT id, name, updated FROM packages WHERE updated <= :updated ');

        if (! $select->execute([':updated' => $updated->getTimestamp()])) {
            return null;
        }

        return $select->fetchAll();
    }

    public function searchPackage(string $search): ?array
    {
        $select = $this->pdo->prepare('SELECT name FROM packages WHERE name = :search');

        if (! $select->execute([':search' => $search])) {
            return null;
        }

        return $select->fetchAll();
    }
}
