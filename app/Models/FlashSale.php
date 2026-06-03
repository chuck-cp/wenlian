<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use Phalcon\Mvc\Model\Behavior\SoftDelete;

class FlashSale extends Model
{

    /**
     * 状态类型
     */
    const STATUS_PENDING = 1; // 未开始
    const STATUS_ACTIVE = 2; // 进行中
    const STATUS_EXPIRED = 3; //　已结束

    /**
     * 课程扩展信息
     *
     * @var array
     */
    protected $_course_info = [
        'course' => [
            'id' => 0,
            'title' => '',
            'cover' => '',
            'price' => 0,
        ]
    ];

    /**
     * 套餐扩展信息
     *
     * @var array
     */
    protected $_package_info = [
        'package' => [
            'id' => 0,
            'title' => '',
            'cover' => '',
            'price' => 0,
        ]
    ];

    /**
     * 会员扩展信息
     *
     * @var array
     */
    protected $_vip_info = [
        'vip' => [
            'id' => 0,
            'title' => '',
            'cover' => '',
            'price' => 0,
        ]
    ];

    /**
     * 试卷扩展信息
     *
     * @var array
     */
    protected $_exam_paper_info = [
        'exam_paper' => [
            'id' => 0,
            'title' => '',
            'cover' => '',
            'price' => 0,
        ]
    ];

    /**
     * 专栏扩展信息
     *
     * @var array
     */
    protected $_article_info = [
        'course' => [
            'id' => 0,
            'title' => '',
            'cover' => '',
            'price' => 0,
        ]
    ];

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 物品编号
     *
     * @var string
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
     * @var array|string
     */
    public $item_info = [];

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
     * 时间场次
     *
     * @var array|string
     */
    public $schedules = [];

    /**
     * 价格
     *
     * @var float
     */
    public $price = 0.00;

    /**
     * 库存
     *
     * @var int
     */
    public $stock = 0;

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
        return 'kg_flash_sale';
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
        if (empty($this->item_info)) {
            if ($this->item_type == KgProduct::ITEM_COURSE) {
                $this->item_info = $this->_course_info;
            } elseif ($this->item_type == KgProduct::ITEM_PACKAGE) {
                $this->item_info = $this->_package_info;
            } elseif ($this->item_type == KgProduct::ITEM_VIP) {
                $this->item_info = $this->_vip_info;
            } elseif ($this->item_type == KgProduct::ITEM_EXAM_PAPER) {
                $this->item_info = $this->_exam_paper_info;
            } elseif ($this->item_type == KgProduct::ITEM_ARTICLE) {
                $this->item_info = $this->_article_info;
            }
        }

        if (is_array($this->item_info)) {
            $this->item_info = kg_json_encode($this->item_info);
        }

        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        if (is_array($this->item_info)) {
            $this->item_info = kg_json_encode($this->item_info);
        }

        $this->update_time = time();
    }

    public function beforeSave()
    {
        if (is_array($this->schedules)) {
            $this->schedules = kg_json_encode($this->schedules);
        }
    }

    public function afterFetch()
    {
        if (is_string($this->item_info)) {
            $this->item_info = json_decode($this->item_info, true);
        }

        if (is_string($this->schedules)) {
            $this->schedules = json_decode($this->schedules, true);
        }
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

    public static function statusTypes()
    {
        return [
            self::STATUS_PENDING => '未开始',
            self::STATUS_ACTIVE => '进行中',
            self::STATUS_EXPIRED => '已结束',
        ];
    }

    public static function schedules()
    {
        $result = [];

        foreach (range(10, 20, 2) as $hour) {
            $result[] = [
                'name' => sprintf('%02d点', $hour),
                'hour' => sprintf('%02d', $hour),
                'start_time' => sprintf('%02d:%02d:%02d', $hour, 0, 0),
                'end_time' => sprintf('%02d:%02d:%02d', $hour + 1, 59, 59)
            ];
        }

        return $result;
    }

}
