<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\Sms;

use App\Repos\Account as AccountRepo;
use App\Services\Smser;

class PaperGradeFinish extends Smser
{

    protected $templateCode = 'paper_grade_finish';

    /**
     * @param array $params
     * @return bool|null
     */
    public function handle(array $params)
    {
        $accountRepo = new AccountRepo();

        $account = $accountRepo->findById($params['user']['id']);

        if (!$account->phone) return null;

        $templateId = $this->getTemplateId($this->templateCode);

        /**
         * 阅卷完成，试卷名称：{1} 试卷总分：{2}，用户得分：{3}，请登录系统查看详情。
         */
        $params = [
            $params['paper']['title'],
            $params['paper_user']['paper_score'],
            $params['paper_user']['user_score'],
        ];

        return $this->send($account->phone, $templateId, $params);
    }

}
