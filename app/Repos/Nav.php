<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Models\Nav as NavModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class Nav extends Repository
{

    /**
     * @param array $where
     * @return ResultsetInterface|Resultset|NavModel[]
     */
    public function findAll($where = [])
    {
        $query = NavModel::query();

        $query->where('1 = 1');

        if (isset($where['parent_id'])) {
            $query->andWhere('parent_id = :parent_id:', ['parent_id' => $where['parent_id']]);
        }

        if (!empty($where['position'])) {
            if (is_array($where['position'])) {
                $query->inWhere('position', $where['position']);
            } else {
                $query->andWhere('position = :position:', ['position' => $where['position']]);
            }
        }

        if (!empty($where['level'])) {
            if (is_array($where['level'])) {
                $query->inWhere('level', $where['level']);
            } else {
                $query->andWhere('level = :level:', ['level' => $where['level']]);
            }
        }

        if (isset($where['published'])) {
            $query->andWhere('published = :published:', ['published' => $where['published']]);
        }

        if (isset($where['deleted'])) {
            $query->andWhere('deleted = :deleted:', ['deleted' => $where['deleted']]);
        }

        $query->orderBy('position ASC,priority ASC');

        return $query->execute();
    }

    /**
     * @param int $id
     * @return NavModel|Model|bool
     */
    public function findById($id)
    {
        return NavModel::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    public function countChildNavs($navId)
    {
        return (int)NavModel::count([
            'conditions' => 'parent_id = :parent_id: AND published = 1 AND deleted = 0',
            'bind' => ['parent_id' => $navId],
        ]);
    }

}
