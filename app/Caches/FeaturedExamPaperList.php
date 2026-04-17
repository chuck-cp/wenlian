<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Caches;

use App\Models\ExamPaper as ExamPaperModel;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class FeaturedExamPaperList extends Cache
{

    protected $lifetime = 3600;

    protected $limit = 5;

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getKey($id = null)
    {
        return 'featured_exam_paper_list';
    }

    public function getContent($id = null)
    {
        $papers = $this->findExamPapers($this->limit);

        if ($papers->count() == 0) {
            return [];
        }

        $result = [];

        foreach ($papers as $paper) {

            $joinCount = $paper->join_count;

            if ($paper->fake_join_count > $paper->join_count) {
                $joinCount = $paper->fake_join_count;
            }

            $result[] = [
                'id' => $paper->id,
                'title' => $paper->title,
                'cover' => $paper->cover,
                'level' => $paper->level,
                'exam_type' => $paper->exam_type,
                'pack_type' => $paper->pack_type,
                'market_price' => (float)$paper->market_price,
                'vip_price' => (float)$paper->vip_price,
                'join_count' => $joinCount,
                'pass_count' => $paper->pass_count,
                'favorite_count' => $paper->favorite_count,
                'question_count' => $paper->question_count,
            ];
        }

        return $result;
    }

    /**
     * @param int $limit
     * @return ResultsetInterface|Resultset|ExamPaperModel[]
     */
    protected function findExamPapers($limit = 5)
    {
        return ExamPaperModel::query()
            ->where('featured = 1')
            ->andWhere('published = 1')
            ->andWhere('deleted = 0')
            ->orderBy('RAND()')
            ->limit($limit)
            ->execute();
    }

}
