<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Library\Utils\Lock as LockUtil;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Services\Search\ExamPaperDocument;
use App\Services\Search\ExamPaperSearcher;
use App\Services\Sync\ExamPaperIndex as ExamPaperIndexSync;

class SyncExamPaperIndexTask extends Task
{

    public function mainAction()
    {
        $taskLockKey = $this->getTaskLockKey();

        $taskLockId = LockUtil::addLock($taskLockKey);

        if (!$taskLockId) return;

        $redis = $this->getRedis();

        $key = $this->getSyncKey();

        $paperIds = $redis->sRandMember($key, 1000);

        if (!$paperIds) return;

        $paperRepo = new ExamPaperRepo();

        $papers = $paperRepo->findByIds($paperIds);

        if ($papers->count() == 0) return;

        echo '------ start sync exam paper index ------' . PHP_EOL;

        $document = new ExamPaperDocument();

        $handler = new ExamPaperSearcher();

        $index = $handler->getXS()->getIndex();

        $index->openBuffer();

        foreach ($papers as $paper) {

            $doc = $document->setDocument($paper);

            if ($paper->published == 1) {
                $index->update($doc);
            } else {
                $index->del($paper->id);
            }
        }

        $index->closeBuffer();

        $redis->sRem($key, ...$paperIds);

        echo '------ end sync exam paper index ------' . PHP_EOL;

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    protected function getSyncKey()
    {
        $sync = new ExamPaperIndexSync();

        return $sync->getSyncKey();
    }

}
