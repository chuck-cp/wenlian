<?php
/**
 * @copyright Copyright (c) 2022 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

require_once 'PageTrait.php';

use Phinx\Migration\AbstractMigration;

final class V20220919120815 extends AbstractMigration
{

    use PageTrait;

    public function up()
    {
        $this->handleProtocolPages();
    }

    protected function handleProtocolPages()
    {
        $rows = [
            [
                'title' => '用户协议',
                'alias' => 'terms',
                'content' => '',
                'published' => 1,
                'create_time' => time(),
            ],
            [
                'title' => '隐私政策',
                'alias' => 'privacy',
                'content' => '',
                'published' => 1,
                'create_time' => time(),
            ],
        ];

        $this->insertPages($rows);
    }

}
