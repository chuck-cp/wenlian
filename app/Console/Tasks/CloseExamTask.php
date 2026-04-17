<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Library\Utils\Lock as LockUtil;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Services\Logic\Exam\MockPaperSubmit as ExamPaperSubmitService;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class CloseExamTask extends Task
{

    public function mainAction()
    {
        $taskLockKey = $this->getTaskLockKey();

        $taskLockId = LockUtil::addLock($taskLockKey);

        if (!$taskLockId) return;

        $exams = $this->findExams();

        echo sprintf('pending exams: %s', $exams->count()) . PHP_EOL;

        if ($exams->count() == 0) return;

        echo '------ start close exam task ------' . PHP_EOL;

        foreach ($exams as $exam) {
            $this->closeExam($exam);
        }

        LockUtil::releaseLock($taskLockKey, $taskLockId);

        echo '------ end close exam task ------' . PHP_EOL;
    }

    protected function closeExam(ExamPaperUserModel $paperUser)
    {
        $service = new ExamPaperSubmitService();

        $service->handlePaperSubmit($paperUser);
    }

    /**
     * 查找待关闭考试
     *
     * @param int $limit
     * @return ResultsetInterface|Resultset|ExamPaperUserModel[]
     */
    protected function findExams($limit = 1000)
    {
        $status = ExamPaperUserModel::STATUS_ACTIVE;

        return ExamPaperUserModel::query()
            ->where('status = :status:', ['status' => $status])
            ->andWhere('start_time + paper_duration < :time:', ['time' => time()])
            ->limit($limit)
            ->execute();
    }

}
