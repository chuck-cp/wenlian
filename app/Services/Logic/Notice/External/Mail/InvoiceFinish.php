<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\Mail;

use App\Services\Mailer as MailerService;

class InvoiceFinish extends MailerService
{

    public function handle(array $params)
    {
        $email = $params['invoice']['post_email'];

        $subject = $this->formatSubject('您申请的发票已开具，请查收！');

        $placeholder = '您申请的发票已开具，详情见附件！发票抬头：{1}，发票金额：￥{2}';

        $content = kg_ph_replace($placeholder, [
            '1' => $params['invoice_account']['head_name'],
            '2' => $params['invoice']['amount'],
        ]);

        $content = $this->formatContent($content);

        $file = $params['invoice']['voucher'];

        return $this->send($email, $subject, $content, $file);
    }

}
