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

class ExamPaperTopMockUserList extends Cache
{

    protected $lifetime = 3600;

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getKey($id = null)
    {
        return "exam_paper_top_user_list:{$id}";
    }

    public function getContent($id = null)
    {
        $endTime = time();

        $startTime = strtotime('-1 week');
        $relations = $this->findPaperUsers($id, $startTime, $endTime);

        if ($relations->count() == 0) {
            $startTime = strtotime('-1 month');
            $relations = $this->findPaperUsers($id, $startTime, $endTime);
        }

        if ($relations->count() == 0) {
            $startTime = strtotime('-1 year');
            $relations = $this->findPaperUsers($id, $startTime, $endTime);
        }

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
                'user' => $user,
            ];
        }

        return $result;
    }

    /**
     * @param int $paperId
     * @param int $startTime
     * @param int $endTime
     * @param int $limit
     * @return ResultsetInterface|Resultset|ExamPaperUserModel[]
     */
    protected function findPaperUsers($paperId, $startTime, $endTime, $limit = 5)
    {
        $status = ExamPaperUserModel::STATUS_FINISHED;

        return ExamPaperUserModel::query()
            ->where('paper_id = :paper_id:', ['paper_id' => $paperId])
            ->betweenWhere('start_time', $startTime, $endTime)
            ->andWhere('status = :status:', ['status' => $status])
            ->andWhere('debut = 1')
            ->orderBy('user_score DESC')
            ->limit($limit)
            ->execute();
    }

}
