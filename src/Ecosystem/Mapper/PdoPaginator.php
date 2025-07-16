<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Mapper;

use GetLaminas\Ecosystem\CreateEcosystemPackageFromArrayTrait;
use GetLaminas\Ecosystem\EcosystemPackage;
use Laminas\Paginator\Adapter\AdapterInterface;
use Override;
use PDO;
use PDOStatement;
use RuntimeException;

use function array_map;
use function array_merge;

/** @template-implements AdapterInterface<int, EcosystemPackage> */
class PdoPaginator implements AdapterInterface
{
    use CreateEcosystemPackageFromArrayTrait;

    /** @param array<string, mixed> $params */
    protected array $params;

    public function __construct(
        protected PDOStatement $select,
        protected PDOStatement $count,
        array $params = [],
    ) {
        $this->params = $params;
    }

    /** @inheritDoc */
    #[Override]
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
            $this->CreateEcosystemPackageFromArray(...),
            $this->select->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    #[Override]
    public function count(): int
    {
        $result = $this->count->execute($this->params);
        if (! $result) {
            throw new RuntimeException('Failed to fetch count from database');
        }
        return (int) $this->count->fetchColumn();
    }
}
