<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

class GrouponTeam extends Model
{

    /**
     * 状态类型
     */
    const STATUS_PENDING = 1; // 待启动
    const STATUS_ACTIVE = 2; // 进行中
    const STATUS_FINISHED = 3; // 已完成
    const STATUS_CLOSED = 4; // 已关闭

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 团购编号
     *
     * @var int
     */
    public $groupon_id = 0;

    /**
     * 团长编号
     *
     * @var int
     */
    public $leader_id = 0;

    /**
     * 状态标识
     *
     * @var int
     */
    public $status = self::STATUS_PENDING;

    /**
     * 目标订单数
     *
     * @var int
     */
    public $target_order_count = 0;

    /**
     * 完成订单数
     *
     * @var int
     */
    public $finish_order_count = 0;

    /**
     * 过期时间
     *
     * @var int
     */
    public $expire_time = 0;

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
        return 'kg_groupon_team';
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        $this->update_time = time();
    }

}
