<?php
/**
 * @copyright Copyright (c) 2025 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\GroupExamPaperList as GroupExamPaperListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Group as GroupModel;
use App\Models\GroupExamPaper as GroupExamPaperModel;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\Group as GroupRepo;
use App\Repos\GroupExamPaper as GroupExamPaperRepo;
use App\Validators\GroupExamPaper as GroupExamPaperValidator;

class GroupExamPaper extends Service
{

    public function create()
    {
        $post = $this->request->getPost();

        $validator = new GroupExamPaperValidator();

        $group = $validator->checkGroup($post['group_id']);

        $groupPaperRepo = new GroupExamPaperRepo();

        $paperIds = $post['xm_paper_ids'] ? explode(',', $post['xm_paper_ids']) : [];

        if (!$paperIds) return;

        foreach ($paperIds as $paperId) {

            $paper = $validator->checkExamPaper($paperId);
            $groupPaper = $groupPaperRepo->findGroupExamPaper($group->id, $paper->id);

            if (!$groupPaper) {
                $groupPaperModel = new GroupExamPaperModel();
                $groupPaperModel->group_id = $group->id;
                $groupPaperModel->paper_id = $paper->id;
                $groupPaperModel->create();
            }
        }

        $this->recountGroupExamPapers($group);
    }

    public function delete($id)
    {
        $validator = new GroupExamPaperValidator();

        $groupPaper = $validator->checkById($id);

        $group = $validator->checkGroup($groupPaper->group_id);

        $groupPaper->delete();

        $this->recountGroupExamPapers($group);
    }

    public function getExamPapers($id)
    {
        $validator = new GroupExamPaperValidator();

        $group = $validator->checkGroup($id);

        $pagerQuery = new PagerQuery();

        $params['group_id'] = $group->id;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new GroupExamPaperRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleExamPapers($pager);
    }

    public function getXmExamPapers()
    {
        $paperRepo = new ExamPaperRepo();

        $items = $paperRepo->findAll([
            'free' => 0,
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
                'value' => $item->id,
                'selected' => false,
            ];
        }

        return $result;
    }

    protected function recountGroupExamPapers(GroupModel $group)
    {
        $groupRepo = new GroupRepo();

        $paperCount = $groupRepo->countExamPapers($group->id);

        $group->paper_count = $paperCount;

        $group->update();
    }

    protected function handleExamPapers($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new GroupExamPaperListBuilder();

            $items = $pager->items->toArray();
            $pipeA = $builder->handleExamPapers($items);
            $pipeB = $builder->objects($pipeA);

            $pager->items = $pipeB;
        }

        return $pager;
    }

}
