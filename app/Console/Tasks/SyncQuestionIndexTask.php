<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Library\Utils\Lock as LockUtil;
use App\Models\Question as QuestionModel;
use App\Repos\Question as QuestionRepo;
use App\Services\Search\QuestionDocument;
use App\Services\Search\QuestionSearcher;
use App\Services\Sync\QuestionIndex as QuestionIndexSync;

class SyncQuestionIndexTask extends Task
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

        echo '------ start sync question index ------' . PHP_EOL;

        $document = new QuestionDocument();

        $handler = new QuestionSearcher();

        $index = $handler->getXS()->getIndex();

        $index->openBuffer();

        foreach ($questions as $question) {

            $doc = $document->setDocument($question);

            if ($question->published == QuestionModel::PUBLISH_APPROVED) {
                $index->update($doc);
            } else {
                $index->del($question->id);
            }
        }

        $index->closeBuffer();

        $redis->sRem($key, ...$questionIds);

        echo '------ end sync question index ------' . PHP_EOL;

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    protected function getSyncKey()
    {
        $sync = new QuestionIndexSync();

        return $sync->getSyncKey();
    }

}
