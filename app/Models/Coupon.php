<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use Phalcon\Mvc\Model\Behavior\SoftDelete;
use Phalcon\Text;

class Coupon extends Model
{

    /**
     * 优惠类型
     */
    const TYPE_FIXED_AMOUNT = 1; // 固定额（例如: 20元）
    const TYPE_PERCENTAGE = 2; // 百分比（例如: 20%）

    /**
     * 状态类型
     */
    const STATUS_PENDING = 1; // 未开始
    const STATUS_ACTIVE = 2; // 进行中
    const STATUS_EXPIRED = 3; //　已结束

    /**
     * @var array 满减扩展
     */
    protected $_reward_attrs = [
        'deduct_amount' => 0.00, // 面额（抵扣额度）
    ];

    /**
     * @var array 折扣扩展
     */
    protected $_discount_attrs = [
        'max_deduct_amount' => 0.00, // 最大抵扣额
        'discount_rate' => 0, // 折扣率（1-100）
    ];

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 编码
     *
     * @var string
     */
    public $code = '';

    /**
     * 名称
     *
     * @var string
     */
    public $name = '';

    /**
     * 类型
     *
     * @var int
     */
    public $type = 0;

    /**
     * 扩展属性
     *
     * @var array
     */
    public $attrs = [];

    /**
     * 最低消费
     *
     * @var float
     */
    public $consume_limit = 0.00;

    /**
     * 发行限量
     *
     * @var int
     */
    public $total_usage = 0;

    /**
     * 领取限额
     *
     * @var int
     */
    public $user_usage = 1;

    /**
     * 允许领取
     *
     * @var int
     */
    public $private = 0;

    /**
     * 物品编号
     *
     * @var int
     */
    public $item_id = 0;

    /**
     * 物品类型
     *
     * @var int
     */
    public $item_type = 0;

    /**
     * 物品信息
     *
     * @var array
     */
    public $item_info = [];

    /**
     * 发布标识
     *
     * @var int
     */
    public $published = 0;

    /**
     * 删除标识
     *
     * @var int
     */
    public $deleted = 0;

    /**
     * 申领人数
     *
     * @var int
     */
    public $claim_count = 0;

    /**
     * 使用次数
     *
     * @var int
     */
    public $apply_count = 0;

    /**
     * 开始时间
     *
     * @var int
     */
    public $start_time = 0;

    /**
     * 结束时间
     *
     * @var int
     */
    public $end_time = 0;

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
        return 'kg_coupon';
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
        if (empty($this->attrs)) {
            if ($this->type == self::TYPE_FIXED_AMOUNT) {
                $this->attrs = $this->_reward_attrs;
            } elseif ($this->type == self::TYPE_PERCENTAGE) {
                $this->attrs = $this->_discount_attrs;
            }
        }

        if (is_array($this->attrs)) {
            $this->attrs = kg_json_encode($this->attrs);
        }

        $this->code = Text::random(Text::RANDOM_ALNUM, 8);

        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        if (is_array($this->attrs)) {
            $this->attrs = kg_json_encode($this->attrs);
        }

        $this->update_time = time();
    }

    public function beforeSave()
    {
        if (is_array($this->item_info)) {
            $this->item_info = kg_json_encode($this->item_info);
        }
    }

    public function afterFetch()
    {
        if (is_string($this->attrs)) {
            $this->attrs = json_decode($this->attrs, true);
        }

        if (is_string($this->item_info)) {
            $this->item_info = json_decode($this->item_info, true);
        }
    }

    public static function types()
    {
        return [
            self::TYPE_FIXED_AMOUNT => '满减',
            self::TYPE_PERCENTAGE => '折扣',
        ];
    }

    public static function statusTypes()
    {
        return [
            self::STATUS_PENDING => '未开始',
            self::STATUS_ACTIVE => '进行中',
            self::STATUS_EXPIRED => '已结束',
        ];
    }

    public static function itemTypes()
    {
        return [
            KgProduct::ITEM_COURSE => '课程',
            KgProduct::ITEM_PACKAGE => '套餐',
            KgProduct::ITEM_EXAM_PAPER => '试卷',
            KgProduct::ITEM_ARTICLE => '专栏',
            KgProduct::ITEM_VIP => '会员',
        ];
    }

}
