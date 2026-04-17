<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Repos\ExamPaper as ExamPaperRepo;
use App\Validators\ExamPaper as ExamPaperValidator;

class ExamPaperRangeStat extends Service
{

    public function handle($id)
    {
        $result = [
            'A' => ['name' => '优秀', 'count' => 0, 'rate' => 0],
            'B' => ['name' => '良好', 'count' => 0, 'rate' => 0],
            'C' => ['name' => '及格', 'count' => 0, 'rate' => 0],
            'D' => ['name' => '不及格', 'count' => 0, 'rate' => 0],
        ];

        $paper = $this->findPaperOrFail($id);

        $paperUsers = $this->findPaperUsers($paper->id);

        if ($paperUsers->count() == 0) return $result;

        $totalScore = $paper->total_score;

        $totalCount = 0;

        foreach ($paperUsers as $paperUser) {
            $userScore = $paperUser->user_score;
            if ($userScore >= $totalScore * 0.9) {
                $result['A']['count']++;
            } elseif ($userScore >= $totalScore * 0.7) {
                $result['B']['count']++;
            } elseif ($userScore >= $totalScore * 0.6) {
                $result['C']['count']++;
            } else {
                $result['D']['count']++;
            }
            $totalCount++;
        }

        foreach ($result as $key => $value) {
            $result[$key]['rate'] = round($value['count'] / $totalCount, 4);
        }

        return $result;
    }

    protected function findPaperUsers($paperId)
    {
        $paperRepo = new ExamPaperRepo();

        return $paperRepo->findExamPaperUsers($paperId);
    }

    protected function findPaperOrFail($id)
    {
        $validator = new ExamPaperValidator();

        return $validator->checkExamPaper($id);
    }

}
