<?php

namespace App\Console\Tasks;

use App\Models\User as UserModel;
use App\Services\Logic\Notice\External\VipRenew as VipRenewNotice;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class VipRenewNoticeTask extends Task
{

    public function mainAction()
    {
        $users = $this->findUsers();

        if ($users->count() == 0) return;

        $notice = new VipRenewNotice();

        $todayDateTime = strtotime(date('Y-m-d'), time());

        /**
         * 距离到期[30,15,7,3]天发送续费提醒
         */
        foreach ($users as $user) {

            $expireDateTime = strtotime(date('Y-m-d', $user->vip_expiry_time));

            $scopes = [30, 15, 7, 3];

            $leftDays = ($expireDateTime - $todayDateTime) / 86400;

            if (in_array($leftDays, $scopes)) {
                $notice->createTask($user);
            }
        }
    }

    /**
     * @return ResultsetInterface|Resultset|UserModel[]
     */
    protected function findUsers()
    {
        $startTime = strtotime('+1 day');
        $endTime = strtotime('+1 month');

        return UserModel::query()
            ->betweenWhere('vip_expiry_time', $startTime, $endTime)
            ->execute();
    }

}