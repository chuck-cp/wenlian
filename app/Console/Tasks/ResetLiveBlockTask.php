<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Library\Utils\Lock as LockUtil;
use App\Models\LiveBlock as LiveBlockModel;
use App\Services\Logic\Live\LiveManage as LiveManageService;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ResetLiveBlockTask extends Task
{

    public function mainAction()
    {
        $taskLockKey = $this->getTaskLockKey();

        $taskLockId = LockUtil::addLock($taskLockKey);

        if (!$taskLockId) return;

        $courseIds = $this->findCourseIds();

        echo sprintf('pending tasks: %s', count($courseIds)) . PHP_EOL;

        if (empty($courseIds)) return;

        echo '------ start reset live block task ------' . PHP_EOL;

        foreach ($courseIds as $courseId) {
            $this->deleteLiveBlockCache($courseId);
            $this->createBlockCache($courseId);
        }

        echo '------ end reset live block task ------' . PHP_EOL;

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    protected function deleteLiveBlockCache($courseId)
    {
        $service = new LiveManageService();

        $service->deleteLiveBlockCache($courseId);
    }

    protected function createBlockCache($courseId)
    {
        $rows = $this->findActiveBlock($courseId);

        if ($rows->count() == 0) return;

        $service = new LiveManageService();

        $userIds = kg_array_column($rows->toArray(), 'user_id');

        $service->addBlockCache($courseId, $userIds);
    }

    /**
     * @param int $courseId
     * @return ResultsetInterface|Resultset|LiveBlockModel[]
     */
    protected function findActiveBlock($courseId)
    {
        $time = strtotime('+1 day');

        return LiveBlockModel::query()
            ->where('course_id = :course_id:', ['course_id' => $courseId])
            ->andWhere('expire_time > :time:', ['time' => $time])
            ->execute();
    }

    protected function findCourseIds()
    {
        $result = [];

        $rows = LiveBlockModel::query()
            ->columns('course_id')
            ->distinct(true)
            ->execute();

        if ($rows->count() > 0) {
            $result = array_column($rows->toArray(), 'course_id');
        }

        return $result;
    }

}
