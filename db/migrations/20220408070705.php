<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

require_once 'SettingTrait.php';

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class V20220408070705 extends AbstractMigration
{

    use SettingTrait;

    public function up()
    {
        $this->createInvoiceTable();
        $this->createInvoiceAccountTable();
        $this->createInvoiceStatusTable();
        $this->alterPointGiftRedeemTable();
        $this->alterUserContactTable();
        $this->handleInvoiceSettings();
        $this->handleSmsSettings();
        $this->handleWechatOASettings();
        $this->handleDingTalkRobotSettings();
    }

    protected function createInvoiceTable()
    {
        $tableName = 'kg_invoice';

        if ($this->table($tableName)->exists()) {
            return;
        }

        $this->table($tableName, [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'identity' => 'enable',
                'comment' => '主键编号',
            ])
            ->addColumn('user_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '用户编号',
                'after' => 'id',
            ])
            ->addColumn('account_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '抬头编号',
                'after' => 'user_id',
            ])
            ->addColumn('contact_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '联系编号',
                'after' => 'account_id',
            ])
            ->addColumn('media_type', 'integer', [
                'null' => false,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '发票介质',
                'after' => 'account_id',
            ])
            ->addColumn('amount', 'decimal', [
                'null' => false,
                'default' => '0.00',
                'precision' => '10',
                'scale' => '2',
                'signed' => false,
                'comment' => '发票金额',
                'after' => 'media_type',
            ])
            ->addColumn('voucher', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '发票凭据',
                'after' => 'amount',
            ])
            ->addColumn('sort_no', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 15,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '发票代码',
                'after' => 'voucher',
            ])
            ->addColumn('serial_no', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 15,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '发票号码',
                'after' => 'sort_no',
            ])
            ->addColumn('apply_note', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '申请备注',
                'after' => 'sort_no',
            ])
            ->addColumn('review_note', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '审核备注',
                'after' => 'apply_note',
            ])
            ->addColumn('status', 'integer', [
                'null' => false,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '状态类型',
                'after' => 'review_note',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'status',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'deleted',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->addIndex(['account_id'], [
                'name' => 'account_id',
                'unique' => false,
            ])
            ->addIndex(['contact_id'], [
                'name' => 'contact_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function createInvoiceAccountTable()
    {
        $tableName = 'kg_invoice_account';

        if ($this->table($tableName)->exists()) {
            return;
        }

        $this->table($tableName, [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'identity' => 'enable',
                'comment' => '主键编号',
            ])
            ->addColumn('user_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '用户编号',
                'after' => 'id',
            ])
            ->addColumn('usage_type', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '发票类型',
                'after' => 'user_id',
            ])
            ->addColumn('head_type', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '抬头类型',
                'after' => 'usage_type',
            ])
            ->addColumn('head_name', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '抬头名称',
                'after' => 'head_type',
            ])
            ->addColumn('tax_account', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '税务帐号',
                'after' => 'head_name',
            ])
            ->addColumn('bank_name', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '开户银行',
                'after' => 'tax_account',
            ])
            ->addColumn('bank_account', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '开户帐号',
                'after' => 'bank_name',
            ])
            ->addColumn('company_address', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '公司地址',
                'after' => 'bank_account',
            ])
            ->addColumn('company_phone', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '公司电话',
                'after' => 'company_address',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'company_phone',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'deleted',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function createInvoiceStatusTable()
    {
        $tableName = 'kg_invoice_status';

        if ($this->table($tableName)->exists()) {
            return;
        }

        $this->table($tableName, [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => 'enable',
                'comment' => '主键编号',
            ])
            ->addColumn('invoice_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '发票编号',
                'after' => 'id',
            ])
            ->addColumn('status', 'integer', [
                'null' => false,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '订单状态',
                'after' => 'invoice_id',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'status',
            ])
            ->addIndex(['invoice_id'], [
                'name' => 'invoice_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function alterPointGiftRedeemTable()
    {
        $table = $this->table('kg_point_gift_redeem');

        if (!$table->hasColumn('contact_id')) {
            $table->addColumn('contact_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '联系人编号',
                'after' => 'gift_point',
            ])->addIndex(['contact_id'], [
                'name' => 'contact_id',
                'unique' => false,
            ]);
        }

        $table->save();
    }

    protected function alterUserContactTable()
    {
        $tableName = 'kg_user_contact';

        /**
         * 原有的表更新结构太复杂，干脆重建一个
         */
        if ($this->table($tableName)->exists()) {
            $this->table($tableName)->drop()->save();
        }

        $this->table($tableName, [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'identity' => 'enable',
                'comment' => '主键编号',
            ])
            ->addColumn('user_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '用户编号',
                'after' => 'id',
            ])
            ->addColumn('name', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '姓名',
                'after' => 'user_id',
            ])
            ->addColumn('phone', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '电话',
                'after' => 'name',
            ])
            ->addColumn('add_province', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '地址(省)',
                'after' => 'phone',
            ])
            ->addColumn('add_city', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '地址(市)',
                'after' => 'add_province',
            ])
            ->addColumn('add_county', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '地址(区)',
                'after' => 'add_city',
            ])
            ->addColumn('add_other', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '地址(详)',
                'after' => 'add_county',
            ])
            ->addColumn('master', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '默认标识',
                'after' => 'add_other',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'master',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'master',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function handleInvoiceSettings()
    {
        $rows = [
            [
                'section' => 'invoice',
                'item_key' => 'enabled',
                'item_value' => '0',
            ],
            [
                'section' => 'invoice',
                'item_key' => 'min_amount',
                'item_value' => '100',
            ],
            [
                'section' => 'invoice',
                'item_key' => 'max_amount',
                'item_value' => '10000',
            ],
            [
                'section' => 'invoice',
                'item_key' => 'monthly_limit',
                'item_value' => '2',
            ],
            [
                'section' => 'invoice',
                'item_key' => 'usage_types',
                'item_value' => '["normal","special"]',
            ],
            [
                'section' => 'invoice',
                'item_key' => 'media_types',
                'item_value' => '["etc","paper"]',
            ],
        ];

        $this->insertSettings($rows);
    }

    protected function handleSmsSettings()
    {
        $item = $this->getQueryBuilder()
            ->select('*')
            ->from('kg_setting')
            ->where(['section' => 'sms', 'item_key' => 'template'])
            ->execute()->fetch(PDO::FETCH_ASSOC);

        $template = json_decode($item['item_value'], true);

        $template['invoice_finish'] = [
            'enabled' => 0,
            'id' => 0,
        ];

        $newTemplate = json_encode($template);

        $this->getQueryBuilder()
            ->update('kg_setting')
            ->set('item_value', $newTemplate)
            ->where(['id' => $item['id']])
            ->execute();
    }

    protected function handleWechatOASettings()
    {
        $item = $this->getQueryBuilder()
            ->select('*')
            ->from('kg_setting')
            ->where(['section' => 'wechat.oa', 'item_key' => 'notice_template'])
            ->execute()->fetch(PDO::FETCH_ASSOC);

        $template = json_decode($item['item_value'], true);

        $template['invoice_finish'] = [
            'enabled' => 0,
            'id' => 0,
        ];

        $newTemplate = json_encode($template);

        $this->getQueryBuilder()
            ->update('kg_setting')
            ->set('item_value', $newTemplate)
            ->where(['id' => $item['id']])
            ->execute();
    }

    protected function handleDingTalkRobotSettings()
    {
        $rows = [
            [
                'section' => 'dingtalk.robot',
                'item_key' => 'fs_mobiles',
                'item_value' => '',
            ]
        ];

        $this->insertSettings($rows);
    }

}
