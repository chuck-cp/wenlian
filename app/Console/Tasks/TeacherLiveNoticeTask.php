<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Library\Utils\Lock;
use App\Library\Utils\Lock as LockUtil;
use App\Models\ChapterLive as ChapterLiveModel;
use App\Repos\ChapterLive as ChapterLiveRepo;
use App\Services\Logic\Notice\External\TeacherLive as TeacherLiveNotice;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class TeacherLiveNoticeTask extends Task
{

    /**
     * 生成讲师提醒
     */
    public function provideAction()
    {
        $taskLockKey = $this->getTaskLockKey('teacher_live_notice_provide');

        $taskLockId = LockUtil::addLock($taskLockKey);

        if (!$taskLockId) return;

        $lives = $this->findLives();

        if ($lives->count() == 0) return;

        echo '------ start teacher live notice provide task ------' . PHP_EOL;

        $redis = $this->getRedis();

        $keyName = $this->getCacheKeyName();

        foreach ($lives as $live) {
            $redis->sAdd($keyName, $live->id);
        }

        $redis->expire($keyName, 86400);

        echo '------ end teacher live notice provide task ------' . PHP_EOL;

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    /**
     * 消费讲师提醒
     */
    public function consumeAction()
    {
        $taskLockKey = $this->getTaskLockKey('teacher_live_notice_consume');

        $taskLockId = LockUtil::addLock($taskLockKey);

        if (!$taskLockId) return;

        $redis = $this->getRedis();

        $keyName = $this->getCacheKeyName();

        $liveIds = $redis->sMembers($keyName);

        if (count($liveIds) == 0) return;

        echo '------ start teacher live notice consume task ------' . PHP_EOL;

        $liveRepo = new ChapterLiveRepo();

        $notice = new TeacherLiveNotice();

        foreach ($liveIds as $liveId) {

            $live = $liveRepo->findById($liveId);

            if ($live->start_time > time() && $live->start_time < time() + 60 * 60) {

                $notice->createTask($live);

                $redis->sRem($keyName, $liveId);
            }
        }

        echo '----- end teacher live notice consume task ------' . PHP_EOL;

        Lock::releaseLock($taskLockKey, $taskLockId);
    }

    /**
     * @return ResultsetInterface|Resultset|ChapterLiveModel[]
     */
    protected function findLives()
    {
        $today = strtotime(date('Ymd'));

        return ChapterLiveModel::query()
            ->betweenWhere('start_time', $today, $today + 86400)
            ->execute();
    }

    protected function getCacheKeyName()
    {
        return 'teacher_live_notice_task';
    }

}
