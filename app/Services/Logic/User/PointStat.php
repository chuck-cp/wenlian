<?php
/*
 *  @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 *  @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *  @link https://www.koogua.com
 *
 */

namespace App\Services\Logic\User;

use App\Models\PointHistory as PointHistoryModel;
use App\Repos\PointHistory as PointHistoryRepo;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\UserTrait;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class PointStat extends LogicService
{

    use UserTrait;

    /**
     * @var int
     */
    private $year;

    /**
     * @var int
     */
    private $month;

    public function handle($id)
    {
        $user = $this->checkUserCache($id);

        $params = $this->request->getQuery();

        $this->year = $params['year'] ?? date('Y');
        $this->month = $params['month'] ?? date('m');

        $cache = $this->getCache();

        $keyName = $this->getCacheKey($user->id, $this->year, $this->month);

        $content = $cache->get($keyName);

        if (empty($content)) {

            $repo = new PointHistoryRepo();

            $rows = $repo->findUserMonthlyHistory($user->id, $this->year, $this->month);

            $content = $this->handleStats($rows);

            $lifetime = strtotime('tomorrow') - time();

            $cache->save($keyName, $content, $lifetime);
        }

        return $content;
    }

    /**
     * @param ResultsetInterface|Resultset|PointHistoryModel[] $rows
     * @return array
     */
    protected function handleStats($rows)
    {
        $items = [];

        $dayCount = date('t', strtotime("{$this->year}-{$this->month}"));

        for ($i = 1; $i <= $dayCount; $i++) {
            $items[] = [
                'day' => $i,
                'point' => 0,
            ];
        }

        if ($rows->count() == 0) {
            return $items;
        }

        foreach ($rows as $row) {
            $index = date('d', $row->create_time) - 1;
            $items[$index]['point'] += $row->event_point;
        }

        return $items;
    }

    protected function getCacheKey($userId, $year, $month)
    {
        return sprintf('user_point_stat:%s_%s_%s', $userId, $year, $month);
    }

}
