<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Library\Utils\Lock as LockUtil;
use App\Repos\Question as QuestionRepo;
use App\Services\Sync\QuestionScore as QuestionScoreSync;
use App\Services\Utils\QuestionScore as QuestionScoreService;

class SyncQuestionScoreTask extends Task
{

    public function mainAction()
    {
        $taskLockKey = $this->getTaskLockKey();

        $taskLockId = LockUtil::addLock($taskLockKey);

        if (!$taskLockId) return;

        $redis = $this->getRedis();

        $key = $this->getSyncKey();

        $questionIds = $redis->sRandMember($key, 1000);

        if (!$questionIds) return;

        $questionRepo = new QuestionRepo();

        $questions = $questionRepo->findByIds($questionIds);

        if ($questions->count() == 0) return;

        echo '------ start sync question score ------' . PHP_EOL;

        $service = new QuestionScoreService();

        foreach ($questions as $question) {
            $service->handle($question);
        }

        $redis->sRem($key, ...$questionIds);

        echo '------ end sync question score ------' . PHP_EOL;

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    protected function getSyncKey()
    {
        $sync = new QuestionScoreSync();

        return $sync->getSyncKey();
    }

}
