<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class V20220922034788 extends AbstractMigration
{

    public function up()
    {
        $this->alterCourseUserTable();
        $this->alterChapterUserTable();
        $this->handleVipOrders();
    }

    protected function alterCourseUserTable()
    {
        $table = $this->table('kg_course_user');

        if (!$table->hasColumn('active_time')) {
            $table->addColumn('active_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '活跃时间',
                'after' => 'deleted',
            ]);
        }

        $table->save();
    }

    protected function alterChapterUserTable()
    {
        $table = $this->table('kg_chapter_user');

        if (!$table->hasColumn('active_time')) {
            $table->addColumn('active_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '活跃时间',
                'after' => 'consumed',
            ]);
        }

        $table->save();
    }

    protected function handleVipOrders()
    {
        $orders = $this->getQueryBuilder()
            ->select('*')
            ->from('kg_order')
            ->where(['item_type' => 3])
            ->execute()->fetchAll(PDO::FETCH_ASSOC);

        if (count($orders) == 0) return;

        foreach ($orders as $order) {

            $itemInfo = json_decode($order['item_info'], true);

            if (!isset($itemInfo['vip']['cover'])) {

                $itemInfo['vip']['cover'] = '/img/default/vip_cover.png';

                $this->getQueryBuilder()
                    ->update('kg_order')
                    ->set('item_info', json_encode($itemInfo))
                    ->where(['id' => $order['id']])
                    ->execute();
            }
        }

    }

}
