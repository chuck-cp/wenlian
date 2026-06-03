<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Library\Utils\Lock as LockUtil;
use App\Models\Trade as TradeModel;
use App\Services\Pay\Alipay as AlipayService;
use App\Services\Pay\Wxpay as WxpayService;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class CloseTradeTask extends Task
{

    public function mainAction()
    {
        $taskLockKey = $this->getTaskLockKey();

        $taskLockId = LockUtil::addLock($taskLockKey, 300);

        if (!$taskLockId) return;

        $trades = $this->findTrades();

        echo sprintf('pending trades: %s', $trades->count()) . PHP_EOL;

        if ($trades->count() == 0) return;

        echo '------ start close trade ------' . PHP_EOL;

        foreach ($trades as $trade) {
            try {
                if ($trade->channel == TradeModel::CHANNEL_ALIPAY) {
                    $this->handleAlipayTrade($trade);
                } elseif ($trade->channel == TradeModel::CHANNEL_WXPAY) {
                    $this->handleWxpayTrade($trade);
                }
            } catch (\Exception $e) {
                $logger = $this->getLogger('trade');
                $logger->error('Close Trade Task Exception: ' . kg_json_encode([
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'message' => $e->getMessage(),
                        'trade' => $trade,
                    ]));
            }
        }

        echo '------ end close trade ------' . PHP_EOL;

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    /**
     * 处理支付宝交易
     *
     * @param TradeModel $trade
     */
    protected function handleAlipayTrade(TradeModel $trade)
    {
        $alipay = new AlipayService();

        $alipayTrade = $alipay->find($trade->sn);

        if (!$alipayTrade) return;

        /**
         * 异步通知接收异常，补救漏网
         */
        if ($alipayTrade['trade_status'] == 'TRADE_SUCCESS') {
            $this->eventsManager->fire('Trade:afterPay', $this, $trade);
            return;
        }

        if ($alipayTrade['trade_status'] != 'WAIT_BUYER_PAY') {
            return;
        }

        if (!$alipay->close($trade->sn)) return;

        $trade->status = TradeModel::STATUS_CLOSED;

        $trade->update();
    }

    /**
     * 处理微信交易
     *
     * @param TradeModel $trade
     */
    protected function handleWxpayTrade(TradeModel $trade)
    {
        $wxpay = new WxpayService();

        $wxpayTrade = $wxpay->find($trade->sn);

        if (!$wxpayTrade) return;

        /**
         * 异步通知接收异常，补救漏网
         */
        if ($wxpayTrade['trade_state'] == 'SUCCESS') {
            $this->eventsManager->fire('Trade:afterPay', $this, $trade);
            return;
        }

        if ($wxpayTrade['trade_state'] != 'NOTPAY') {
            return;
        }

        if (!$wxpay->close($trade->sn)) return;

        $trade->status = TradeModel::STATUS_CLOSED;

        $trade->update();
    }

    /**
     * 查找待关闭交易
     *
     * @param int $limit
     * @return ResultsetInterface|Resultset|TradeModel[]
     */
    protected function findTrades($limit = 50)
    {
        $status = TradeModel::STATUS_PENDING;

        $createTime = time() - 15 * 60;

        return TradeModel::query()
            ->where('status = :status:', ['status' => $status])
            ->andWhere('create_time < :create_time:', ['create_time' => $createTime])
            ->limit($limit)
            ->execute();
    }

}
