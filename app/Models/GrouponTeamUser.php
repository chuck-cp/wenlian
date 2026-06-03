<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

class GrouponTeamUser extends Model
{

    /**
     * 状态类型
     */
    const STATUS_PENDING = 1; // 待启动（未支付）
    const STATUS_FINISHED = 2; // 已完成（已支付）
    const STATUS_CLOSED = 3; // 已关闭

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
     * 队伍编号
     *
     * @var int
     */
    public $team_id = 0;

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
     * 状态标识
     *
     * @var int
     */
    public $status = self::STATUS_PENDING;

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
        return 'kg_groupon_team_user';
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
