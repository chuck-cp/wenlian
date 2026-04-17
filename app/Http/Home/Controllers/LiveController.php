<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\Chapter\ChapterInfo as ChapterInfoService;
use App\Services\Logic\Live\LiveChat as LiveChatService;
use App\Services\Logic\Live\LiveManage as LiveManageService;
use Phalcon\Mvc\View;

/**
 * @RoutePrefix("/live")
 */
class LiveController extends Controller
{

    /**
     * @Get("/{id:[0-9]+}/manage", name="home.live.manage")
     */
    public function manageAction($id)
    {
        $service = new ChapterInfoService();

        $chapter = $service->handle($id);

        $this->view->pick('chapter/live/manage');
        $this->view->setVar('chapter', $chapter);
    }

    /**
     * @Get("/{id:[0-9]+}/users/online", name="home.live.online_users")
     */
    public function onlineUsersAction($id)
    {
        $service = new LiveChatService();

        $pager = $service->getOnlineUsers($id);

        $pager->target = 'online-user-list';

        $this->view->pick('chapter/live/online_users');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/users/blocked", name="home.live.blocked_users")
     */
    public function blockedUsersAction($id)
    {
        $service = new LiveManageService();

        $pager = $service->getBlockedUsers($id);

        $pager->target = 'blocked-user-list';

        $this->view->pick('chapter/live/blocked_users');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/chats", name="home.live.chats")
     */
    public function chatsAction($id)
    {
        $service = new LiveChatService();

        $chats = $service->getRecentChats($id);

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->pick('chapter/live/chats');
        $this->view->setVar('chats', $chats);
    }

    /**
     * @Get("/{id:[0-9]+}/stats", name="home.live.stats")
     */
    public function statsAction($id)
    {
        $service = new LiveChatService();

        $stats = $service->getStats($id);

        return $this->jsonSuccess(['stats' => $stats]);
    }

    /**
     * @Get("/{id:[0-9]+}/status", name="home.live.status")
     */
    public function statusAction($id)
    {
        $service = new LiveChatService();

        $status = $service->getLiveStatus($id);

        return $this->jsonSuccess(['status' => $status]);
    }

    /**
     * @Post("/{id:[0-9]+}/user/bind", name="home.live.bind_user")
     */
    public function bindUserAction($id)
    {
        $service = new LiveChatService();

        $service->bindUser($id);

        return $this->jsonSuccess();
    }

    /**
     * @Post("/{id:[0-9]+}/msg/send", name="home.live.send_msg")
     */
    public function sendMessageAction($id)
    {
        $service = new LiveChatService();

        $response = $service->sendMessage($id);

        return $this->jsonSuccess($response);
    }

    /**
     * @Post("/{id:[0-9]+}/settings/update", name="home.live.update_settings")
     */
    public function updateSettingsAction($id)
    {
        $service = new LiveManageService();

        $service->updateSettings($id);

        return $this->jsonSuccess(['msg' => '更新设置成功']);
    }

    /**
     * @Post ("/{id:[0-9]+}/user/block", name="home.live.block_user")
     */
    public function blockUserAction($id)
    {
        $service = new LiveManageService();

        $service->blockUser($id);

        return $this->jsonSuccess(['msg' => '用户禁言成功']);
    }

    /**
     * @Post("/{id:[0-9]+}/user/unblock", name="home.live.unblock_user")
     */
    public function unblockUserAction($id)
    {
        $service = new LiveManageService();

        $service->unblockUser($id);

        return $this->jsonSuccess(['msg' => '解除禁言成功']);
    }

}
