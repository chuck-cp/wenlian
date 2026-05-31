<?php
/**
 * @copyright Copyright (c) 2026 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 *
 * kg_user 增加自定义教学角色名称字段（按用户存储，edu_role=3 时使用）
 *
 * 等价 SQL：
 * ALTER TABLE kg_user
 *     ADD COLUMN edu_role_label VARCHAR(4) NOT NULL DEFAULT '' COMMENT '自定义教学角色名称' AFTER edu_role;
 */

use Phinx\Migration\AbstractMigration;

final class V20260531120000 extends AbstractMigration
{

    /**
     * 用户表增加 edu_role_label 字段
     */
    public function up()
    {
        $this->alterUserTable();
    }

    /**
     * 为 kg_user 增加自定义教学角色名称列
     */
    protected function alterUserTable()
    {
        $table = $this->table('kg_user');

        if ($table->hasColumn('edu_role_label')) {
            return;
        }

        $table->addColumn('edu_role_label', 'string', [
            'null' => false,
            'default' => '',
            'limit' => 4,
            'comment' => '自定义教学角色名称',
            'after' => 'edu_role',
        ])->save();
    }

}
