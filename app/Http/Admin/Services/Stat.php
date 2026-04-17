<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Models\KgSale as KgSaleModel;
use App\Repos\Stat as StatRepo;

class Stat extends Service
{

    public function hotSales()
    {
        $type = $this->request->getQuery('type', 'int', KgSaleModel::ITEM_COURSE);
        $year = $this->request->getQuery('year', 'int', date('Y'));
        $month = $this->request->getQuery('month', 'int', date('m'));

        $prev = $this->getPrevMonth($year, $month);
        $yoy = $this->getYoyMonth($year, $month);

        return [
            [
                'month' => sprintf('%02d-%02d', $year, $month),
                'sales' => $this->handleHotSales($type, $year, $month),
            ],
            [
                'month' => sprintf('%02d-%02d', $yoy['year'], $yoy['month']),
                'sales' => $this->handleHotSales($type, $yoy['year'], $yoy['month']),
            ],
            [
                'month' => sprintf('%02d-%02d', $prev['year'], $prev['month']),
                'sales' => $this->handleHotSales($type, $prev['year'], $prev['month']),
            ],
        ];
    }

    public function sales()
    {
        $year = $this->request->getQuery('year', 'int', date('Y'));
        $month = $this->request->getQuery('month', 'int', date('m'));

        $prev = $this->getPrevMonth($year, $month);
        $yoy = $this->getYoyMonth($year, $month);

        $currMonthSales = $this->handleSales($year, $month);
        $prevMonthSales = $this->handleSales($prev['year'], $prev['month']);
        $yoyMonthSales = $this->handleSales($yoy['year'], $yoy['month']);

        $currMonthAmount = array_sum($currMonthSales);
        $prevMonthAmount = array_sum($prevMonthSales);
        $yoyMonthAmount = array_sum($yoyMonthSales);

        $monGrowthRate = $this->calculateGrowthRate($currMonthAmount, $prevMonthAmount);
        $yoyGrowthRate = $this->calculateGrowthRate($currMonthAmount, $yoyMonthAmount);

        $currMonth = sprintf('%02d-%02d', $year, $month);
        $prevMonth = sprintf('%02d-%02d', $prev['year'], $prev['month']);
        $yoyMonth = sprintf('%02d-%02d', $yoy['year'], $yoy['month']);

        $items = [];

        foreach (range(1, 31) as $day) {
            $date = sprintf('%02d', $day);
            $items[] = [
                'date' => $date,
                $currMonth => $currMonthSales[$date] ?? 0,
                $prevMonth => $prevMonthSales[$date] ?? 0,
                $yoyMonth => $yoyMonthSales[$date] ?? 0,
            ];
        }

        return [
            'items' => $items,
            'curr_month_key' => $currMonth,
            'prev_month_key' => $prevMonth,
            'yoy_month_key' => $yoyMonth,
            'curr_month_value' => $currMonthAmount,
            'prev_month_value' => $prevMonthAmount,
            'yoy_month_value' => $yoyMonthAmount,
            'mom_growth_rate' => $monGrowthRate,
            'yoy_growth_rate' => $yoyGrowthRate,
        ];
    }

