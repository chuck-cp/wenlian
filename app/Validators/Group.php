<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Library\Validators\Common as CommonValidator;
use App\Repos\Group as GroupRepo;

class Group extends Validator
{

    public function checkGroup($id)
    {
        $groupRepo = new GroupRepo();

        $group = $groupRepo->findById($id);

        if (!$group) {
            throw new BadRequestException('group.not_found');
        }

        return $group;
    }

    public function checkName($name)
    {
        $value = $this->filter->sanitize($name, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2) {
            throw new BadRequestException('group.name_too_short');
        }

        if ($length > 30) {
            throw new BadRequestException('group.name_too_long');
        }

        return $value;
    }

    public function checkExpiryTime($expiryTime)
    {
        $value = $this->filter->sanitize($expiryTime, ['trim', 'string']);

        if (!CommonValidator::date($value, 'Y-m-d H:i:s')) {
            throw new BadRequestException('group.invalid_expiry_time');
        }

        return strtotime($value);
    }

    public function checkPublishStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('group.invalid_publish_status');
        }

        return $status;
    }

    public function checkIfNameExists($name)
    {
        $groupRepo = new GroupRepo();

        $group = $groupRepo->findByName($name);

        if ($group) {
            throw new BadRequestException('group.name_existed');
        }
    }

}
