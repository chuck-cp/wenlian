<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\User\Contact\ContactCreate as UserContactCreateService;
use App\Services\Logic\User\Contact\ContactDelete as UserContactDeleteService;

/**
 * @RoutePrefix("/user/contact")
 */
class UserContactController extends Controller
{

    /**
     * @Post("/create", name="home.user_contact.create")
     */
    public function createAction()
    {
        $service = new UserContactCreateService();

        $service->handle();

        $location = $this->url->get(
            ['for' => 'home.uc.contact'],
            ['action' => 'list']
        );

        $content = [
            'location' => $location,
            'msg' => '添加收货地址成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="home.user_contact.delete")
     */
    public function deleteAction($id)
    {
        $service = new UserContactDeleteService();

        $service->handle($id);

        return $this->jsonSuccess(['msg' => '删除收货地址成功']);
    }

}
