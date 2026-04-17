<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Migration\AbstractMigration;

final class V20230612123030 extends AbstractMigration
{

    public function up()
    {
        /**
         * 先处理原有数据，再字段重命名，顺序不能颠倒
         */
        $this->handleWithdrawAccounts();

        $this->alterWithdrawAccountTable();
    }

    protected function alterWithdrawAccountTable()
    {
        $table = $this->table('kg_withdraw_account');

        if ($table->hasColumn('trade_id')) {
            $table->renameColumn('trade_id', 'order_id');
        }

        if ($table->hasIndexByName('trade_id')) {
            $table->removeIndexByName('trade_id');
            $table->addIndex(['order_id'], [
                'name' => 'order_id',
                'unique' => false,
            ]);
        }

        $table->save();
    }

    protected function handleWithdrawAccounts()
    {
        $rows = $this->getQueryBuilder()
            ->select([
                'account_id' => 'account.id',
                'order_id' => 'order.id',
                'order_status' => 'order.status',
                'trade_channel_identity' => 'trade.channel_identity',
            ])
            ->from(['account' => 'kg_withdraw_account'])
            ->innerJoin(['trade' => 'kg_trade'], 'account.trade_id = trade.id')
            ->innerJoin(['order' => 'kg_order'], 'trade.order_id = order.id')
            ->execute()->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) == 0) return;

        foreach ($rows as $row) {

            $verified = $row['order_status'] == 3 ? 1 : 0;

            $this->getQueryBuilder()
                ->update('kg_withdraw_account')
                ->where(['id' => $row['account_id']])
                ->set('trade_id', $row['order_id'])
                ->set('identity', $row['trade_channel_identity'])
                ->set('verified', $verified)
                ->execute();
        }
    }

}
