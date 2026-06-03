<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\User\Contact\ContactCreate as ContactCreateService;
use App\Services\Logic\User\Contact\ContactDelete as ContactDeleteService;
use App\Services\Logic\User\Contact\ContactInfo as ContactInfoService;

/**
 * @RoutePrefix("/api/user/contact")
 */
class UserContactController extends Controller
{

    /**
     * @Get("/{id:[0-9]+}/info", name="api.user_contact.info")
     */
    public function infoAction($id)
    {
        $service = new ContactInfoService();

        $contact = $service->handle($id);

        return $this->jsonSuccess(['contact' => $contact]);
    }

    /**
     * @Post("/create", name="api.user_contact.create")
     */
    public function createAction()
    {
        $service = new ContactCreateService();

        $contact = $service->handle();

        $service = new ContactInfoService();

        $contact = $service->handle($contact->id);

        return $this->jsonSuccess(['contact' => $contact]);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="api.user_contact.delete")
     */
    public function deleteAction($id)
    {
        $service = new ContactDeleteService();

        $service->handle($id);

        return $this->jsonSuccess();
    }

}
