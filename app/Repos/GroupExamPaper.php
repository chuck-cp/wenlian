<?php
/**
 * @copyright Copyright (c) 2025 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\GroupExamPaper as GroupExamPaperModel;
use Phalcon\Mvc\Model;

class GroupExamPaper extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(GroupExamPaperModel::class);

        $builder->where('1 = 1');

        if (!empty($where['group_id'])) {
            $builder->andWhere('group_id = :group_id:', ['group_id' => $where['group_id']]);
        }

        if (!empty($where['paper_id'])) {
            $builder->andWhere('paper_id = :paper_id:', ['paper_id' => $where['paper_id']]);
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
     * @return GroupExamPaperModel|Model|bool
     */
    public function findById($id)
    {
        return GroupExamPaperModel::findFirst($id);
    }

    /**
     * @param int $groupId
     * @param int $paperId
     * @return GroupExamPaperModel|Model|bool
     */
    public function findGroupExamPaper($groupId, $paperId)
    {
        return GroupExamPaperModel::findFirst([
            'conditions' => 'group_id = ?1 AND paper_id = ?2',
            'bind' => [1 => $groupId, 2 => $paperId],
            'order' => 'id DESC',
        ]);
    }

}
