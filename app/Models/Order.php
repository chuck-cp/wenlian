<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use Phalcon\Mvc\Model\Behavior\SoftDelete;

class Order extends Model
{

    /**
     * 促销类型
     */
    const PROMOTION_FLASH_SALE = 1; // 秒杀
    const PROMOTION_COUPON = 2; // 优惠券
    const PROMOTION_GROUPON = 3; // 拼团

    /**
     * 状态类型
     */
    const STATUS_PENDING = 1; // 待支付
    const STATUS_DELIVERING = 2; // 发货中
    const STATUS_FINISHED = 3; // 已完成
    const STATUS_CLOSED = 4; // 已关闭
    const STATUS_REFUNDED = 5; // 已退款

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 序号
     *
     * @var string
     */
    public $sn = '';

    /**
     * 主题
     *
     * @var string
     */
    public $subject = '';

    /**
     * 金额
     *
     * @var float
     */
    public $amount = 0.00;

    /**
     * 用户编号
     *
     * @var int
     */
    public $owner_id = 0;

    /**
     * 条目编号
     *
     * @var int
     */
    public $item_id = 0;

    /**
     * 条目类型
     *
     * @var int
     */
    public $item_type = 0;

    /**
     * 条目信息
     *
     * @var array|string
     */
    public $item_info = [];

    /**
     * 促销编号
     *
     * @var int
     */
    public $promotion_id = 0;

    /**
     * 促销类型
     *
     * @var int
     */
    public $promotion_type = 0;

    /**
     * 促销信息
     *
     * @var array|string
     */
    public $promotion_info = [];

    /**
     * 终端类型
     *
     * @var int
     */
    public $client_type = 0;

    /**
     * 终端IP
     *
     * @var string
     */
    public $client_ip = '';

    /**
     * 状态类型
     *
     * @var int
     */
    public $status = self::STATUS_PENDING;

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
        return 'kg_order';
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
        $this->sn = $this->getOrderSn();

        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        $this->update_time = time();
    }

    public function beforeSave()
    {
        if (is_array($this->item_info)) {
            $this->item_info = kg_json_encode($this->item_info);
        }

        if (is_array($this->promotion_info)) {
            $this->promotion_info = kg_json_encode($this->promotion_info);
        }
    }

    public function afterSave()
    {
        if ($this->hasUpdated('status')) {
            $orderStatus = new OrderStatus();
            $orderStatus->order_id = $this->id;
            $orderStatus->status = $this->getSnapshotData()['status'];
            $orderStatus->create();
        }
    }

    public function afterFetch()
    {
        $this->amount = (float)$this->amount;

        if (is_string($this->item_info)) {
            $this->item_info = json_decode($this->item_info, true);
        }

        if (is_string($this->promotion_info)) {
            $this->promotion_info = json_decode($this->promotion_info, true);
        }
    }

    public static function itemTypes()
    {
        return [
            KgSale::ITEM_COURSE => '课程',
            KgSale::ITEM_PACKAGE => '套餐',
            KgSale::ITEM_EXAM_PAPER => '试卷',
            KgSale::ITEM_ARTICLE => '专栏',
            KgSale::ITEM_VIP => '会员',
            KgSale::ITEM_PAY_ACCOUNT_VERIFY => '账户验证',
            KgSale::ITEM_PAY_TEST => '支付测试',
        ];
    }

    public static function promotionTypes()
    {
        return [
            self::PROMOTION_FLASH_SALE => '秒杀',
            self::PROMOTION_COUPON => '优惠券',
            self::PROMOTION_GROUPON => '拼团',
        ];
    }

    public static function statusTypes()
    {
        return [
            self::STATUS_PENDING => '待支付',
            self::STATUS_DELIVERING => '发货中',
            self::STATUS_FINISHED => '已完成',
            self::STATUS_CLOSED => '已关闭',
            self::STATUS_REFUNDED => '已退款',
        ];
    }

    protected function getOrderSn()
    {
        $sn = date('YmdHis') . rand(1000, 9999);

        $order = self::findFirst([
            'conditions' => 'sn = :sn:',
            'bind' => ['sn' => $sn],
        ]);

        if (!$order) return $sn;

        return $this->getOrderSn();
    }

}
