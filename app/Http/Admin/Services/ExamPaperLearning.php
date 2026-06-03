<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\ExamPaperUserList as ExamPaperUserListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Validators\ExamPaper as ExamPaperValidator;

class ExamPaperLearning extends Service
{

    use AccountSearchTrait;

    public function getLearnings($id)
    {
        $paper = $this->findPaperOrFail($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleAccountSearchParams($params);

        $params['paper_id'] = $paper->id;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new ExamPaperUserRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleLearnings($pager);
    }

    protected function handleLearnings($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new ExamPaperUserListBuilder();

            $items = $pager->items->toArray();
            $pipeA = $builder->handleAuthCode($items);
            $pipeB = $builder->handleExamPapers($pipeA);
            $pipeC = $builder->handleUsers($pipeB);
            $pipeD = $builder->objects($pipeC);

            $pager->items = $pipeD;
        }

        return $pager;
    }

    protected function findPaperOrFail($id)
    {
        $validator = new ExamPaperValidator();

        return $validator->checkExamPaper($id);
    }

}
