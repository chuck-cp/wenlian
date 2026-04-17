<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Teacher\Console;

use App\Library\Paginator\Query as PagerQuery;
use App\Repos\TeacherGradeExam as TeacherGradeExamRepo;
use App\Services\Logic\Exam\Pilot as PilotService;
use App\Services\Logic\Service as LogicService;

class GradeExamList extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser();

        $pagerQuery = new PagerQuery();

        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new TeacherGradeExamRepo();

        $pager = $repo->paginate($user->id, $page, $limit);

        return $this->handlePager($pager);
    }

    protected function handlePager($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $pilot = new PilotService();

        $items = [];

        foreach ($pager->items as $item) {

            $authCode = $pilot->getAuthCode($item->paper_user_id);

            $items[] = [
                'exam_paper_user' => [
                    'id' => $item->paper_user_id,
                    'create_time' => $item->paper_user_create_time,
                ],
                'exam_paper' => [
                    'id' => $item->paper_id,
                    'title' => $item->paper_title,
                ],
                'user' => [
                    'id' => $item->user_id,
                    'name' => $item->user_name,
                ],
                'auth_code' => $authCode,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
