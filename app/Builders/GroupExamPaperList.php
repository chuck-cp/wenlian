<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\Group as GroupRepo;

class GroupExamPaperList extends Builder
{

    public function handleGroups($relations)
    {
        $groups = $this->getGroups($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['group'] = $groups[$value['group_id']] ?? null;
        }

        return $relations;
    }

    public function handleExamPapers($relations)
    {
        $papers = $this->getExamPapers($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['paper'] = $papers[$value['paper_id']] ?? null;
        }

        return $relations;
    }

    public function getGroups($relations)
    {
        $ids = kg_array_column($relations, 'group_id');

        $groupRepo = new GroupRepo();

        $groups = $groupRepo->findShallowGroupByIds($ids);

        $result = [];

        foreach ($groups->toArray() as $group) {
            $result[$group['id']] = $group;
        }

        return $result;
    }

    public function getExamPapers($relations)
    {
        $ids = kg_array_column($relations, 'paper_id');

        $paperRepo = new ExamPaperRepo();

        $papers = $paperRepo->findShallowExamPaperByIds($ids);

        $result = [];

        foreach ($papers->toArray() as $paper) {
            $result[$paper['id']] = $paper;
        }

        return $result;
    }

}