    public function refunds()
    {
        $year = $this->request->getQuery('year', 'int', date('Y'));
        $month = $this->request->getQuery('month', 'int', date('m'));

        $prev = $this->getPrevMonth($year, $month);
        $yoy = $this->getYoyMonth($year, $month);

        $currMonthRefunds = $this->handleRefunds($year, $month);
        $prevMonthRefunds = $this->handleRefunds($prev['year'], $prev['month']);
        $yoyMonthRefunds = $this->handleRefunds($yoy['year'], $yoy['month']);

        $currMonthAmount = array_sum($currMonthRefunds);
        $prevMonthAmount = array_sum($prevMonthRefunds);
        $yoyMonthAmount = array_sum($yoyMonthRefunds);

        $monGrowthRate = $this->calculateGrowthRate($currMonthAmount, $prevMonthAmount);
        $yoyGrowthRate = $this->calculateGrowthRate($currMonthAmount, $yoyMonthAmount);

        $currMonth = sprintf('%02d-%02d', $year, $month);
        $prevMonth = sprintf('%02d-%02d', $prev['year'], $prev['month']);
        $yoyMonth = sprintf('%02d-%02d', $yoy['year'], $yoy['month']);

        $items = [];

        foreach (range(1, 31) as $day) {
            $date = sprintf('%02d', $day);
            $items[] = [
                'date' => $date,
                $currMonth => $currMonthRefunds[$date] ?? 0,
                $prevMonth => $prevMonthRefunds[$date] ?? 0,
                $yoyMonth => $yoyMonthRefunds[$date] ?? 0,
            ];
        }

        return [
            'items' => $items,
            'curr_month_key' => $currMonth,
            'prev_month_key' => $prevMonth,
            'yoy_month_key' => $yoyMonth,
            'curr_month_value' => $currMonthAmount,
            'prev_month_value' => $prevMonthAmount,
            'yoy_month_value' => $yoyMonthAmount,
            'mom_growth_rate' => $monGrowthRate,
            'yoy_growth_rate' => $yoyGrowthRate,
        ];
    }

    public function registeredUsers()
    {
        $year = $this->request->getQuery('year', 'int', date('Y'));
        $month = $this->request->getQuery('month', 'int', date('m'));

        $prev = $this->getPrevMonth($year, $month);
        $yoy = $this->getYoyMonth($year, $month);

        $currMonthUsers = $this->handleRegisteredUsers($year, $month);
        $prevMonthUsers = $this->handleRegisteredUsers($prev['year'], $prev['month']);
        $yoyMonthUsers = $this->handleRegisteredUsers($yoy['year'], $yoy['month']);

        $currMonthCount = array_sum($currMonthUsers);
        $prevMonthCount = array_sum($prevMonthUsers);
        $yoyMonthCount = array_sum($yoyMonthUsers);

        $monGrowthRate = $this->calculateGrowthRate($currMonthCount, $prevMonthCount);
        $yoyGrowthRate = $this->calculateGrowthRate($currMonthCount, $yoyMonthCount);

        $currMonth = sprintf('%02d-%02d', $year, $month);
        $prevMonth = sprintf('%02d-%02d', $prev['year'], $prev['month']);
        $yoyMonth = sprintf('%02d-%02d', $yoy['year'], $yoy['month']);

        $items = [];

        foreach (range(1, 31) as $day) {
            $date = sprintf('%02d', $day);
            $items[] = [
                'date' => $date,
                $currMonth => $currMonthUsers[$date] ?? 0,
                $prevMonth => $prevMonthUsers[$date] ?? 0,
                $yoyMonth => $yoyMonthUsers[$date] ?? 0,
            ];
        }

        return [
            'items' => $items,
            'curr_month_key' => $currMonth,
            'prev_month_key' => $prevMonth,
            'yoy_month_key' => $yoyMonth,
            'curr_month_value' => $currMonthCount,
            'prev_month_value' => $prevMonthCount,
            'yoy_month_value' => $yoyMonthCount,
            'mom_growth_rate' => $monGrowthRate,
            'yoy_growth_rate' => $yoyGrowthRate,
        ];
    }

