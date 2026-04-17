<?php
/**
 * @copyright Copyright (c) 2022 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Migration\AbstractMigration;

final class V20220825170005 extends AbstractMigration
{

    public function up()
    {
        $this->handleSmsSettings();
        $this->handleWechatOASettings();
    }

    protected function handleSmsSettings()
    {
        $item = $this->getQueryBuilder()
            ->select('*')
            ->from('kg_setting')
            ->where(['section' => 'sms', 'item_key' => 'template'])
            ->execute()->fetch(PDO::FETCH_ASSOC);

        $template = json_decode($item['item_value'], true);

        if (isset($template['dist_success'])) return;

        $template['dist_success'] = [
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

        if (isset($template['dist_success'])) return;

        $template['dist_success'] = [
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
