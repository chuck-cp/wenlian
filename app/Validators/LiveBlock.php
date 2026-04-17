<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Repos\LiveBlock as LiveBlockRepo;

class LiveBlock extends Validator
{

    public function checkLiveBlock($courseId, $userId)
    {
        $repo = new LiveBlockRepo();

        $block = $repo->findByCourseUser($courseId, $userId);

        if (!$block) {
            throw new BadRequestException('live_block.not_found');
        }

        return $block;
    }

    public function checkCourse($id)
    {
        $validator = new Course();

        return $validator->checkCourse($id);
    }

    public function checkUser($name)
    {
        $validator = new Account();

        $account = $validator->checkAccount($name);

        $validator = new User();

        return $validator->checkUser($account->id);
    }

    public function checkExpiry($expiry)
    {
        $value = $this->filter->sanitize($expiry, ['trim', 'int']);

        if ($value < 1) {
            throw new BadRequestException('live_block.invalid_expiry');
        }

        return intval($value);
    }

}
