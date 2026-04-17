<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Paper;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Services\Logic\ExamPaperTrait;
use App\Services\Logic\Service as LogicService;

class PaperMockHistory extends LogicService
{

    use ExamPaperTrait;

    public function handle($id)
    {
        $paper = $this->checkExamPaperCache($id);

        $user = $this->getLoginUser(true);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['paper_id'] = $paper->id;
        $params['user_id'] = $user->id;
        $params['deleted'] = 0;
        $params['status'] = [
            ExamPaperUserModel::STATUS_ACTIVE,
            ExamPaperUserModel::STATUS_WAITING,
            ExamPaperUserModel::STATUS_FINISHED,
        ];

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new ExamPaperUserRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handlePager($pager);
    }

    protected function handlePager($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $items = [];

        foreach ($pager->items->toArray() as $relation) {
            $items[] = [
                'id' => $relation['id'],
                'debut' => $relation['debut'],
                'status' => $relation['status'],
                'grade_type' => $relation['grade_type'],
                'paper_duration' => $relation['paper_duration'],
                'user_duration' => $relation['user_duration'],
                'paper_score' => $relation['paper_score'],
                'user_score' => $relation['user_score'],
                'start_time' => $relation['start_time'],
                'end_time' => $relation['end_time'],
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
