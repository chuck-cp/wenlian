<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Migration\AbstractMigration;

final class V20220520014205 extends AbstractMigration
{

    public function up()
    {
        $this->handlePointGift();
        $this->handlePointGiftRedeem();
    }

    protected function handlePointGift()
    {
        $this->getQueryBuilder()
            ->update('kg_point_gift')
            ->set('type', 100)
            ->where(['type' => 2])
            ->execute();
    }

    protected function handlePointGiftRedeem()
    {
        $this->getQueryBuilder()
            ->update('kg_point_gift_redeem')
            ->set('gift_type', 100)
            ->where(['gift_type' => 2])
            ->execute();
    }

}
