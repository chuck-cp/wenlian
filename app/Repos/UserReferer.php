<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\UserReferer as UserRefererModel;
use Phalcon\Mvc\Model;

class UserReferer extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(UserRefererModel::class);

        $builder->where('1 = 1');

        if (!empty($where['user_id'])) {
            $builder->andWhere('user_id = :user_id:', ['user_id' => $where['user_id']]);
        }

        if (!empty($where['parent_id'])) {
            $builder->andWhere('parent_id = :parent_id:', ['parent_id' => $where['parent_id']]);
        }

        if (!empty($where['parent_level'])) {
            if (is_array($where['parent_level'])) {
                $builder->inWhere('parent_level', $where['parent_level']);
            } else {
                $builder->andWhere('parent_level = :parent_level:', ['parent_level' => $where['parent_level']]);
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
     * @param int $userId
     * @param int $parentLevel
     * @return UserRefererModel|Model|bool
     */
    public function findByUserParentLevel($userId, $parentLevel)
    {
        return UserRefererModel::findFirst([
            'conditions' => 'user_id = ?1 AND parent_level = ?2',
            'bind' => [1 => $userId, 2 => $parentLevel],
        ]);
    }

}
