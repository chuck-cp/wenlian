<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\CashHistory as CashHistoryModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class CashHistory extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(CashHistoryModel::class);

        $builder->where('1 = 1');

        if (!empty($where['user_id'])) {
            $builder->andWhere('user_id = :user_id:', ['user_id' => $where['user_id']]);
        }

        if (!empty($where['event_id'])) {
            $builder->andWhere('event_id = :event_id:', ['event_id' => $where['event_id']]);
        }

        if (!empty($where['event_type'])) {
            if (is_array($where['event_type'])) {
                $builder->inWhere('event_type', $where['event_type']);
            } else {
                $builder->andWhere('event_type = :event_type:', ['event_type' => $where['event_type']]);
            }
        }

        if (!empty($where['create_time'][0]) && !empty($where['create_time'][1])) {
            $startTime = strtotime($where['create_time'][0]);
            $endTime = strtotime($where['create_time'][1]);
            $builder->betweenWhere('create_time', $startTime, $endTime);
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
     * @return CashHistoryModel|Model|bool
     */
    public function findById($id)
    {
        return CashHistoryModel::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * @param int $userId
     * @param string $month
     * @return ResultsetInterface|Resultset|CashHistoryModel[]
     */
    public function findUserMonthlyHistory($userId, $month)
    {
        $startTime = strtotime($month);
        $endTime = strtotime('+1 month', $startTime);

        return CashHistoryModel::query()
            ->where('user_id = :user_id:', ['user_id' => $userId])
            ->betweenWhere('create_time', $startTime, $endTime)
            ->execute();
    }

}
