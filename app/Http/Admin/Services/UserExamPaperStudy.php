<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\ExamPaperUserList as ExamPaperUserListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Services\Logic\Exam\Paper\PaperUserTrait;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;

class UserExamPaperStudy extends Service
{

    use PaperUserTrait;

    public function getExamPapers($id)
    {
        $validator = new ExamPaperUserValidator();

        $user = $validator->checkUser($id);

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

        return $this->handleExamPapers($pager);
    }

    protected function handleExamPapers($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new ExamPaperUserListBuilder();

            $items = $pager->items->toArray();
            $pipeA = $builder->handleExamPapers($items);
            $pipeB = $builder->objects($pipeA);

            $pager->items = $pipeB;
        }

        return $pager;
    }

}
