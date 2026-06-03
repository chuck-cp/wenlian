<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\Distribution as DistributionModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class Distribution extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(DistributionModel::class);

        $builder->where('1 = 1');

        if (!empty($where['id'])) {
            $builder->andWhere('id = :id:', ['id' => $where['id']]);
        }

        if (!empty($where['item_id'])) {
            $builder->andWhere('item_id = :item_id:', ['item_id' => $where['item_id']]);
        }

        if (!empty($where['item_type'])) {
            if (is_array($where['item_type'])) {
                $builder->inWhere('item_type', $where['item_type']);
            } else {
                $builder->andWhere('item_type = :item_type:', ['item_type' => $where['item_type']]);
            }
        }

        if (!empty($where['create_time'][0]) && !empty($where['create_time'][1])) {
            $startTime = strtotime($where['create_time'][0]);
            $endTime = strtotime($where['create_time'][1]);
            $builder->betweenWhere('create_time', $startTime, $endTime);
        }

        if (!empty($where['status'])) {
            if ($where['status'] == DistributionModel::STATUS_PENDING) {
                $builder->andWhere('start_time > :start_time:', ['start_time' => time()]);
            } elseif ($where['status'] == DistributionModel::STATUS_ACTIVE) {
                $builder->andWhere('start_time < :start_time:', ['start_time' => time()]);
                $builder->andWhere('end_time > :end_time:', ['end_time' => time()]);
            } elseif ($where['status'] == DistributionModel::STATUS_EXPIRED) {
                $builder->andWhere('end_time < :end_time:', ['end_time' => time()]);
            }
        }

        if (isset($where['published'])) {
            $builder->andWhere('published = :published:', ['published' => $where['published']]);
        }

        if (isset($where['deleted'])) {
            $builder->andWhere('deleted = :deleted:', ['deleted' => $where['deleted']]);
        }

        switch ($sort) {
            case 'oldest':
                $orderBy = 'id ASC';
                break;
            default:
                $orderBy = 'id DESC';
                break;
        }

        $builder->orderBy($orderBy);

        $pager = new PagerQueryBuilder([
            'builder' => $builder,
            'page' => $page,
            'limit' => $limit,
        ]);

        return $pager->paginate();
    }

    /**
     * @param int $id
     * @return DistributionModel|Model|bool
     */
    public function findById($id)
    {
        return DistributionModel::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * @param int $itemType
     * @param array $itemIds
     * @param array|string $columns
     * @return ResultsetInterface|Resultset|DistributionModel[]
     */
    public function findByItemIds($itemType, $itemIds, $columns = '*')
    {
        return DistributionModel::query()
            ->columns($columns)
            ->where('item_type = :item_type:', ['item_type' => $itemType])
            ->inWhere('item_id', $itemIds)
            ->execute();
    }

    /**
     * @param int $itemId
     * @param int $itemType
     * @return DistributionModel|Model|bool
     */
    public function findItemDistribution($itemId, $itemType)
    {
        return DistributionModel::findFirst([
            'conditions' => 'item_id = ?1 AND item_type = ?2 AND deleted = 0',
            'bind' => [1 => $itemId, 2 => $itemType],
            'order' => 'id DESC',
        ]);
    }

}
