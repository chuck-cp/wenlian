<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Caches;

use App\Builders\ExamPaperUserList as ExamPaperUserListBuilder;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ExamPaperLatestUnitUserList extends Cache
{

    protected $lifetime = 30 * 60;

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getKey($id = null)
    {
        return "exam_paper_latest_unit_user_list:{$id}";
    }

    public function getContent($id = null)
    {
        $relations = $this->findPaperUsers($id);

        $result = [];

        if ($relations->count() == 0) {
            return $result;
        }

        $builder = new ExamPaperUserListBuilder();

        $users = $builder->getUsers($relations->toArray());

        foreach ($relations as $relation) {

            $user = $users[$relation->user_id] ?? new \stdClass();

            $result[] = [
                'id' => $relation->id,
                'user_score' => $relation->user_score,
                'user_duration' => $relation->user_duration,
                'start_time' => $relation->start_time,
                'end_time' => $relation->end_time,
                'create_time' => $relation->create_time,
                'update_time' => $relation->update_time,
                'user' => $user,
            ];
        }

        return $result;
    }

    /**
     * @param int $paperId
     * @param int $limit
     * @return ResultsetInterface|Resultset|ExamPaperUserModel[]
     */
    protected function findPaperUsers($paperId, $limit = 5)
    {
        return ExamPaperUserModel::query()
            ->where('paper_id = :paper_id:', ['paper_id' => $paperId])
            ->andWhere('debut = 1')
            ->orderBy('id DESC')
            ->limit($limit)
            ->execute();
    }

}
