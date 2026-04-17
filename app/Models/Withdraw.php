<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use Phalcon\Mvc\Model\Behavior\SoftDelete;

class Withdraw extends Model
{

    /**
     * 状态类型
     */
    const STATUS_PENDING = 1; // 待处理
    const STATUS_CANCELED = 2; // 已取消
    const STATUS_APPROVED = 3; // 已审核
    const STATUS_REFUSED = 4; // 已拒绝
    const STATUS_FINISHED = 5; // 已完成
    const STATUS_FAILED = 6; // 已失败
    const STATUS_REFUNDED = 7; // 已退款

    /**
     * 审核类型
     */
    const REVIEW_TYPE_MANUAL = 'manual'; // 人工
    const REVIEW_TYPE_AUTO = 'auto'; // 自动

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
     * 用户编号
     *
     * @var int
     */
    public $user_id = 0;

    /**
     * 提现账户编号
     *
     * @var int
     */
    public $account_id = 0;

    /**
     * 申请额度
     *
     * @var float
     */
    public $apply_amount = 0.00;

    /**
     * 转账金额
     *
     * @var float
     */
    public $trans_amount = 0.00;

    /**
     * 服务费
     *
     * @var float
     */
    public $service_fee = 0.00;

    /**
     * 税费
     *
     * @var float
     */
    public $tax_fee = 0.00;

    /**
     * 申请备注
     *
     * @var string
     */
    public $apply_note = '';

    /**
     * 审核备注
     *
     * @var string
     */
    public $review_note = '';

    /**
     * 状态类型
     *
     * @var int
     */
    public $status = self::STATUS_PENDING;

    /**
     * 转账标识
     *
     * @var int
     */
    public $transferred = 0;

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
        return 'kg_withdraw';
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
        $this->sn = $this->getWithdrawSn();

        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        $this->update_time = time();
    }

    public function afterSave()
    {
        if ($this->hasUpdated('status')) {
            $withdrawStatus = new WithdrawStatus();
            $withdrawStatus->withdraw_id = $this->id;
            $withdrawStatus->status = $this->getSnapshotData()['status'];
            $withdrawStatus->create();
        }
    }

    public static function statusTypes()
    {
        return [
            self::STATUS_PENDING => '待处理',
            self::STATUS_CANCELED => '已取消',
            self::STATUS_APPROVED => '已审核',
            self::STATUS_REFUSED => '已拒绝',
            self::STATUS_FINISHED => '已完成',
            self::STATUS_FAILED => '已失败',
            self::STATUS_REFUNDED => '已退款',
        ];
    }

    public static function reviewTypes()
    {
        return [
            self::REVIEW_TYPE_AUTO => '自动',
            self::REVIEW_TYPE_MANUAL => '人工',
        ];
    }

    protected function getWithdrawSn()
    {
        $sn = date('YmdHis') . rand(1000, 9999);

        $withdraw = self::findFirst([
            'conditions' => 'sn = :sn:',
            'bind' => ['sn' => $sn],
        ]);

        if (!$withdraw) return $sn;

        return $this->getWithdrawSn();
    }

}
