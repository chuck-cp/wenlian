<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use Phalcon\Mvc\Model\Behavior\SoftDelete;
use Phalcon\Text;

class Invoice extends Model
{

    /**
     * 介质类型
     */
    const MEDIA_TYPE_ETC = 1; // 电子票
    const MEDIA_TYPE_PAGER = 2; // 纸质票

    /**
     * 状态类型
     */
    const STATUS_PENDING = 1; // 待处理
    const STATUS_CANCELED = 2; // 已取消
    const STATUS_APPROVED = 3; // 已审核
    const STATUS_REFUSED = 4; // 已拒绝
    const STATUS_FINISHED = 5; // 已完成

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 用户编号
     *
     * @var int
     */
    public $user_id = 0;

    /**
     * 抬头编号
     *
     * @var int
     */
    public $account_id = 0;

    /**
     * 联系人编号
     *
     * @var int
     */
    public $contact_id = 0;

    /**
     * 发票介质（电子票｜纸质票）
     *
     * @var int
     */
    public $media_type = self::MEDIA_TYPE_ETC;

    /**
     * 发票金额
     *
     * @var float
     */
    public $amount = 0.00;

    /**
     * 发票凭据
     *
     * @var string
     */
    public $voucher = '';

    /**
     * 发票代码（种类）
     *
     * @var string
     */
    public $sort_no = '';

    /**
     * 发票号码（序号）
     *
     * @var string
     */
    public $serial_no = '';

    /**
     * 投递邮箱
     *
     * @var string
     */
    public $post_email = '';

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
        return 'kg_invoice';
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

    public function beforeSave()
    {
        if (Text::startsWith($this->voucher, 'http')) {
            $this->voucher = self::getVoucherPath($this->voucher);
        }
    }

    public function afterSave()
    {
        if ($this->hasUpdated('status')) {
            $is = new InvoiceStatus();
            $is->invoice_id = $this->id;
            $is->status = $this->getSnapshotData()['status'];
            $is->create();
        }
    }

    public function afterFetch()
    {
        if (!empty($this->voucher) && !Text::startsWith($this->voucher, 'http')) {
            $this->voucher = kg_cos_url() . $this->voucher;
        }
    }

    public static function getVoucherPath($url)
    {
        if (Text::startsWith($url, 'http')) {
            return parse_url($url, PHP_URL_PATH);
        }

        return $url;
    }

    public static function mediaTypes()
    {
        return [
            self::MEDIA_TYPE_ETC => '电子票',
            self::MEDIA_TYPE_PAGER => '纸质票',
        ];
    }

    public static function statusTypes()
    {
        return [
            self::STATUS_PENDING => '待处理',
            self::STATUS_CANCELED => '已取消',
            self::STATUS_APPROVED => '已审核',
            self::STATUS_REFUSED => '已拒绝',
            self::STATUS_FINISHED => '已完成',
        ];
    }

}
