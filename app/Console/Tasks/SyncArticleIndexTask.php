<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Library\Utils\Lock as LockUtil;
use App\Models\Article as ArticleModel;
use App\Repos\Article as ArticleRepo;
use App\Services\Search\ArticleDocument;
use App\Services\Search\ArticleSearcher;
use App\Services\Sync\ArticleIndex as ArticleIndexSync;

class SyncArticleIndexTask extends Task
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

        echo '------ start sync article index ------' . PHP_EOL;

        $document = new ArticleDocument();

        $handler = new ArticleSearcher();

        $index = $handler->getXS()->getIndex();

        $index->openBuffer();

        foreach ($articles as $article) {

            $doc = $document->setDocument($article);

            if ($article->published == 1) {
                $index->update($doc);
            } else {
                $index->del($article->id);
            }
        }

        $index->closeBuffer();

        $redis->sRem($key, ...$articleIds);

        echo '------ end sync article index ------' . PHP_EOL;

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    protected function getSyncKey()
    {
        $sync = new ArticleIndexSync();

        return $sync->getSyncKey();
    }

}