    public function onlineUsers()
    {
        $year = $this->request->getQuery('year', 'int', date('Y'));
        $month = $this->request->getQuery('month', 'int', date('m'));

        $prev = $this->getPrevMonth($year, $month);
        $yoy = $this->getYoyMonth($year, $month);

        $currMonthUsers = $this->handleOnlineUsers($year, $month);
        $prevMonthUsers = $this->handleOnlineUsers($prev['year'], $prev['month']);
        $yoyMonthUsers = $this->handleOnlineUsers($yoy['year'], $yoy['month']);

        $currMonthCount = array_sum($currMonthUsers);
        $prevMonthCount = array_sum($prevMonthUsers);
        $yoyMonthCount = array_sum($yoyMonthUsers);

        $monGrowthRate = $this->calculateGrowthRate($currMonthCount, $prevMonthCount);
        $yoyGrowthRate = $this->calculateGrowthRate($currMonthCount, $yoyMonthCount);

        $currMonth = sprintf('%02d-%02d', $year, $month);
        $prevMonth = sprintf('%02d-%02d', $prev['year'], $prev['month']);
        $yoyMonth = sprintf('%02d-%02d', $yoy['year'], $yoy['month']);

        $items = [];

        foreach (range(1, 31) as $day) {
            $date = sprintf('%02d', $day);
            $items[] = [
                'date' => $date,
                $currMonth => $currMonthUsers[$date] ?? 0,
                $prevMonth => $prevMonthUsers[$date] ?? 0,
                $yoyMonth => $yoyMonthUsers[$date] ?? 0,
            ];
        }

        return [
            'items' => $items,
            'curr_month_key' => $currMonth,
            'prev_month_key' => $prevMonth,
            'yoy_month_key' => $yoyMonth,
            'curr_month_value' => $currMonthCount,
            'prev_month_value' => $prevMonthCount,
            'yoy_month_value' => $yoyMonthCount,
            'mom_growth_rate' => $monGrowthRate,
            'yoy_growth_rate' => $yoyGrowthRate,
        ];
    }

    public function getYearOptions()
    {
        $end = date('Y');

        $start = $end - 10;

        return range($start, $end);
    }

    public function getMonthOptions()
    {
        $options = [];

        foreach (range(1, 12) as $value) {
            $options[] = sprintf('%02d', $value);
        }
        return $options;
    }

    protected function isCurrMonth($year, $month)
    {
        $yearOk = date('Y') == $year;
        $monthOk = date('m') == $month;

        return $yearOk && $monthOk;
    }

    protected function getLifetime()
    {
        return strtotime('tomorrow') - time();
    }

    protected function getPrevMonth($year, $month)
    {
        $currentMonthTime = strtotime("{$year}-{$month}");

        $prevMonthTime = strtotime('-1 month', $currentMonthTime);

        return [
            'year' => date('Y', $prevMonthTime),
            'month' => date('m', $prevMonthTime),
        ];
    }

    /**
     * 往年同月 yoy (year-over-year)
     */
    protected function getYoyMonth($year, $month)
    {
        return [
            'year' => $year - 1,
            'month' => $month,
        ];
    }

