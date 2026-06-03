<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Contact;

use App\Services\Logic\Service as LogicService;
use App\Validators\UserContact as UserContactValidator;

class ContactInfo extends LogicService
{

    public function handle($id)
    {
        $user = $this->getLoginUser();

        $validator = new UserContactValidator();

        $contact = $validator->checkUserContact($id);

        $validator->checkOwner($user->id, $contact->user_id);

        return [
            'id' => $contact->id,
            'name' => $contact->name,
            'phone' => $contact->phone,
            'add_province' => $contact->add_province,
            'add_city' => $contact->add_city,
            'add_county' => $contact->add_county,
            'add_other' => $contact->add_other,
            'master' => $contact->master,
            'create_time' => $contact->create_time,
            'update_time' => $contact->update_time,
        ];
    }

}
