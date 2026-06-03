<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services;

use EasyWeChat\Factory;

class WeChat extends Service
{

    public function getWeChatLogger()
    {
        return $this->getLogger('wechat');
    }

    public function getOfficialAccount()
    {
        $settings = $this->getSettings('wechat.oa');

        $config = [
            'app_id' => $settings['app_id'],
            'secret' => $settings['app_secret'],
            'token' => $settings['app_token'],
            'aes_key' => $settings['aes_key'],
            'log' => $this->getLogOptions(),
        ];

        return Factory::officialAccount($config);
    }

    public function getMiniProgram()
    {
        $settings = $this->getSettings('wechat.mp');

        $config = [
            'app_id' => $settings['app_id'],
            'secret' => $settings['app_secret'],
            'log' => $this->getLogOptions(),
        ];

        return Factory::miniProgram($config);
    }

    protected function getLogOptions()
    {
        $config = $this->getConfig();

        $default = $config->get('env') == ENV_DEV ? 'dev' : 'prod';

        return [
            'default' => $default,
            'channels' => [
                'dev' => [
                    'driver' => 'daily',
                    'path' => log_path('wechat.log'),
                    'level' => 'debug',
                ],
                'prod' => [
                    'driver' => 'daily',
                    'path' => log_path('wechat.log'),
                    'level' => 'info',
                ],
            ]
        ];
    }

}
