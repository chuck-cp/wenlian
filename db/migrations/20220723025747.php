<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

class V20220723025747 extends Phinx\Migration\AbstractMigration
{

    public function up()
    {
        $this->dropImTables();
        $this->deleteImGroupNav();
        $this->deleteImSettings();
    }

    protected function dropImTables()
    {
        $tableNames = [
            'kg_im_friend_group',
            'kg_im_friend_user',
            'kg_im_group',
            'kg_im_group_user',
            'kg_im_message',
            'kg_im_notice',
            'kg_im_user',
        ];

        foreach ($tableNames as $tableName) {
            if ($this->table($tableName)->exists()) {
                $this->table($tableName)->drop()->save();
            }
        }
    }

    protected function deleteImGroupNav()
    {
        $this->getQueryBuilder()
            ->delete('kg_nav')
            ->where(['url' => '/im/group/list'])
            ->execute();
    }

    protected function deleteImSettings()
    {
        $this->getQueryBuilder()
            ->delete('kg_setting')
            ->whereInList('section', ['im.main', 'im.cs'])
            ->execute();
    }

}
