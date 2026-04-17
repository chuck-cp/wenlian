<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\ExamQuestionFavorite as ExamQuestionFavoriteModel;
use App\Repos\ExamQuestionFavorite as ExamQuestionFavoriteRepo;
use App\Services\Logic\Service as LogicService;

class FavoriteQuestionList extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser(true);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['user_id'] = $user->id;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new ExamQuestionFavoriteRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handlePager($pager);
    }

    protected function handlePager($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        /**
         * @var $relations ExamQuestionFavoriteModel[]
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
