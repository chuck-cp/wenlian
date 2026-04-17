<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use Phalcon\Mvc\Model\Behavior\SoftDelete;

class CouponUser extends Model
{

    /**
     * 获取途径
     */
    const CHANNEL_COLLECT = 1; // 直接领取
    const CHANNEL_PRESENT = 2; // 后台转赠
    const CHANNEL_ORDER = 3; // 订单领取
    const CHANNEL_ACTIVITY = 4; // 活动领取

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 优惠券编号
     *
     * @var int
     */
    public $coupon_id = 0;

    /**
     * 用户编号
     *
     * @var int
     */
    public $user_id = 0;

    /**
     * 获取途径
     *
     * @var int
     */
    public $channel = 0;

    /**
     * 删除标识
     *
     * @var int
     */
    public $deleted = 0;

    /**
     * 使用次数
     *
     * @var int
     */
    public $apply_count = 0;

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
        return 'kg_coupon_user';
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

    public static function channels()
    {
        return [
            self::CHANNEL_COLLECT => '直接领取',
            self::CHANNEL_PRESENT => '后台转赠',
            self::CHANNEL_ORDER => '订单领取',
            self::CHANNEL_ACTIVITY => '活动领取',
        ];
    }

}