    protected function getMonthDates($year, $month)
    {
        $startTime = strtotime("{$year}-{$month}-01");

        $days = date('t', $startTime);

        $result = [];

        foreach (range(1, $days) as $day) {
            $result[] = sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        return $result;
    }

    function calculateGrowthRate($currentValue, $previousValue, $precision = 2)
    {
        if ($previousValue == 0) {
            $growthRate = $currentValue > 0 ? null : 0;
        } else {
            $growthRate = ($currentValue - $previousValue) / $previousValue * 100;
        }

        if ($growthRate !== null) {
            return round($growthRate, $precision);
        } else {
            return 'N/A';
        }
    }

    protected function handleHotSales($type, $year, $month)
    {
        $keyName = "stat_month_hot_sales:{$type}_{$year}_{$month}";

        $cache = $this->getCache();

        $items = $cache->get($keyName);

        if (!$items) {

            $statRepo = new StatRepo();

            $orders = $statRepo->findMonthlyOrders($type, $year, $month);

            $items = [];

            if ($orders->count() > 0) {

                foreach ($orders as $order) {
                    $key = $order->item_id;
                    if (!isset($items[$key])) {
                        $items[$key] = [
                            'title' => $order->subject,
                            'total_count' => 1,
                            'total_amount' => $order->amount,
                        ];
                    } else {
                        $items[$key]['total_count'] += 1;
                        $items[$key]['total_amount'] += $order->amount;
                    }
                }

                $totalCount = array_column($items, 'total_count');

                array_multisort($totalCount, SORT_DESC, $items);
            }

            $queryMonth = "{$year}-{$month}";

            $currMonth = date('Y-m');

            if ($queryMonth < $currMonth) {
                $cache->save($keyName, $items, 86400);
            } else {
                $cache->save($keyName, $items, 3600);
            }
        }

        return $items;
    }

    protected function handleSales($year, $month)
    {
        $keyName = "stat_month_sales:{$year}_{$month}";

        $redis = $this->getRedis();

        $list = $redis->hGetAll($keyName);

        $statRepo = new StatRepo();

        $currDate = date('Y-m-d');
        $currDay = date('d');

        if (!$list) {
            $dates = $this->getMonthDates($year, $month);
            foreach ($dates as $date) {
                $key = substr($date, -2);
                if ($date < $currDate) {
                    $list[$key] = $statRepo->sumDailySales($date);
                } else {
                    $list[$key] = 0;
                }
            }
            $redis->hMSet($keyName, $list);
            $redis->expire($keyName, $this->getLifetime());
        }

        if ($this->isCurrMonth($year, $month)) {
            $list[$currDay] = $statRepo->sumDailySales($currDate);
        }

        return $list;
    }

    protected function handleRefunds($year, $month)
    {
        $keyName = "stat_month_refunds:{$year}_{$month}";

        $redis = $this->getRedis();

        $list = $redis->hGetAll($keyName);

        $statRepo = new StatRepo();

        $currDate = date('Y-m-d');
        $currDay = date('d');

        if (!$list) {
            $dates = $this->getMonthDates($year, $month);
            foreach ($dates as $date) {
                $key = substr($date, -2);
                if ($date < $currDate) {
                    $list[$key] = $statRepo->sumDailyRefunds($date);
                } else {
                    $list[$key] = 0;
                }
            }
            $redis->hMSet($keyName, $list);
            $redis->expire($keyName, $this->getLifetime());
        }

        if ($this->isCurrMonth($year, $month)) {
            $list[$currDay] = $statRepo->sumDailyRefunds($currDate);
        }

        return $list;
    }

    protected function handleRegisteredUsers($year, $month)
    {
        $keyName = "stat_month_registered_users:{$year}_{$month}";

        $redis = $this->getRedis();

        $list = $redis->hGetAll($keyName);

        $statRepo = new StatRepo();

        $currDate = date('Y-m-d');
        $currDay = date('d');

        if (!$list) {
            $dates = $this->getMonthDates($year, $month);
            foreach ($dates as $date) {
                $key = substr($date, -2);
                if ($date < $currDate) {
                    $list[$key] = $statRepo->countDailyRegisteredUsers($date);
                } else {
                    $list[$key] = 0;
                }
            }
            $redis->hMSet($keyName, $list);
            $redis->expire($keyName, $this->getLifetime());
        }

        if ($this->isCurrMonth($year, $month)) {
            $list[$currDay] = $statRepo->countDailyRegisteredUsers($currDate);
        }

        return $list;
    }

    protected function handleOnlineUsers($year, $month)
    {
        $keyName = "stat_month_online_users:{$year}_{$month}";

        $redis = $this->getRedis();

        $list = $redis->hGetAll($keyName);

        $statRepo = new StatRepo();

        $currDate = date('Y-m-d');
        $currDay = date('d');

        if (!$list) {
            $dates = $this->getMonthDates($year, $month);
            foreach ($dates as $date) {
                $key = substr($date, -2);
                if ($date < $currDate) {
                    $list[$key] = $statRepo->countDailyOnlineUsers($date);
                } else {
                    $list[$key] = 0;
                }
            }
            $redis->hMSet($keyName, $list);
            $redis->expire($keyName, $this->getLifetime());
        }

        if ($this->isCurrMonth($year, $month)) {
            $list[$currDay] = $statRepo->countDailyOnlineUsers($currDate);
        }

        return $list;
    }

}
