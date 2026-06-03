<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Auth;

use App\Models\User as UserModel;
use App\Models\UserToken as UserTokenModel;
use App\Repos\UserToken as UserTokenRepo;
use App\Services\Auth as AuthService;
use App\Traits\Client as ClientTrait;

class Api extends AuthService
{

    use ClientTrait;

    public function saveAuthInfo(UserModel $user)
    {
        $token = $this->generateToken($user->id);

        $lifetime = $this->getTokenLifetime();

        $clientType = $this->getClientType();

        $this->logoutSpecialClients($user->id, $clientType);

        $this->createUserToken($user->id, $token, $lifetime);

        $cache = $this->getCache();

        $key = $this->getTokenCacheKey($token);

        $authInfo = [
            'id' => $user->id,
            'name' => $user->name,
        ];

        $cache->save($key, $authInfo, $lifetime);

        return $token;
    }

    public function clearAuthInfo()
    {
        $token = $this->request->getHeader('X-Token');

        if (empty($token)) return null;

        $cache = $this->getCache();

        $key = $this->getTokenCacheKey($token);

        $cache->delete($key);
    }

    public function getAuthInfo()
    {
        $token = $this->request->getHeader('X-Token');

        if (empty($token)) return null;

        $cache = $this->getCache();

        $key = $this->getTokenCacheKey($token);

        $authInfo = $cache->get($key);

        return $authInfo ?: null;
    }

    public function logoutSpecialClients($userId, $clientType)
    {
        $settings = $this->getSettings('oauth.local');

        $clientLimit = max($settings['mutex_client_limit'], 1);

        if ($settings['mutex_login'] == 0) return;

        $repo = new UserTokenRepo();

        $records = $repo->findUserActiveTokens($userId);

        if ($records->count() < $clientLimit) return;

        $shouldKickCount = $records->count() - $clientLimit;

        $kickedCount = 0;

        $cache = $this->getCache();

        foreach ($records as $record) {
            if ($record->client_type == $clientType && $kickedCount < $shouldKickCount) {
                $key = $this->getTokenCacheKey($record->token);
                $cache->delete($key);
                $kickedCount++;
            }
        }
    }

    public function logoutAllClients($userId)
    {
        $repo = new UserTokenRepo();

        $records = $repo->findUserActiveTokens($userId);

        if ($records->count() == 0) return;

        $cache = $this->getCache();

        foreach ($records as $record) {
            $key = $this->getTokenCacheKey($record->token);
            $cache->delete($key);
        }
    }

    protected function createUserToken($userId, $token, $lifetime)
    {
        $userToken = new UserTokenModel();

        $userToken->user_id = $userId;
        $userToken->token = $token;
        $userToken->client_type = $this->getClientType();
        $userToken->client_ip = $this->getClientIp();
        $userToken->expire_time = time() + $lifetime;

        $userToken->create();
    }

    protected function generateToken($userId)
    {
        return md5(uniqid() . time() . $userId);
    }

    protected function getTokenLifetime()
    {
        $config = $this->getConfig();

        return $config->path('token.lifetime') ?: 7 * 86400;
    }

    protected function getTokenCacheKey($token)
    {
        return "_PHCR_TOKEN_:{$token}";
    }

}
