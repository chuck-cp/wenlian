<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use Phalcon\Mvc\Model\Behavior\SoftDelete;

class InvoiceAccount extends Model
{

    /**
     * 抬头类型
     */
    const HEAD_TYPE_PERSON = 1; // 个人
    const HEAD_TYPE_COMPANY = 2; // 公司
    const HEAD_TYPE_ORG = 3; // 组织

    /**
     * 发票类型
     */
    const USAGE_TYPE_NORMAL = 1; // 普票
    const USAGE_TYPE_SPECIAL = 2; // 专票

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
     * 开票类型（普票｜专票）
     *
     * @var int
     */
    public $usage_type = self::USAGE_TYPE_NORMAL;

    /**
     * 抬头类型（个人｜企业｜组织）
     *
     * @var int
     */
    public $head_type = self::HEAD_TYPE_PERSON;

    /**
     * 发票抬头
     *
     * @var string
     */
    public $head_name = '';

    /**
     * 纳税人识别号
     *
     * @var string
     */
    public $tax_account = '';

    /**
     *
     * 开户银行
     * @var string
     */
    public $bank_name = '';

    /**
     *
     * 开户帐号
     * @var string
     */
    public $bank_account = '';

    /**
     *
     * 企业地址
     * @var string
     */
    public $company_address = '';

    /**
     *
     * 企业电话
     * @var string
     */
    public $company_phone = '';

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
        return 'kg_invoice_account';
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

    public static function headTypes()
    {
        return [
            self::HEAD_TYPE_PERSON => '个人',
            self::HEAD_TYPE_COMPANY => '公司',
            self::HEAD_TYPE_ORG => '组织',
        ];
    }

    public static function usageTypes()
    {
        return [
            self::USAGE_TYPE_NORMAL => '增值税普票',
            self::USAGE_TYPE_SPECIAL => '增值税专票',
        ];
    }

    public static function getEnabledUsageTypes()
    {
        $settings = kg_setting('invoice');

        $usageTypes = json_decode($settings['usage_types'], true);

        $result = [];

        if (in_array('normal', $usageTypes)) {
            $result[self::USAGE_TYPE_NORMAL] = '增值税普票';
        }

        if (in_array('special', $usageTypes)) {
            $result[self::USAGE_TYPE_SPECIAL] = '增值税专票';
        }

        return $result;
    }

}
