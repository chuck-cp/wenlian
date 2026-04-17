<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\ExamQuestionUser as ExamQuestionUserModel;
use App\Repos\ExamQuestionUser as ExamQuestionUserRepo;
use App\Services\Logic\Service as LogicService;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;

class UnitHistoryQuestionList extends LogicService
{

    public function handle($id)
    {
        $validator = new ExamPaperUserValidator();

        $paperUser = $validator->checkById($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['paper_user_id'] = $paperUser->id;
        $params['finished'] = 1;
        $params['deleted'] = 0;

        if (!empty($params['model'])) {
            $params['question_model'] = $params['model'];
        }

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new ExamQuestionUserRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handlePager($pager);
    }

    protected function handlePager($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        /**
         * @var $relations ExamQuestionUserModel[]
         */
        $relations = $pager->items;

        $items = [];

        foreach ($relations as $relation) {
            $items[] = [
                'id' => $relation->question_id,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
