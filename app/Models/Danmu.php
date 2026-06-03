<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use Phalcon\Mvc\Model\Behavior\SoftDelete;

class Danmu extends Model
{

    /**
     * 位置类型
     */
    const POS_ROLL = 0; // 滚动
    const POS_TOP = 1; // 顶部
    const POS_BOTTOM = 2; // 底部

    /**
     * 发布状态
     */
    const PUBLISH_PENDING = 1; // 审核中
    const PUBLISH_APPROVED = 2; // 已发布
    const PUBLISH_REJECTED = 3; // 未通过

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 章节编号
     *
     * @var int
     */
    public $chapter_id = 0;

    /**
     * 用户编号
     *
     * @var int
     */
    public $owner_id = 0;

    /**
     * 内容
     *
     * @var string
     */
    public $text = '';

    /**
     * 颜色
     *
     * @var string
     */
    public $color = '#ffffff';

    /**
     * 大小
     *
     * @var int
     */
    public $size = 12;

    /**
     * 类型（0:滚动，1:顶部，2:底部）
     *
     * @var int
     */
    public $type = 0;

    /**
     * 时间轴
     *
     * @var int
     */
    public $time = 0;

    /**
     * 发布标识
     *
     * @var int
     */
    public $published = self::PUBLISH_PENDING;

    /**
     * 删除标识
     *
     * @var int
     */
    public $deleted = 0;

    /**
     * 终端类型
     *
     * @var integer
     */
    public $client_type = 0;

    /**
     * 终端IP
     *
     * @var string
     */
    public $client_ip = '';

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
        return 'kg_danmu';
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

    public static function posTypes()
    {
        return [
            self::POS_ROLL => '滚动',
            self::POS_TOP => '顶部',
            self::POS_BOTTOM => '底部',
        ];
    }

    public static function publishTypes()
    {
        return [
            self::PUBLISH_PENDING => '审核中',
            self::PUBLISH_APPROVED => '已发布',
            self::PUBLISH_REJECTED => '未通过',
        ];
    }

}
