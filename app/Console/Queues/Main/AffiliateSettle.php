<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Queues\Main;

use App\Models\CashHistory as CashHistoryModel;
use App\Models\Order as OrderModel;
use App\Models\Task as TaskModel;
use App\Repos\Distribution as DistributionRepo;
use App\Repos\Order as OrderRepo;
use App\Repos\User as UserRepo;
use App\Repos\UserReferer as UserRefererRepo;
use App\Services\Logic\Notice\External\DistSuccess as DistSuccessNotice;
use App\Traits\Service as ServiceTrait;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class AffiliateSettle extends Injectable
{

    use ServiceTrait;

    public function handle(TaskModel $task)
    {
        echo '------ start affiliate settle task ------' . PHP_EOL;

        $orderRepo = new OrderRepo();

        $order = $orderRepo->findById($task->item_id);

        try {

            $this->db->begin();

            $this->handleAffiliateAccount($order);
            $this->handleDistributionNotice($order);

            $task->status = TaskModel::STATUS_FINISHED;
            $task->update();

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $task->status = TaskModel::STATUS_FAILED;
            $task->update();

            $logger = $this->getLogger('affiliate');

            $logger->error('Affiliate Settle Task Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'task' => $task->toArray(),
                ]));
        }

        echo '------ end affiliate settle task ------' . PHP_EOL;
    }

    protected function handleAffiliateAccount(OrderModel $order)
    {
        $setting = $this->getSettings('affiliate');

        if ($setting['v1_com_enabled'] == 0) return;

        $comAmount = $this->getOrderComAmount($order);

        /**
         * 一级用户佣金处理
         */
        if ($setting['v1_com_enabled'] == 1) {
            $this->createCashHistory($order, 1, $comAmount['v1']);
        }

        /**
         * 二级用户佣金处理
         */
        if ($setting['v2_com_enabled'] == 1) {
            $this->createCashHistory($order, 2, $comAmount['v2']);
        }

        /**
         * 三级用户佣金处理
         */
        if ($setting['v3_com_enabled'] == 1) {
            $this->createCashHistory($order, 3, $comAmount['v3']);
        }
    }

    protected function handleDistributionNotice(OrderModel $order)
    {
        $records = $this->findOrderCashHistory($order->id);

        if ($records->count() == 0) return;

        foreach ($records as $record) {
            $notice = new DistSuccessNotice();
            $notice->createTask($record);
        }
    }

    protected function createCashHistory(OrderModel $order, $parentLevel, $eventAmount)
    {
        $userRepo = new UserRepo();

        $userRefererRepo = new UserRefererRepo();

        $userReferer = $userRefererRepo->findByUserParentLevel($order->owner_id, $parentLevel);

        $orderOwner = $userRepo->findById($order->owner_id);

        if ($userReferer) {

            $user = $userRepo->findById($userReferer->parent_id);

            $userBalance = $userRepo->findUserBalance($user->id);

            $userBalance->cash += $eventAmount;

            $userBalance->update();

            $eventInfo = [
                'order' => [
                    'sn' => $order->sn,
                    'subject' => $order->subject,
                    'amount' => $order->amount,
                ],
                'referer' => [
                    'id' => $orderOwner->id,
                    'name' => $orderOwner->name,
                    'level' => $parentLevel,
                ],
            ];

            $cashHistory = new CashHistoryModel();

            $cashHistory->user_id = $user->id;
            $cashHistory->user_name = $user->name;
            $cashHistory->event_id = $order->id;
            $cashHistory->event_type = CashHistoryModel::EVENT_AFFILIATE_SETTLE;
            $cashHistory->event_info = $eventInfo;
            $cashHistory->event_amount = $eventAmount;

            $cashHistory->create();
        }
    }

    protected function getOrderComAmount(OrderModel $order)
    {
        $settings = $this->getSettings('affiliate');

        $comRate = [
            'v1_com_rate' => $settings['v1_com_rate'],
            'v2_com_rate' => $settings['v2_com_rate'],
            'v3_com_rate' => $settings['v3_com_rate'],
        ];

        $itemComRate = $this->getItemComRate($order->item_id, $order->item_type);

        if ($itemComRate) {
            $comRate = $itemComRate;
        }

        return [
            'v1' => round($order->amount * $comRate['v1_com_rate'] / 100, 2),
            'v2' => round($order->amount * $comRate['v2_com_rate'] / 100, 2),
            'v3' => round($order->amount * $comRate['v3_com_rate'] / 100, 2),
        ];
    }

    protected function getItemComRate($itemId, $itemType)
    {
        $distRepo = new DistributionRepo();

        $dist = $distRepo->findItemDistribution($itemId, $itemType);

        if (!$dist) return null;

        if ($dist->published == 0) return null;

        if ($dist->deleted == 1) return null;

        if ($dist->start_time > time()) return null;

        if ($dist->end_time < time()) return null;

        return [
            'v1_com_rate' => $dist->v1_com_rate,
            'v2_com_rate' => $dist->v2_com_rate,
            'v3_com_rate' => $dist->v3_com_rate,
        ];
    }

    /**
     * 查找分销结算记录
     *
     * @param int $orderId
     * @return ResultsetInterface|Resultset|CashHistoryModel[]
     */
    protected function findOrderCashHistory($orderId)
    {
        $eventType = CashHistoryModel::EVENT_AFFILIATE_SETTLE;

        return CashHistoryModel::query()
            ->where('event_id = :event_id:', ['event_id' => $orderId])
            ->andWhere('event_type = :event_type:', ['event_type' => $eventType])
            ->execute();
    }

}
