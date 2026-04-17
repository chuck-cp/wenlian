<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Service as AppService;
use App\Traits\Response as ResponseTrait;

/**
 * @RoutePrefix("/api/setting")
 */
class SettingController extends Controller
{

    use ResponseTrait;

    /**
     * @Get("/site", name="api.setting.site")
     */
    public function siteAction()
    {
        $service = new AppService();

        $site = $service->getSettings('site');

        unset($site['license']);

        return $this->jsonSuccess(['site' => $site]);
    }

    /**
     * @Get("/mobile", name="api.setting.mobile")
     */
    public function mobileAction()
    {
        $service = new AppService();

        $mobile = $service->getSettings('mobile');

        $indexModule = json_decode($mobile['index_module'], true);

        /**
         * 格式化成索引数组形式
         */
        $mobile['index_module'] = [
            'top' => array_values($indexModule['top']),
            'nav' => array_values($indexModule['nav']),
            'content' => array_values($indexModule['content']),
        ];

        return $this->jsonSuccess(['mobile' => $mobile]);
    }

    /**
     * @Get("/payment", name="api.setting.payment")
     */
    public function paymentAction()
    {
        $service = new AppService();

        $alipay = $service->getSettings('pay.alipay');
        $wxpay = $service->getSettings('pay.wxpay');

        $content = [
            'alipay' => ['enabled' => $alipay['enabled']],
            'wxpay' => ['enabled' => $wxpay['enabled']],
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Get("/affiliate", name="api.setting.affiliate")
     */
    public function affiliateAction()
    {
        $service = new AppService();

        $affiliate = $service->getSettings('affiliate');

        return $this->jsonSuccess(['affiliate' => $affiliate]);
    }

    /**
     * @Get("/withdraw", name="api.setting.withdraw")
     */
    public function withdrawAction()
    {
        $service = new AppService();

        $withdraw = $service->getSettings('withdraw');

        $channels = json_decode($withdraw['channels'], true);

        $withdraw['channels'] = array_values($channels);

        return $this->jsonSuccess(['withdraw' => $withdraw]);
    }

    /**
     * @Get("/invoice", name="api.setting.invoice")
     */
    public function invoiceAction()
    {
        $service = new AppService();

        $invoice = $service->getSettings('invoice');

        $mediaTypes = json_decode($invoice['media_types'],true);
        $usageTypes = json_decode($invoice['usage_types'],true);

        $invoice['media_types'] = array_values($mediaTypes);
        $invoice['usage_types'] = array_values($usageTypes);

        return $this->jsonSuccess(['invoice' => $invoice]);
    }

    /**
     * @Get("/point", name="api.setting.point")
     */
    public function pointAction()
    {
        $service = new AppService();

        $point = $service->getSettings('point');

        $settings = [
            'enabled' => $point['enabled'],
            'consume_rule' => json_decode($point['consume_rule']),
            'event_rule' => json_decode($point['event_rule']),
        ];

        return $this->jsonSuccess(['point' => $settings]);
    }

    /**
     * @Get("/signup", name="api.setting.signup")
     */
    public function signupAction()
    {
        $service = new AppService();

        $local = $service->getSettings('oauth.local');

        $settings = [
            'register_with_phone' => $local['register_with_phone'],
            'register_with_email' => $local['register_with_email'],
        ];

        return $this->jsonSuccess(['signup' => $settings]);
    }

}
