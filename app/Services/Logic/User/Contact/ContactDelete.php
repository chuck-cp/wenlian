<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Contact;

use App\Services\Logic\Service as LogicService;
use App\Validators\UserContact as UserContactValidator;

class ContactDelete extends LogicService
{

    public function handle($id)
    {
        $user = $this->getLoginUser();

        $validator = new UserContactValidator();

        $contact = $validator->checkUserContact($id);

        $validator->checkOwner($user->id, $contact->user_id);

        $contact->deleted = 1;

        $contact->update();

        return $contact;
    }

}
