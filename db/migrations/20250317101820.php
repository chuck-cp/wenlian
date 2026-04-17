<?php
/**
 * @copyright Copyright (c) 2025 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class V20250317101820 extends AbstractMigration
{

    public function up()
    {
        $this->alterCouponTable();
        $this->alterCouponUserTable();
    }

    protected function alterCouponTable()
    {
        $table = $this->table('kg_coupon');

        if (!$table->hasColumn('private')) {
            $table->addColumn('private', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '私有标识',
                'after' => 'item_info',
            ]);
        }

        if ($table->hasColumn('issue_limit')) {
            $table->renameColumn('issue_limit', 'total_usage');
        }

        if ($table->hasColumn('apply_limit')) {
            $table->renameColumn('apply_limit', 'user_usage');
        }

        if ($table->hasColumn('apply_count')) {
            $table->renameColumn('apply_count', 'claim_count');
        }

        if ($table->hasColumn('consume_count')) {
            $table->renameColumn('consume_count', 'apply_count');
        }

        if ($table->hasColumn('allow_apply')) {
            $table->removeColumn('allow_apply');
        }

        $table->save();
    }

    protected function alterCouponUserTable()
    {
        $table = $this->table('kg_coupon_user');

        if (!$table->hasColumn('apply_count')) {
            $table->addColumn('apply_count', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '使用次数',
                'after' => 'deleted',
            ]);
        }

        if ($table->hasColumn('order_id')) {
            $table->removeColumn('order_id');
        }

        if ($table->hasColumn('consume_time')) {
            $table->removeColumn('consume_time');
        }

        if ($table->hasColumn('expire_time')) {
            $table->removeColumn('expire_time');
        }

        $table->save();
    }

}
