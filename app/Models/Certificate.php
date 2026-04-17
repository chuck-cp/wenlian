<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://openitem.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use App\Models\KgSale as KgSaleModel;
use Phalcon\Mvc\Model\Behavior\SoftDelete;

class Certificate extends Model
{

    /**
     * 授予类型
     */
    const GRANT_TYPE_AUTO = 1; // 自动
    const GRANT_TYPE_MANUAL = 2; // 人工

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
        ]
    ];

    /**
     * 专题扩展信息
     *
     * @var array
     */
    protected $_topic_info = [
        'topic' => [
            'id' => 0,
            'title' => '',
            'cover' => '',
        ]
    ];

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 证书名称
     *
     * @var string
     */
    public $name = '';

    /**
     * 背景类型
     *
     * @var int
     */
    public $bg_type = 1;

    /**
     * 授予方式
     *
     * @var int
     */
    public $grant_type = 0;

    /**
     * 条目类型
     *
     * @var int
     */
    public $item_type = 0;

    /**
     * 条目编号
     *
     * @var int
     */
    public $item_id = 0;

    /**
     * 条目内容
     *
     * @var array|string
     */
    public $item_info = [];

    /**
     * 扩展属性
     *
     * @var array|string
     */
    public $attrs = [];

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
     * 授予人数
     *
     * @var int
     */
    public $grant_count = 0;

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
        return 'kg_certificate';
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
            if ($this->item_type == KgSaleModel::ITEM_COURSE) {
                $this->item_info = $this->_course_info;
            } elseif ($this->item_type == KgSaleModel::ITEM_EXAM_PAPER) {
                $this->item_info = $this->_exam_paper_info;
            } elseif ($this->item_type == KgSaleModel::ITEM_TOPIC) {
                $this->item_info = $this->_topic_info;
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
        if (is_array($this->attrs)) {
            $this->attrs = kg_json_encode($this->attrs);
        }
    }

    public function afterFetch()
    {
        if (is_string($this->item_info)) {
            $this->item_info = json_decode($this->item_info, true);
        }

        if (is_string($this->attrs)) {
            $this->attrs = json_decode($this->attrs, true);
        }
    }

    public static function grantTypes()
    {
        return [
            self::GRANT_TYPE_AUTO => '自动授予',
            self::GRANT_TYPE_MANUAL => '人工授予',
        ];
    }

    public static function itemTypes()
    {
        return [
            KgSaleModel::ITEM_COURSE => '课程',
            KgSaleModel::ITEM_EXAM_PAPER => '考试',
            KgSaleModel::ITEM_TOPIC => '专题',
        ];
    }

}
