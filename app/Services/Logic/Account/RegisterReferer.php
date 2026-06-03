<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Account;

use App\Models\User as UserModel;
use App\Models\UserReferer as UserRefererModel;
use App\Repos\User as UserRepo;
use App\Repos\UserReferer as UserRefererRepo;
use App\Services\Logic\Service as LogicService;

class RegisterReferer extends LogicService
{

    public function handle(UserModel $user)
    {
        try {

            $referUser = $this->getReferUser();

            if (!$referUser) return;

            $refererRepo = new UserRefererRepo();

            $referer = $refererRepo->findByUserParentLevel($user->id, 1);

            if ($referer) return;

            $this->db->begin();

            /**
             * 创建一级用户关系
             */
            $relation = new UserRefererModel();

            $relation->user_id = $user->id;
            $relation->parent_id = $referUser->id;
            $relation->parent_level = 1;

            $relation->create();

            $referer = $refererRepo->findByUserParentLevel($referUser->id, 1);

            if (!$referer) {
                $this->db->commit();
                return;
            }

            /**
             * 创建二级用户关系
             */
            $relation = new UserRefererModel();

            $relation->user_id = $user->id;
            $relation->parent_id = $referer->parent_id;
            $relation->parent_level = 2;

            $relation->create();

            $referer = $refererRepo->findByUserParentLevel($referer->parent_id, 1);

            if (!$referer) {
                $this->db->commit();
                return;
            }

            /**
             * 创建三级用户关系
             */
            $relation = new UserRefererModel();

            $relation->user_id = $user->id;
            $relation->parent_id = $referer->parent_id;
            $relation->parent_level = 3;

            $relation->create();

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Register Referer Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            throw new \RuntimeException('sys.trans_rollback');
        }
    }

    protected function getReferUser()
    {
        if ($this->request->isApi()) {
            $userId = $this->request->getHeader('X-Referer');
        } else {
            $userId = $this->session->get('referer');
        }

        if (empty($userId)) return null;

        $userRepo = new UserRepo();

        return $userRepo->findById($userId);
    }

}
