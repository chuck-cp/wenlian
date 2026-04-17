<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

require_once 'SettingTrait.php';

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class V20211212105748 extends AbstractMigration
{

    use SettingTrait;

    public function up()
    {
        $this->dropDistributionTable();

        $this->alterChapterVodTable();
        $this->alterTradeTable();
        $this->alterVipTable();

        $this->createCashHistoryTable();
        $this->createCouponTable();
        $this->createCouponUserTable();
        $this->createDigitalCardTable();
        $this->createDistributionTable();
        $this->createGrouponTable();
        $this->createGrouponTeamTable();
        $this->createGrouponTeamUserTable();
        $this->createUserRefererTable();
        $this->createWithdrawTable();
        $this->createWithdrawAccountTable();
        $this->createWithdrawStatusTable();

        $this->handleNav();
        $this->handleChapter();
        $this->handleFlashSale();
        $this->handleVodSettings();
        $this->handleSmsSettings();
        $this->handleSiteSettings();
        $this->handleWechatOASettings();
        $this->handleWithdrawSettings();
        $this->handleAffiliateSettings();
    }

    protected function dropDistributionTable()
    {
        $tableName = 'kg_distribution';

        if ($this->table($tableName)->exists()) {
            $this->table($tableName)->drop()->save();
        }
    }

    protected function createCashHistoryTable()
    {
        $tableName = 'kg_cash_history';

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
            ->addColumn('user_name', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '用户名称',
                'after' => 'user_id',
            ])
            ->addColumn('event_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '事件编号',
                'after' => 'user_name',
            ])
            ->addColumn('event_type', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '事件类型',
                'after' => 'event_id',
            ])
            ->addColumn('event_info', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 1000,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '事件内容',
                'after' => 'event_type',
            ])
            ->addColumn('event_amount', 'float', [
                'null' => false,
                'default' => '0.00',
                'comment' => '事件金额',
                'after' => 'event_info',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'event_amount',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['event_id', 'event_type'], [
                'name' => 'event',
                'unique' => false,
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function createCouponTable()
    {
        $tableName = 'kg_coupon';

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
            ->addColumn('code', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 32,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '编码',
                'after' => 'id',
            ])
            ->addColumn('name', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '名称',
                'after' => 'code',
            ])
            ->addColumn('type', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '类型',
                'after' => 'name',
            ])
            ->addColumn('attrs', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 500,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '扩展属性',
                'after' => 'type',
            ])
            ->addColumn('consume_limit', 'decimal', [
                'null' => false,
                'default' => '0.00',
                'precision' => '10',
                'scale' => '2',
                'signed' => false,
                'comment' => ' 最低消费',
                'after' => 'attrs',
            ])
            ->addColumn('issue_limit', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '发行数量',
                'after' => 'consume_limit',
            ])
            ->addColumn('apply_limit', 'integer', [
                'null' => false,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '申领限额',
                'after' => 'issue_limit',
            ])
            ->addColumn('allow_apply', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '允许领取',
                'after' => 'apply_limit',
            ])
            ->addColumn('item_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '物品编号',
                'after' => 'allow_apply',
            ])
            ->addColumn('item_type', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '物品类型',
                'after' => 'item_id',
            ])
            ->addColumn('item_info', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 500,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '物品信息',
                'after' => 'item_type',
            ])
            ->addColumn('published', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '发布标识',
                'after' => 'item_info',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'published',
            ])
            ->addColumn('apply_count', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '申领数量',
                'after' => 'deleted',
            ])
            ->addColumn('consume_count', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '使用数量',
                'after' => 'apply_count',
            ])
            ->addColumn('start_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '开始时间',
                'after' => 'consume_count',
            ])
            ->addColumn('end_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '结束时间',
                'after' => 'start_time',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'end_time',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['code'], [
                'name' => 'code',
                'unique' => false,
            ])
            ->addIndex(['item_id', 'item_type'], [
                'name' => 'item',
                'unique' => false,
            ])
            ->create();
    }

    protected function createCouponUserTable()
    {
        $tableName = 'kg_coupon_user';

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
            ->addColumn('coupon_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '优惠券编号',
                'after' => 'id',
            ])
            ->addColumn('user_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '用户编号',
                'after' => 'coupon_id',
            ])
            ->addColumn('order_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '订单编号',
                'after' => 'user_id',
            ])
            ->addColumn('channel', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '获取途径',
                'after' => 'order_id',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'channel',
            ])
            ->addColumn('expire_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '过期时间',
                'after' => 'deleted',
            ])
            ->addColumn('consume_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '使用时间',
                'after' => 'expire_time',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'consume_time',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['coupon_id'], [
                'name' => 'coupon_id',
                'unique' => false,
            ])
            ->addIndex(['order_id'], [
                'name' => 'order_id',
                'unique' => false,
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function createDigitalCardTable()
    {
        $tableName = 'kg_digital_card';

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
            ->addColumn('code', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '兑换码',
                'after' => 'id',
            ])
            ->addColumn('remark', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '备注说明',
                'after' => 'code',
            ])
            ->addColumn('user_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '用户编号',
                'after' => 'remark',
            ])
            ->addColumn('user_name', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '用户名称',
                'after' => 'user_id',
            ])
            ->addColumn('item_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '物品编号',
                'after' => 'user_name',
            ])
            ->addColumn('item_type', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '物品类型',
                'after' => 'item_id',
            ])
            ->addColumn('item_title', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '物品名称',
                'after' => 'item_type',
            ])
            ->addColumn('item_price', 'decimal', [
                'null' => false,
                'default' => '0.00',
                'precision' => '10',
                'scale' => '2',
                'signed' => false,
                'comment' => '物品价格',
                'after' => 'item_title',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'item_price',
            ])
            ->addColumn('redeem_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '兑换时间',
                'after' => 'deleted',
            ])
            ->addColumn('expire_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '过期时间',
                'after' => 'redeem_time',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'expire_time',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['code'], [
                'name' => 'code',
                'unique' => false,
            ])
            ->addIndex(['item_id', 'item_type'], [
                'name' => 'item',
                'unique' => false,
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function createDistributionTable()
    {
        $tableName = 'kg_distribution';

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
            ->addColumn('item_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '商品编号',
                'after' => 'id',
            ])
            ->addColumn('item_type', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '商品类型',
                'after' => 'item_id',
            ])
            ->addColumn('item_info', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 1500,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '商品信息',
                'after' => 'item_type',
            ])
            ->addColumn('v1_com_rate', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '一级佣金',
                'after' => 'item_info',
            ])
            ->addColumn('v2_com_rate', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '二级佣金',
                'after' => 'v1_com_rate',
            ])
            ->addColumn('v3_com_rate', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '三级佣金',
                'after' => 'v2_com_rate',
            ])
            ->addColumn('published', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '发布标识',
                'after' => 'v3_com_rate',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'published',
            ])
            ->addColumn('start_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '开始时间',
                'after' => 'deleted',
            ])
            ->addColumn('end_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '结束时间',
                'after' => 'start_time',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'end_time',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->create();
    }

    protected function createGrouponTable()
    {
        $tableName = 'kg_groupon';

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
            ->addColumn('item_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '物品编号',
                'after' => 'id',
            ])
            ->addColumn('item_type', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '物品类型',
                'after' => 'item_id',
            ])
            ->addColumn('item_info', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 1500,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '物品信息',
                'after' => 'item_type',
            ])
            ->addColumn('member_price', 'decimal', [
                'null' => false,
                'default' => '0.00',
                'precision' => '10',
                'scale' => '2',
                'signed' => false,
                'comment' => '团员价格',
                'after' => 'item_info',
            ])
            ->addColumn('leader_price', 'decimal', [
                'null' => false,
                'default' => '0.00',
                'precision' => '10',
                'scale' => '2',
                'signed' => false,
                'comment' => '团长价格',
                'after' => 'member_price',
            ])
            ->addColumn('partner_limit', 'integer', [
                'null' => false,
                'default' => '2',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '成团人数',
                'after' => 'leader_price',
            ])
            ->addColumn('partner_expiry', 'integer', [
                'null' => false,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '成团期限',
                'after' => 'partner_limit',
            ])
            ->addColumn('virtual_partner', 'integer', [
                'null' => false,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '虚拟成团',
                'after' => 'partner_expiry',
            ])
            ->addColumn('published', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '发布标识',
                'after' => 'virtual_partner',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'published',
            ])
            ->addColumn('total_team_count', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '开团数量',
                'after' => 'deleted',
            ])
            ->addColumn('finish_team_count', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '成团数量',
                'after' => 'total_team_count',
            ])
            ->addColumn('start_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '开始时间',
                'after' => 'finish_team_count',
            ])
            ->addColumn('end_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '结束时间',
                'after' => 'start_time',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'end_time',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->create();
    }

    protected function createGrouponTeamTable()
    {
        $tableName = 'kg_groupon_team';

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
            ->addColumn('groupon_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '拼团编号',
                'after' => 'id',
            ])
            ->addColumn('leader_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '团长编号',
                'after' => 'groupon_id',
            ])
            ->addColumn('status', 'integer', [
                'null' => false,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '状态标识',
                'after' => 'leader_id',
            ])
            ->addColumn('target_order_count', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '目标订单数',
                'after' => 'status',
            ])
            ->addColumn('finish_order_count', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '完成订单数',
                'after' => 'target_order_count',
            ])
            ->addColumn('expire_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '过期时间',
                'after' => 'finish_order_count',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'expire_time',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['groupon_id'], [
                'name' => 'coupon_id',
                'unique' => false,
            ])
            ->addIndex(['leader_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function createGrouponTeamUserTable()
    {
        $tableName = 'kg_groupon_team_user';

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
            ->addColumn('groupon_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '团购编号',
                'after' => 'id',
            ])
            ->addColumn('team_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '队伍编号',
                'after' => 'groupon_id',
            ])
            ->addColumn('user_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '用户编号',
                'after' => 'team_id',
            ])
            ->addColumn('order_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '订单编号',
                'after' => 'user_id',
            ])
            ->addColumn('status', 'integer', [
                'null' => false,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '状态标识',
                'after' => 'order_id',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'status',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['groupon_id'], [
                'name' => 'coupon_id',
                'unique' => false,
            ])
            ->addIndex(['team_id'], [
                'name' => 'team_id',
                'unique' => false,
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function createUserRefererTable()
    {
        $tableName = 'kg_user_referer';

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
            ->addColumn('parent_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '上级编号',
                'after' => 'user_id',
            ])
            ->addColumn('parent_level', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '上级等级',
                'after' => 'parent_id',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'parent_level',
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function createWithdrawTable()
    {
        $tableName = 'kg_withdraw';

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
            ->addColumn('sn', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 32,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '转账序号',
                'after' => 'id',
            ])
            ->addColumn('user_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '用户编号',
                'after' => 'sn',
            ])
            ->addColumn('account_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '账户编号',
                'after' => 'user_id',
            ])
            ->addColumn('apply_amount', 'decimal', [
                'null' => false,
                'default' => '0.00',
                'precision' => '10',
                'scale' => '2',
                'signed' => false,
                'comment' => '申请金额',
                'after' => 'account_id',
            ])
            ->addColumn('trans_amount', 'decimal', [
                'null' => false,
                'default' => '0.00',
                'precision' => '10',
                'scale' => '2',
                'signed' => false,
                'comment' => '转账金额',
                'after' => 'apply_amount',
            ])
            ->addColumn('service_fee', 'decimal', [
                'null' => false,
                'default' => '0.00',
                'precision' => '10',
                'scale' => '2',
                'signed' => false,
                'comment' => '服务费',
                'after' => 'trans_amount',
            ])
            ->addColumn('tax_fee', 'decimal', [
                'null' => false,
                'default' => '0.00',
                'precision' => '10',
                'scale' => '2',
                'signed' => false,
                'comment' => '税费',
                'after' => 'service_fee',
            ])
            ->addColumn('apply_note', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '申请备注',
                'after' => 'tax_fee',
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
            ->addColumn('transferred', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '转账标识',
                'after' => 'status',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'transferred',
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
            ->addIndex(['account_id'], [
                'name' => 'account_id',
                'unique' => false,
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->addIndex(['sn'], [
                'name' => 'sn',
                'unique' => false,
            ])
            ->create();
    }

    protected function createWithdrawAccountTable()
    {
        $tableName = 'kg_withdraw_account';

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
            ->addColumn('trade_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '交易编号',
                'after' => 'user_id',
            ])
            ->addColumn('channel', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '平台类型',
                'after' => 'trade_id',
            ])
            ->addColumn('name', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '买家姓名',
                'after' => 'channel',
            ])
            ->addColumn('account', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 64,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '买家账号',
                'after' => 'name',
            ])
            ->addColumn('identity', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 64,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '买家标识',
                'after' => 'account',
            ])
            ->addColumn('master', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '默认标识',
                'after' => 'identity',
            ])
            ->addColumn('verified', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '验证标识',
                'after' => 'master',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'verified',
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
            ->addIndex(['trade_id'], [
                'name' => 'trade_id',
                'unique' => false,
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function createWithdrawStatusTable()
    {
        $tableName = 'kg_withdraw_status';

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
            ->addColumn('withdraw_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '提现编号',
                'after' => 'id',
            ])
            ->addColumn('status', 'integer', [
                'null' => false,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '订单状态',
                'after' => 'withdraw_id',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'status',
            ])
            ->addIndex(['withdraw_id'], [
                'name' => 'withdraw_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function alterChapterVodTable()
    {
        $table = $this->table('kg_chapter_vod');

        if (!$table->hasColumn('file_origin')) {
            $table->addColumn('file_origin', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 500,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '原始文件',
                'after' => 'file_id',
            ]);
        }

        if (!$table->hasColumn('file_encrypt')) {
            $table->addColumn('file_encrypt', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 500,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '加密文件',
                'after' => 'file_transcode',
            ]);
        }

        $table->save();
    }

    protected function alterTradeTable()
    {
        $table = $this->table('kg_trade');

        if (!$table->hasColumn('channel_identity')) {
            $table->addColumn('channel_identity', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 64,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '帐户标识',
                'after' => 'channel_sn',
            ]);
        }

        $table->save();
    }

    protected function alterVipTable()
    {
        $table = $this->table('kg_vip');

        if (!$table->hasColumn('published')) {
            $table->addColumn('published', 'integer', [
                'null' => false,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '发布标识',
                'after' => 'price',
            ]);
        }

        $table->save();
    }

    protected function handleChapter()
    {
        $chapters = $this->getQueryBuilder()
            ->select('*')
            ->from('kg_chapter')
            ->where(['model' => 1, 'parent_id >' => 0])
            ->execute()->fetchAll(PDO::FETCH_ASSOC);

        if (empty($chapters)) return;

        foreach ($chapters as $chapter) {
            $attrs = json_decode($chapter['attrs'], true);
            $attrs['transcode'] = [
                'standard' => ['status' => 'finished'],
                'encrypt' => ['status' => 'pending'],
            ];
            if (isset($attrs['file'])) {
                unset($attrs['file']);
            }
            $attrs = json_encode($attrs);
            $this->getQueryBuilder()
                ->update('kg_chapter')
                ->set('attrs', $attrs)
                ->where(['id' => $chapter['id']])
                ->execute();
        }
    }

    protected function handleFlashSale()
    {
        $sales = $this->getQueryBuilder()
            ->select('*')
            ->from('kg_flash_sale')
            ->execute()->fetchAll(PDO::FETCH_ASSOC);

        if (empty($sales)) return;

        foreach ($sales as $sale) {

            $allowReplace = false;

            $itemInfo = json_decode($sale['item_info'], true);

            if (isset($itemInfo['course']['market_price'])) {
                $itemInfo['course']['price'] = $itemInfo['course']['market_price'];
                unset($itemInfo['course']['market_price']);
                $allowReplace = true;
            } elseif (isset($itemInfo['package']['market_price'])) {
                $itemInfo['package']['price'] = $itemInfo['package']['market_price'];
                unset($itemInfo['package']['market_price']);
                $allowReplace = true;
            }

            if ($allowReplace) {
                $itemInfo = json_encode($itemInfo);
                $this->getQueryBuilder()
                    ->update('kg_flash_sale')
                    ->set('item_info', $itemInfo)
                    ->where(['id' => $sale['id']])
                    ->execute();
            }
        }
    }

    protected function handleNav()
    {
        $now = time();

        $rows = [
            [
                'parent_id' => 0,
                'level' => 1,
                'name' => '拼团',
                'path' => ',0,',
                'target' => '_self',
                'url' => '/groupon/list',
                'position' => 1,
                'priority' => 8,
                'published' => 1,
                'create_time' => $now,
            ],
            [
                'parent_id' => 0,
                'level' => 1,
                'name' => '分销',
                'path' => ',0,',
                'target' => '_self',
                'url' => '/distribution/list',
                'position' => 1,
                'priority' => 9,
                'published' => 1,
                'create_time' => $now,
            ],
        ];

        $this->table('kg_nav')->insert($rows)->save();

        $navs = $this->getQueryBuilder()
            ->select('*')
            ->from('kg_nav')
            ->order(['id' => 'DESC'])
            ->limit(2)
            ->execute()->fetchAll(PDO::FETCH_ASSOC);

        foreach ($navs as $nav) {
            $this->getQueryBuilder()
                ->update('kg_nav')
                ->set('path', ",{$nav['id']},")
                ->where(['id' => $nav['id']])
                ->execute();
        }
    }

    protected function handleVodSettings()
    {
        $item = $this->getQueryBuilder()
            ->select('*')
            ->from('kg_setting')
            ->where(['section' => 'secret', 'item_key' => 'app_id'])
            ->execute()->fetch(PDO::FETCH_ASSOC);

        $appId = $item['item_value'];

        $rows = [
            [
                'section' => 'vod',
                'item_key' => 'sub_app_id',
                'item_value' => $appId,
            ],
            [
                'section' => 'vod',
                'item_key' => 'std_trans_enabled',
                'item_value' => 1,
            ],
            [
                'section' => 'vod',
                'item_key' => 'encrypt_trans_enabled',
                'item_value' => 0,
            ],
            [
                'section' => 'vod',
                'item_key' => 'encrypt_player_config',
                'item_value' => 'kooguaDrmPreset',
            ],
            [
                'section' => 'vod',
                'item_key' => 'encrypt_tpl_id',
                'item_value' => 0,
            ],
            [
                'section' => 'vod',
                'item_key' => 'record_anti_enabled',
                'item_value' => 1,
            ],
            [
                'section' => 'vod',
                'item_key' => 'record_anti_config',
                'item_value' => json_encode([
                    'color' => '#ff5722',
                    'size' => 18,
                    'interval' => 10,
                ]),
            ],
        ];

        $this->insertSettings($rows);
    }

    protected function handleSiteSettings()
    {
        $rows = [
            [
                'section' => 'site',
                'item_key' => 'license',
                'item_value' => '',
            ]
        ];

        $this->insertSettings($rows);
    }

    protected function handleAffiliateSettings()
    {
        $rows = [
            [
                'section' => 'affiliate',
                'item_key' => 'v1_com_enabled',
                'item_value' => 1,
            ],
            [
                'section' => 'affiliate',
                'item_key' => 'v2_com_enabled',
                'item_value' => 1,
            ],
            [
                'section' => 'affiliate',
                'item_key' => 'v3_com_enabled',
                'item_value' => 1,
            ],
            [
                'section' => 'affiliate',
                'item_key' => 'v1_com_rate',
                'item_value' => 10,
            ],
            [
                'section' => 'affiliate',
                'item_key' => 'v2_com_rate',
                'item_value' => 5,
            ],
            [
                'section' => 'affiliate',
                'item_key' => 'v3_com_rate',
                'item_value' => 2,
            ],
        ];

        $this->insertSettings($rows);
    }

    protected function handleWithdrawSettings()
    {
        $rows = [
            [
                'section' => 'withdraw',
                'item_key' => 'enabled',
                'item_value' => 1,
            ],
            [
                'section' => 'withdraw',
                'item_key' => 'review_type',
                'item_value' => 'auto',
            ],
            [
                'section' => 'withdraw',
                'item_key' => 'min_amount',
                'item_value' => 100,
            ],
            [
                'section' => 'withdraw',
                'item_key' => 'max_amount',
                'item_value' => 5000,
            ],
            [
                'section' => 'withdraw',
                'item_key' => 'service_rate',
                'item_value' => 20,
            ],
            [
                'section' => 'withdraw',
                'item_key' => 'tax_rate',
                'item_value' => 0,
            ],
            [
                'section' => 'withdraw',
                'item_key' => 'monthly_limit',
                'item_value' => 2,
            ],
            [
                'section' => 'withdraw',
                'item_key' => 'channels',
                'item_value' => '["alipay","wechat"]',
            ],
        ];

        $this->insertSettings($rows);
    }

    protected function handleSmsSettings()
    {
        $rows = [
            [
                'section' => 'sms',
                'item_key' => 'region',
                'item_value' => 'ap-guangzhou',
            ]
        ];

        $this->insertSettings($rows);

        $item = $this->getQueryBuilder()
            ->select('*')
            ->from('kg_setting')
            ->where(['section' => 'sms', 'item_key' => 'template'])
            ->execute()->fetch(PDO::FETCH_ASSOC);

        $template = json_decode($item['item_value'], true);

        $template['withdraw_finish'] = [
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

        $template['withdraw_finish'] = [
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

}
