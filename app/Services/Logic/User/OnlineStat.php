<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User;

use App\Library\Paginator\Query as PagerQuery;
use App\Repos\Online as OnlineRepo;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\UserTrait;

class OnlineStat extends LogicService
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

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $this->year = $params['year'] ?? date('Y');
        $this->month = $params['month'] ?? date('m');

        $cache = $this->getCache();

        $keyName = $this->getCacheKey($user->id, $this->year, $this->month);

        $content = $cache->get($keyName);

        if (empty($content)) {

            $params['user_id'] = $user->id;

            $startTime = strtotime("{$this->year}-{$this->month}");

            $endTime = strtotime('+1 month', $startTime);

            $params['create_time'] = [
                date('Y-m-d', $startTime),
                date('Y-m-d', $endTime),
            ];

            $sort = $pagerQuery->getSort();
            $page = $pagerQuery->getPage();
            $limit = $pagerQuery->getLimit();

            $repo = new OnlineRepo();

            $pager = $repo->paginate($params, $sort, $page, $limit);

            $content = $this->handleStats($pager);

            $lifetime = strtotime('tomorrow') - time();

            $cache->save($keyName, $content, $lifetime);
        }

        return $content;
    }

    protected function handleStats($pager)
    {
        $items = [];

        $dayCount = date('t', strtotime("{$this->year}-{$this->month}"));

        for ($i = 1; $i <= $dayCount; $i++) {
            $items[] = [
                'day' => $i,
                'online' => 0,
                'active_time' => 0,
            ];
        }

        if ($pager->total_items == 0) {
            return $items;
        }

        foreach ($pager->items as $item) {
            $index = date('d', $item->create_time) - 1;
            $items[$index]['online'] = 1;
            $items[$index]['active_time'] = $item->active_time;
        }

        return $items;
    }

    protected function getCacheKey($userId, $year, $month)
    {
        return sprintf('user_online_stat:%s_%s_%s', $userId, $year, $month);
    }

}
