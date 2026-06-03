<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Repos\ExamPaper as ExamPaperRepo;

class ExamPaperFavoriteList extends Builder
{

    public function handleExamPapers(array $relations)
    {
        $papers = $this->getExamPapers($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['exam_paper'] = $papers[$value['paper_id']] ?? null;
        }

        return $relations;
    }

    public function handleUsers(array $relations)
    {
        $users = $this->getUsers($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['user'] = $users[$value['user_id']] ?? null;
        }

        return $relations;
    }

    public function getExamPapers(array $relations)
    {
        $ids = kg_array_column($relations, 'paper_id');

        $paperRepo = new ExamPaperRepo();

        $columns = [
            'id', 'title', 'cover', 'level', 'duration',
            'exam_type', 'pack_type', 'market_price', 'vip_price',
            'question_count', 'favorite_count', 'join_count', 'pass_count',
        ];

        $papers = $paperRepo->findByIds($ids, $columns);

        $baseUrl = kg_cos_url();

        $result = [];

        foreach ($papers->toArray() as $paper) {
            $paper['cover'] = $baseUrl . $paper['cover'];
            $paper['market_price'] = (float)$paper['market_price'];
            $paper['vip_price'] = (float)$paper['vip_price'];
            $result[$paper['id']] = $paper;
        }

        return $result;
    }

    public function getUsers(array $relations)
    {
        $ids = kg_array_column($relations, 'user_id');

        return $this->getShallowUserByIds($ids);
    }

}
