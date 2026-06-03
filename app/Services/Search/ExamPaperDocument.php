<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Search;

use App\Models\ExamPaper as ExamPaperModel;
use App\Repos\Category as CategoryRepo;
use Phalcon\Di\Injectable;

class ExamPaperDocument extends Injectable
{

    /**
     * 设置文档
     *
     * @param ExamPaperModel $paper
     * @return \XSDocument
     */
    public function setDocument(ExamPaperModel $paper)
    {
        $doc = new \XSDocument();

        $data = $this->formatDocument($paper);

        $doc->setFields($data);

        return $doc;
    }

    /**
     * 格式化文档
     *
     * @param ExamPaperModel $paper
     * @return array
     */
    public function formatDocument(ExamPaperModel $paper)
    {
        if (is_array($paper->attrs)) {
            $paper->attrs = kg_json_encode($paper->attrs);
        }

        if (is_array($paper->tags)) {
            $paper->tags = kg_json_encode($paper->tags);
        }

        $category = '{}';

        if ($paper->category_id > 0) {
            $category = $this->handleCategory($paper->category_id);
        }

        $paper->cover = ExamPaperModel::getCoverPath($paper->cover);

        return [
            'id' => $paper->id,
            'title' => $paper->title,
            'cover' => $paper->cover,
            'summary' => $paper->summary,
            'keywords' => $paper->keywords,
            'level' => $paper->level,
            'attrs' => $paper->attrs,
            'tags' => $paper->tags,
            'category_id' => $paper->category_id,
            'exam_type' => $paper->exam_type,
            'pack_type' => $paper->pack_type,
            'grade_type' => $paper->grade_type,
            'market_price' => $paper->market_price,
            'vip_price' => $paper->vip_price,
            'study_expiry' => $paper->study_expiry,
            'refund_expiry' => $paper->refund_expiry,
            'question_count' => $paper->question_count,
            'favorite_count' => $paper->favorite_count,
            'join_count' => $paper->join_count,
            'pass_count' => $paper->pass_count,
            'category' => $category,
        ];
    }

    protected function handleCategory($id)
    {
        $categoryRepo = new CategoryRepo();

        $category = $categoryRepo->findById($id);

        return kg_json_encode([
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

}
