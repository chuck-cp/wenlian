<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Repos\ExamPaper as ExamPaperRepo;
use App\Validators\ExamPaper as ExamPaperValidator;

class ExamPaperSummaryStat extends Service
{

    public function handle($id)
    {
        $paper = $this->findPaperOrFail($id);

        $cache = $this->getCache();

        $keyName = $this->getSummaryStatCacheKey($paper->id);

        $result = $cache->get($keyName);

        if ($result) return $result;

        $result = [
            'pass_rate' => 0,
            'total_user_count' => 0,
            'pass_user_count' => 0,
            'avg_user_score' => 0,
            'min_user_score' => 0,
            'max_user_score' => 0,
        ];

        $paperUsers = $this->findPaperUsers($paper->id);

        if ($paperUsers->count() == 0) return $result;

        $totalUserCount = 0;
        $passUserCount = 0;
        $totalUserScore = 0;
        $maxUserScore = 0;
        $minUserScore = 999;

        foreach ($paperUsers as $paperUser) {
            $totalUserScore += $paperUser->user_score;
            if ($paperUser->user_score < $minUserScore) {
                $minUserScore = $paperUser->user_score;
            }
            if ($paperUser->user_score > $maxUserScore) {
                $maxUserScore = $paperUser->user_score;
            }
            if ($paperUser->user_score >= $paperUser->pass_score) {
                $passUserCount++;
            }
            $totalUserCount++;
        }

        $avgUserScore = round($totalUserScore / $totalUserCount, 2);
        $passRate = round($passUserCount / $totalUserCount, 4);

        $result = [
            'pass_rate' => $passRate,
            'total_user_count' => $totalUserCount,
            'pass_user_count' => $passUserCount,
            'avg_user_score' => $avgUserScore,
            'min_user_score' => $minUserScore,
            'max_user_score' => $maxUserScore,
        ];

        $cache->save($keyName, $result, 1800);

        return $result;
    }

    protected function getSummaryStatCacheKey($paperId)
    {
        return "exam_paper_summary_stat:{$paperId}";
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
