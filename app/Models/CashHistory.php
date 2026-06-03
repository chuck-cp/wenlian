<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

class CashHistory extends Model
{

    /**
     * 事件类型
     */
    const EVENT_WITHDRAW_APPLY = 1; // 申请提现(-)
    const EVENT_AFFILIATE_SETTLE = 2; // 分销结算(+)
    const EVENT_LOTTERY_PRIZE = 3; // 抽奖奖励(+)
    const EVENT_WITHDRAW_REFUND = 4; // 提现退款(+)

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 用户编号
     *
     * @var int
     */
    public $user_id = 0;

    /**
     * 用户名称
     *
     * @var int
     */
    public $user_name = '';

    /**
     * 事件编号
     *
     * @var int
     */
    public $event_id = 0;

    /**
     * 事件类型
     *
     * @var int
     */
    public $event_type = 0;

    /**
     * 事件内容
     *
     * @var array|string
     */
    public $event_info = [];

    /**
     * 事件金额
     *
     * @var float
     */
    public $event_amount = 0.00;

    /**
     * 创建时间
     *
     * @var int
     */
    public $create_time = 0;

    /**
     * 更新时间
     *
     * @var int
     */
    public $update_time = 0;

    public function getSource(): string
    {
        return 'kg_cash_history';
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        $this->update_time = time();
    }

    public function beforeSave()
    {
        if (is_array($this->event_info)) {
            $this->event_info = kg_json_encode($this->event_info);
        }
    }

    public function afterFetch()
    {
        if (is_string($this->event_info)) {
            $this->event_info = json_decode($this->event_info, true);
        }
    }

    public static function eventTypes()
    {
        return [
            self::EVENT_WITHDRAW_APPLY => '申请提现',
            self::EVENT_AFFILIATE_SETTLE => '商品分销',
            self::EVENT_LOTTERY_PRIZE => '抽奖奖励',
        ];
    }

}
