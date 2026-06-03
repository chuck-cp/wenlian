<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Console\Migrations;

class V20250610101502 extends Migration
{

    public function run()
    {
        $this->handleCosSettings();
    }

    protected function handleCosSettings()
    {
        $settings = [
            [
                'section' => 'cos',
                'item_key' => 'doc_copy_enabled',
                'item_value' => 0,
            ],
            [
                'section' => 'cos',
                'item_key' => 'doc_wmk',
                'item_value' => json_encode([
                    'enabled' => 1,
                    'text' => '酷瓜云课堂',
                    'size' => 20,
                    'color' => 'rgba(192,192,192,0.6)',
                    'rotate' => 320,
                    'horizontal' => 50,
                    'vertical' => 100,
                ]),
            ],
        ];

        $this->saveSettings($settings);
    }

}
