<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User;

use App\Builders\ExamPaperUserList as ExamPaperUserListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\UserTrait;

class StudyExamPaperList extends LogicService
{

    use UserTrait;

    public function handle($id)
    {
        $user = $this->checkUserCache($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['user_id'] = $user->id;
        $params['debut'] = 1;
        $params['deleted'] = 0;

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

        $relations = $pager->items->toArray();

        $builder = new ExamPaperUserListBuilder();

        $examPapers = $builder->getExamPapers($relations);

        $items = [];

        foreach ($relations as $relation) {
            $examPaper = $examPapers[$relation['paper_id']] ?? new \stdClass();
            $items[] = [
                'id' => $relation['id'],
                'status' => $relation['status'],
                'source_type' => $relation['source_type'],
                'grade_type' => $relation['grade_type'],
                'paper_score' => $relation['paper_score'],
                'user_score' => $relation['user_score'],
                'user_duration' => $relation['user_duration'],
                'create_time' => $relation['create_time'],
                'expiry_time' => $relation['expiry_time'],
                'start_time' => $relation['start_time'],
                'end_time' => $relation['end_time'],
                'exam_paper' => $examPaper,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
