<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services;

use App\Traits\Auth as AuthTrait;
use App\Traits\Service as ServiceTrait;
use Phalcon\Mvc\User\Component as UserComponent;

/**
 * @method array getLicenseInfo()
 */
class Service extends UserComponent
{
    use AuthTrait;
    use ServiceTrait;

    public function getMyLicenseInfo()
    {
        $licenseInfo = $this->getLicenseInfo();

        $licenseInfo['remove_copyright'] = $licenseInfo['remove_copyright'] ?? 0;
        $licenseInfo['affiliate'] = $licenseInfo['affiliate'] ?? 0;

        return $licenseInfo;
    }

}
