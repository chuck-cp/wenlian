<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Services\Logic\Service as LogicService;

class Pilot extends LogicService
{

    public function getAuthCode($paperUserId)
    {
        $text = sprintf('%s:%s', $paperUserId, time() + 7200);

        return $this->crypt->encryptBase64($text, null, true);
    }

    public function checkAuthCode($paperUserId, $authCode)
    {
        if (empty($paperUserId) || empty($authCode)) {
            return false;
        }

        $authCode = $this->crypt->decryptBase64($authCode, null, true);

        list($orgPaperUserId, $orgExpireTime) = explode(':', $authCode);

        $case1 = $orgPaperUserId == $paperUserId;
        $case2 = time() < $orgExpireTime;

        return $case1 && $case2;
    }

}
