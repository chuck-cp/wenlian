<?php
/**
 * @copyright Copyright (c) 2024 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services;

class LoginLimit extends Service
{

    public function checkFailedLogin($id)
    {
        $settings = $this->getSettings('oauth.local');

        $cache = $this->getCache();

        $key = $this->getCacheKey($id);

        if (!$cache->exists($key)) return true;

        $failedCount = $cache->get($key);

        $maxAttempts = max($settings['failed_login_limit'], 3);

        return $maxAttempts > $failedCount;
    }

    public function incrFailedLogin($id)
    {
        $settings = $this->getSettings('oauth.local');

        $lifetime = max($settings['failed_login_lock'], 60);

        $cache = $this->getCache();

        $key = $this->getCacheKey($id);

        if (!$cache->exists($key)) {
            $cache->save($key, 1, $lifetime);
        } else {
            $cache->increment($key, 1);
        }
    }

    protected function getCacheKey(int $id)
    {
        return "login_limit:{$id}";
    }

}
