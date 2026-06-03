<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Repos\ExamPaper as ExamPaperRepo;
use App\Services\Logic\Exam\Pilot as PilotService;

class ExamPaperUserList extends Builder
{

    public function handleExamPapers($relations)
    {
        $papers = $this->getExamPapers($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['exam_paper'] = $papers[$value['paper_id']] ?? null;
        }

        return $relations;
    }

    public function handleUsers($relations)
    {
        $users = $this->getUsers($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['user'] = $users[$value['user_id']] ?? null;
        }

        return $relations;
    }

    public function handleAuthCode($relations)
    {
        $service = new PilotService();

        foreach ($relations as $key => $value) {
            $relations[$key]['auth_code'] = $service->getAuthCode($value['id']);
        }

        return $relations;
    }

    public function getExamPapers($relations)
    {
        $ids = kg_array_column($relations, 'paper_id');

        $paperRepo = new ExamPaperRepo();

        $columns = [
            'id', 'title', 'cover', 'level', 'duration',
            'exam_type', 'pack_type', 'grade_type',
            'market_price', 'vip_price', 'total_score', 'pass_score',
            'question_count', 'favorite_count', 'join_count',
            'fake_join_count', 'pass_count',
        ];

        $papers = $paperRepo->findByIds($ids, $columns);

        $baseUrl = kg_cos_url();

        $result = [];

        foreach ($papers->toArray() as $paper) {

            if ($paper['fake_join_count'] > $paper['join_count']) {
                $paper['join_count'] = $paper['fake_join_count'];
            }

            $paper['cover'] = $baseUrl . $paper['cover'];

            $result[$paper['id']] = $paper;
        }

        return $result;
    }

    public function getUsers($relations)
    {
        $ids = kg_array_column($relations, 'user_id');

        return $this->getShallowUserByIds($ids);
    }

}
