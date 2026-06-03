<?php
/**
 * @copyright Copyright (c) 2024 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

require_once 'SettingTrait.php';

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class V20240331104811 extends AbstractMigration
{

    use SettingTrait;

    public function up()
    {
        $this->alterExamQuestionTable();
        $this->handlePointSettings();
    }

    protected function alterExamQuestionTable()
    {
        $table = $this->table('kg_exam_question');

        if (!$table->hasColumn('report_count')) {
            $table->addColumn('report_count', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '举报数',
                'after' => 'favorite_count',
            ]);
        }

        $table->save();
    }

    protected function handlePointSettings()
    {
        $section = 'point';
        $itemKey = 'event_rule';

        $row = $this->findSettingItem($section, $itemKey);

        if (!$row) return;

        $content = json_decode($row['item_value'], true);

        $content['criticize_accepted'] = [
            'enabled' => 1,
            'point' => 50,
            'limit' => 1000,
        ];

        $itemValue = json_encode($content);

        $this->updateSettingItem($section, $itemKey, $itemValue);
    }

}
