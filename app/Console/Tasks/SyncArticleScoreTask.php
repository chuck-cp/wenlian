<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Library\Utils\Lock as LockUtil;
use App\Repos\Article as ArticleRepo;
use App\Services\Sync\ArticleScore as ArticleScoreSync;
use App\Services\Utils\ArticleScore as ArticleScoreService;

class SyncArticleScoreTask extends Task
{

    public function mainAction()
    {
        $taskLockKey = $this->getTaskLockKey();

        $taskLockId = LockUtil::addLock($taskLockKey);

        if (!$taskLockId) return;

        $redis = $this->getRedis();

        $key = $this->getSyncKey();

        $articleIds = $redis->sRandMember($key, 1000);

        if (!$articleIds) return;

        $articleRepo = new ArticleRepo();

        $articles = $articleRepo->findByIds($articleIds);

        if ($articles->count() == 0) return;

        echo '------ start sync article score ------' . PHP_EOL;

        $service = new ArticleScoreService();

        foreach ($articles as $article) {
            $service->handle($article);
        }

        $redis->sRem($key, ...$articleIds);

        echo '------ end sync article score ------' . PHP_EOL;

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    protected function getSyncKey()
    {
        $sync = new ArticleScoreSync();

        return $sync->getSyncKey();
    }

}
