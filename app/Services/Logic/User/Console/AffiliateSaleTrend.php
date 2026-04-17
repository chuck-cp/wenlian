<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Models\CashHistory as CashHistoryModel;
use App\Repos\CashHistory as CashHistoryRepo;
use App\Services\Logic\Service as LogicService;

class AffiliateSaleTrend extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser(true);

        $month = $this->request->getQuery('month', 'string', date('Y-m'));

        return $this->handleSaleTrend($user->id, $month);
    }

    public function handleSaleTrend($userId, $month)
    {
        $dayCount = date('t', strtotime($month));

        $result = [
            'total_amount' => 0.00,
            'items' => [],
        ];

        for ($i = 1; $i <= $dayCount; $i++) {
            $result['items'][] = [
                'day' => $i,
                'amount' => 0.00,
            ];
        }

        $historyRepo = new CashHistoryRepo();

        $rows = $historyRepo->findUserMonthlyHistory($userId, $month);

        if ($rows->count() == 0) return $result;

        foreach ($rows as $row) {
            if ($row->event_type == CashHistoryModel::EVENT_AFFILIATE_SETTLE) {
                $day = date('d', $row->create_time);
                $result['total_amount'] += $row->event_amount;
                $result['items'][$day - 1]['amount'] += $row->event_amount;
            }
        }

        return $result;
    }

}
