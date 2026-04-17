<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Migration\AbstractMigration;

final class V20230625182830 extends AbstractMigration
{

    public function up()
    {
        $this->deleteCaptchaSettings();
        $this->alterUserBalanceTable();
        $this->handleUserFluidInvoices();
    }

    protected function deleteCaptchaSettings()
    {
        $this->getQueryBuilder()
            ->delete('kg_setting')
            ->where(['section' => 'captcha'])
            ->execute();
    }

    /**
     * 表结构有过几次反覆，合并处理最终修改
     */
    protected function alterUserBalanceTable()
    {
        $table = $this->table('kg_user_balance');

        if ($table->hasColumn('fluid_cash')) {
            $table->renameColumn('fluid_cash', 'cash');
        } elseif (!$table->hasColumn('cash')) {
            $table->addColumn('cash', 'decimal', [
                'null' => false,
                'default' => '0.00',
                'precision' => '10',
                'scale' => '2',
                'signed' => false,
                'comment' => '现金额度',
                'after' => 'user_id',
            ]);
        }

        if ($table->hasColumn('fluid_invoice')) {
            $table->renameColumn('fluid_invoice', 'invoice');
        } elseif (!$table->hasColumn('invoice')) {
            $table->addColumn('invoice', 'decimal', [
                'null' => false,
                'default' => '0.00',
                'precision' => '10',
                'scale' => '2',
                'signed' => false,
                'comment' => '开票额度',
                'after' => 'cash',
            ]);
        }

        if ($table->hasColumn('frozen_cash')) {
            $table->removeColumn('frozen_cash');
        }

        if ($table->hasColumn('frozen_invoice')) {
            $table->removeColumn('frozen_invoice');
        }

        $table->save();
    }

    /**
     * 以前没有实现开票可用额度的计算，只把已完成支付的订单金额纳入可开票额度
     *
     * @return void
     */
    protected function handleUserFluidInvoices()
    {
        $orders = $this->getQueryBuilder()
            ->select(['id', 'owner_id', 'amount'])
            ->from('kg_order')
            ->where(['status' => 3])
            ->execute()->fetchAll(PDO::FETCH_ASSOC);

        if (count($orders) == 0) return;

        $mappings = [];

        foreach ($orders as $order) {
            $key = $order['owner_id'];
            if (isset($mappings[$key])) {
                $mappings[$key] += $order['amount'];
            } else {
                $mappings[$key] = $order['amount'];
            }
        }

        foreach ($mappings as $userId => $amount) {
            $this->getQueryBuilder()
                ->update('kg_user_balance')
                ->set('invoice', $amount)
                ->where(['user_id' => $userId])
                ->execute();
        }
    }

}
