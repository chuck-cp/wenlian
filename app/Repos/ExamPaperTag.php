<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Models\ExamPaperTag as ExamPaperTagModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ExamPaperTag extends Repository
{

    /**
     * @param int $paperId
     * @param int $tagId
     * @return ExamPaperTagModel|Model|bool
     */
    public function findExamPaperTag($paperId, $tagId)
    {
        return ExamPaperTagModel::findFirst([
            'conditions' => 'paper_id = :paper_id: AND tag_id = :tag_id:',
            'bind' => ['paper_id' => $paperId, 'tag_id' => $tagId],
        ]);
    }

    /**
     * @param array $tagIds
     * @return ResultsetInterface|Resultset|ExamPaperTagModel[]
     */
    public function findByTagIds($tagIds)
    {
        return ExamPaperTagModel::query()
            ->inWhere('tag_id', $tagIds)
            ->execute();
    }

}
