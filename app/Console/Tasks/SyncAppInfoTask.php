<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Library\AppInfo;
use App\Library\Utils\Lock as LockUtil;
use App\Repos\Setting as SettingRepo;
use GuzzleHttp\Client as HttpClient;

class SyncAppInfoTask extends Task
{

    const API_BASE_URL = 'https://www.koogua.com/api';

    public function mainAction()
    {
        $taskLockKey = $this->getTaskLockKey();

        $taskLockId = LockUtil::addLock($taskLockKey);

        if (!$taskLockId) return;

        echo '------ start sync app info ------' . PHP_EOL;

        $site = $this->getSettings('site');

        $serverHost = parse_url($site['url'], PHP_URL_HOST);

        $serverIp = gethostbyname($serverHost);

        $appInfo = new AppInfo();

        $params = [
            'server_host' => $serverHost,
            'server_ip' => $serverIp,
            'app_name' => $appInfo->get('name'),
            'app_alias' => $appInfo->get('alias'),
            'app_version' => $appInfo->get('version'),
            'app_link' => $appInfo->get('link'),
        ];

        $this->checkLicense($params);

        $client = new HttpClient();

        $url = sprintf('%s/instance/collect', self::API_BASE_URL);

        $client->request('POST', $url, ['form_params' => $params]);

        echo '------ end of sync app info ------' . PHP_EOL;

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    protected function checkLicense($params)
    {
        $url = sprintf('%s/license/check', self::API_BASE_URL);

        $settingRepo = new SettingRepo();

        $setting = $settingRepo->findItem('site', 'license');

        $params['content'] = $setting->item_value;

        $client = new HttpClient();

        $response = $client->request('POST', $url, [
            'form_params' => $params,
            'http_errors' => false,
        ]);

        $content = json_decode($response->getBody()->getContents(), true);

        $code = $content['code'] ?? 0;

        if ($code !== 0) {
            $setting->item_value = '';
            $setting->update();
        }
    }

}
