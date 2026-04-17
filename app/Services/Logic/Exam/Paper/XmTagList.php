<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Paper;

use App\Models\Tag as TagModel;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\Tag as TagRepo;
use App\Services\Logic\Service as LogicService;

class XmTagList extends LogicService
{

    public function handle($id)
    {
        $tagRepo = new TagRepo();

        $allTags = $tagRepo->findAll([
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($allTags->count() == 0) return [];

        $paperTagIds = [];

        if ($id > 0) {
            $paper = $this->findExamPaper($id);
            if (!empty($paper->tags)) {
                $paperTagIds = kg_array_column($paper->tags, 'id');
            }
        }

        $list = [];

        foreach ($allTags as $tag) {
            $case1 = is_string($tag->scopes) && $tag->scopes == 'all';
            $case2 = is_array($tag->scopes) && in_array(TagModel::SCOPE_EXAM_PAPER, $tag->scopes);
            if ($case1 || $case2) {
                $selected = in_array($tag->id, $paperTagIds);
                $list[] = [
                    'name' => $tag->name,
                    'value' => $tag->id,
                    'selected' => $selected,
                ];
            }
        }

        return $list;
    }

    protected function findExamPaper($id)
    {
        $paperRepo = new ExamPaperRepo();

        return $paperRepo->findById($id);
    }

}
