<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Mapper;

use DateTimeImmutable;
use GetLaminas\Ecosystem\CreateEcosystemPackageFromArrayTrait;
use GetLaminas\Ecosystem\EcosystemPackage;
use Laminas\Paginator\Paginator;
use PDO;

use function property_exists;
use function sprintf;

class PdoMapper implements MapperInterface
{
    use CreateEcosystemPackageFromArrayTrait;

    public function __construct(private readonly PDO $pdo)
    {
    }

    public function fetchAll(): Paginator
    {
        $select = 'SELECT * FROM packages ORDER BY downloads DESC LIMIT :offset, :limit';
        $count  = 'SELECT COUNT(id) FROM packages';
        return $this->preparePaginator($select, $count);
    }

    public function fetchAllByFilters(array $filters, string $search = ''): Paginator
    {
        $select = 'SELECT * FROM packages';
        $count  = 'SELECT COUNT(id) FROM packages';

        $values = [];

        /**
         * @var string $filterType
         * @var array<string>|null $filterValues
         */
        foreach ($filters as $filterType => $filterValues) {
            if ($filterValues === null) {
                continue;
            }

            if (property_exists(EcosystemPackage::class, $filterType)) {
                foreach ($filterValues as $filterValue) {
                    $where   = (empty($values) ? ' WHERE ' : ' AND ') . $filterType . ' LIKE :' . $filterType;
                    $select .= $where;
                    $count  .= $where;

                    $values[':' . $filterType] = '%' . $filterValue . '%';
                }
            }
        }

        if ($search !== '') {
            $select           .= (empty($values) ? ' WHERE ' : ' AND ') . 'name LIKE :search';
            $count            .= (empty($values) ? ' WHERE ' : ' AND ') . 'name LIKE :search';
            $values[':search'] = '%' . $search . '%';
        }

        $select .= ' ORDER BY downloads DESC LIMIT :offset, :limit';

        return $this->preparePaginator(
            $select,
            $count,
            $values
        );
    }

    public function fetchAllByTag(string $tag): Paginator
    {
        $select = 'SELECT * FROM packages '
            . 'WHERE tags LIKE :tag '
            . 'ORDER BY downloads '
            . 'DESC LIMIT :offset, :limit';
        $count  = 'SELECT COUNT(id) FROM packages WHERE tags LIKE :tag';
        return $this->preparePaginator($select, $count, [':tag' => sprintf('%%|%s|%%', $tag)]);
    }

    public function fetchAllByCategory(string $category): Paginator
    {
        $select = 'SELECT * FROM packages '
            . 'WHERE categories LIKE :category '
            . 'ORDER BY downloads '
            . 'DESC LIMIT :offset, :limit';
        $count  = 'SELECT COUNT(id) FROM packages WHERE categories LIKE :category';
        return $this->preparePaginator($select, $count, [':tag' => sprintf('%%|%s|%%', $category)]);
    }

    public function search(string $toMatch): ?array
    {
        $select = $this->pdo->prepare('SELECT id, title from search_packages WHERE search MATCH :query');
        if (! $select->execute([':query' => $toMatch])) {
            return null;
        }

        return $select->fetchAll();
    }

    /**
     * @return Paginator<int, EcosystemPackage>
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

    public function fetchPackagesDueUpdates(DateTimeImmutable $updated): ?array
    {
        $select = $this->pdo->prepare('SELECT id, name, updated FROM packages WHERE updated <= :updated ');

        if (! $select->execute([':updated' => $updated->getTimestamp()])) {
            return null;
        }

        return $select->fetchAll();
    }
}
