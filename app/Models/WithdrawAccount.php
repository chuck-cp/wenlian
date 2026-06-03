<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use Phalcon\Mvc\Model\Behavior\SoftDelete;

class WithdrawAccount extends Model
{

    /**
     * 服务商类型
     */
    const CHANNEL_ALIPAY = 1;
    const CHANNEL_WECHAT = 2;

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
     * 订单编号
     *
     * @var int
     */
    public $order_id = 0;

    /**
     * 平台类型
     *
     * @var int
     */
    public $channel = 0;

    /**
     * 买家姓名
     *
     * @var string
     */
    public $name = '';

    /**
     * 买家帐号（支付宝，微信帐号）
     *
     * @var string
     */
    public $account = '';

    /**
     * 买家标识（支付宝：buyer_id, 微信：openid）
     *
     * @var string
     */
    public $identity = '';

    /**
     * 默认标识
     *
     * @var int
     */
    public $master = 0;

    /**
     * 验证标识
     *
     * @var int
     */
    public $verified = 0;

    /**
     * 删除标识
     *
     * @var int
     */
    public $deleted = 0;

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
        return 'kg_withdraw_account';
    }

    public function initialize()
    {
        parent::initialize();

        $this->addBehavior(
            new SoftDelete([
                'field' => 'deleted',
                'value' => 1,
            ])
        );
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        $this->update_time = time();
    }

    public static function channelTypes()
    {
        return [
            self::CHANNEL_ALIPAY => '支付宝',
            self::CHANNEL_WECHAT => '微信',
        ];
    }

    public static function getEnabledChannels()
    {
        $settings = kg_setting('withdraw');

        $channels = json_decode($settings['channels'], true);

        $result = [];

        if (in_array('alipay', $channels)) {
            $result[self::CHANNEL_ALIPAY] = '支付宝';
        }

        if (in_array('wechat', $channels)) {
            $result[self::CHANNEL_WECHAT] = '微信';
        }

        return $result;
    }

}
