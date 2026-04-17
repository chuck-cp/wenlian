<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

class ExamPaperUser extends Model
{

    /**
     * 状态类型
     */
    const STATUS_PENDING = 1; // 未开始
    const STATUS_ACTIVE = 2; // 进行中
    const STATUS_WAITING = 3; // 待批改
    const STATUS_FINISHED = 4; // 已完成

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 试卷编号
     *
     * @var int
     */
    public $paper_id = 0;

    /**
     * 用户编号
     *
     * @var int
     */
    public $user_id = 0;

    /**
     * 来源类型
     *
     * @var int
     */
    public $source_type = 0;

    /**
     * 判卷类型
     *
     * @var int
     */
    public $grade_type = 0;

    /**
     * 过期时间
     *
     * @var int
     */
    public $expiry_time = 0;

    /**
     * 试卷时长（秒）
     *
     * @var int
     */
    public $paper_duration = 0;

    /**
     * 用户时长（秒）
     *
     * @var int
     */
    public $user_duration = 0;

    /**
     * 试卷总分
     *
     * @var int
     */
    public $paper_score = 0;

    /**
     * 及格分数
     *
     * @var int
     */
    public $pass_score = 0;

    /**
     * 用户得分
     *
     * @var int
     */
    public $user_score = 0;

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
     * 首秀标识
     *
     * @var int
     */
    public $debut = 0;

    /**
     * 状态标识
     *
     * @var int
     */
    public $status = self::STATUS_PENDING;

    /**
     * 通过标识
     *
     * @var int
     */
    public $passed = 0;

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
        return 'kg_exam_paper_user';
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        $this->update_time = time();
    }

    public static function sourceTypes()
    {
        return KgOwnership::sourceTypes();
    }

    public static function statusTypes()
    {
        return [
            self::STATUS_PENDING => '未开始',
            self::STATUS_ACTIVE => '进行中',
            self::STATUS_WAITING => '待批改',
            self::STATUS_FINISHED => '已完成',
        ];
    }

}
