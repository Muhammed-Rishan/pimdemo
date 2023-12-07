<?php

namespace SpyBundle\Model\AdminActivity\Listing;

use Pimcore\Model\Listing;
use App\Model;
use Doctrine\DBAL\Query\QueryBuilder as DoctrineQueryBuilder;
use Pimcore\Model\Listing\Dao\QueryBuilderHelperTrait;
use SpyBundle\Model\AdminActivity;

class Dao extends Listing\Dao\AbstractDao
{
    use QueryBuilderHelperTrait;

    protected string $tableName = 'actives';

    /**
     * Get tableName, either for localized or non-localized data.
     *
     * @throws \Exception
     */
    protected function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @throws \Exception
     */
    public function getQueryBuilder(): DoctrineQueryBuilder
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $field = $this->getTableName().'.id';
        $queryBuilder->select([sprintf('SQL_CALC_FOUND_ROWS %s as id', $field)]);
        $queryBuilder->from($this->getTableName());

        $this->applyListingParametersToQueryBuilder($queryBuilder);

        return $queryBuilder;
    }

    /**
     * Loads objects from the database.
     *
     * @throws \Exception
     */
    public function load(): array
    {
        // load id's
        $list = $this->loadIdList();

        $objects = array();
        foreach ($list as $id) {
            if ($object = AdminActivity::getById($id)) {
                $objects[] = $object;
            }
        }

        $this->model->setData($objects);

        return $objects;
    }

    /**
     * Loads a list for the specicifies parameters, returns an array of ids.
     *
     * @return int[]
     * @throws \Exception
     */
    public function loadIdList(): array
    {
        $query = $this->getQueryBuilder();
        $objectIds = $this->db->fetchFirstColumn((string) $query, $this->model->getConditionVariables(), $this->model->getConditionVariableTypes());
        $this->totalCount = (int) $this->db->fetchOne('SELECT FOUND_ROWS()');

        return array_map('intval', $objectIds);
    }

    /**
     * Get Count.
     *
     * @throws \Exception
     */
    public function getCount(): int
    {
        if ($this->model->isLoaded()) {
            return count($this->model->getData());
        } else {
            $idList = $this->loadIdList();

            return count($idList);
        }
    }

    /**
     * Get Total Count.
     *
     * @throws \Exception
     */
    public function getTotalCount(): int
    {
        $queryBuilder = $this->getQueryBuilder();
        $this->prepareQueryBuilderForTotalCount($queryBuilder, $this->getTableName() . '.id');

        $totalCount = $this->db->fetchOne((string) $queryBuilder, $this->model->getConditionVariables(), $this->model->getConditionVariableTypes());

        return (int) $totalCount;
    }
}

