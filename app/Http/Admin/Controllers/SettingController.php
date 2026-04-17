<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Setting as SettingService;

/**
 * @RoutePrefix("/admin/setting")
 */
class SettingController extends Controller
{

    /**
     * @Route("/site", name="admin.setting.site")
     */
    public function siteAction()
    {
        $section = 'site';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $site = $settingService->getSettings($section);

            $site['url'] = $site['url'] ?: kg_site_url();

            $this->view->setVar('site', $site);
        }
    }

    /**
     * @Route("/secret", name="admin.setting.secret")
     */
    public function secretAction()
    {
        $section = 'secret';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $secret = $settingService->getSettings($section);

            $this->view->setVar('secret', $secret);
        }
    }

    /**
     * @Route("/storage", name="admin.setting.storage")
     */
    public function storageAction()
    {
        $section = 'cos';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateStorageSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $cos = $settingService->getSettings($section);

            $this->view->setVar('cos', $cos);
        }
    }

    /**
     * @Route("/vod", name="admin.setting.vod")
     */
    public function vodAction()
    {
        $section = 'vod';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $vod = $settingService->getSettings($section);

            $this->view->setVar('vod', $vod);
        }
    }

    /**
     * @Route("/live", name="admin.setting.live")
     */
    public function liveAction()
    {
        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $section = $this->request->getPost('section', 'string');

            $data = $this->request->getPost();

            $settingService->updateLiveSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $push = $settingService->getLiveSettings('live.push');
            $pull = $settingService->getLiveSettings('live.pull');
            $notify = $settingService->getLiveSettings('live.notify');

            $this->view->setVar('push', $push);
            $this->view->setVar('pull', $pull);
            $this->view->setVar('notify', $notify);
        }
    }

    /**
     * @Route("/pay", name="admin.setting.pay")
     */
    public function payAction()
    {
        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $section = $this->request->getPost('section', 'string');

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $alipay = $settingService->getAlipaySettings();
            $wxpay = $settingService->getWxpaySettings();

            $this->view->setVar('alipay', $alipay);
            $this->view->setVar('wxpay', $wxpay);
        }
    }

    /**
     * @Route("/sms", name="admin.setting.sms")
     */
    public function smsAction()
    {
        $section = 'sms';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $sms = $settingService->getSettings($section);

            $this->view->setVar('sms', $sms);
        }
    }

    /**
     * @Route("/mail", name="admin.setting.mail")
     */
    public function mailAction()
    {
        $section = 'mail';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $mail = $settingService->getSettings($section);

            $this->view->setVar('mail', $mail);
        }
    }

    /**
     * @Route("/exam", name="admin.setting.exam")
     */
    public function examAction()
    {
        $section = 'exam';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $exam = $settingService->getSettings($section);

            $this->view->setVar('exam', $exam);
        }
    }

    /**
     * @Route("/point", name="admin.setting.point")
     */
    public function pointAction()
    {
        $section = 'point';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $point = $settingService->getSettings($section);

            $this->view->setVar('point', $point);
        }
    }

    /**
     * @Route("/oauth", name="admin.setting.oauth")
     */
    public function oauthAction()
    {
        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $section = $this->request->getPost('section', 'string');

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $qqAuth = $settingService->getQQAuthSettings();
            $weixinAuth = $settingService->getWeixinAuthSettings();
            $weiboAuth = $settingService->getWeiboAuthSettings();
            $localAuth = $settingService->getLocalAuthSettings();

            $this->view->setVar('qq_auth', $qqAuth);
            $this->view->setVar('weixin_auth', $weixinAuth);
            $this->view->setVar('weibo_auth', $weiboAuth);
            $this->view->setVar('local_auth', $localAuth);
        }
    }

    /**
     * @Route("/security", name="admin.setting.security")
     */
    public function securityAction()
    {
        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $section = $this->request->getPost('section', 'string');

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $throttle = $settingService->getSettings('security.throttle');
            $blacklist = $settingService->getSettings('security.blacklist');
            $audit = $settingService->getSettings('security.audit');

            $this->view->setVar('throttle', $throttle);
            $this->view->setVar('blacklist', $blacklist);
            $this->view->setVar('audit', $audit);
        }
    }

    /**
     * @Route("/mobile", name="admin.setting.mobile")
     */
    public function mobileAction()
    {
        $section = 'mobile';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $tab = $this->request->getPost('tab', 'trim', 'basic');

            $settingService->updateMobileSettings($section, $tab, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $mobile = $settingService->getSettings($section);

            $this->view->pick('setting/mobile');
            $this->view->setVar('mobile', $mobile);
        }
    }

    /**
     * @Route("/wechat/oa", name="admin.setting.wechat_oa")
     */
    public function wechatOaAction()
    {
        $section = 'wechat.oa';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateWechatOASettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $oa = $settingService->getWechatOASettings();

            $this->view->pick('setting/wechat_oa');
            $this->view->setVar('oa', $oa);
        }
    }

    /**
     * @Route("/wechat/mp", name="admin.setting.wechat_mp")
     */
    public function wechatMpAction()
    {
        $section = 'wechat.mp';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateWechatMpSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $mp = $settingService->getSettings($section);

            $this->view->pick('setting/wechat_mp');
            $this->view->setVar('mp', $mp);
        }
    }

    /**
     * @Route("/robot", name="admin.setting.robot")
     */
    public function robotAction()
    {
        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $section = $this->request->getPost('section', 'string');

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $weworkRobot = $settingService->getSettings('wework.robot');
            $dingtalkRobot = $settingService->getSettings('dingtalk.robot');

            $this->view->setVar('wework_robot', $weworkRobot);
            $this->view->setVar('dingtalk_robot', $dingtalkRobot);
        }
    }

    /**
     * @Route("/contact", name="admin.setting.contact")
     */
    public function contactAction()
    {
        $section = 'contact';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $contact = $settingService->getSettings($section);

            $this->view->setVar('contact', $contact);
        }
    }

    /**
     * @Route("/affiliate", name="admin.setting.affiliate")
     */
    public function affiliateAction()
    {
        $section = 'affiliate';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $affiliate = $settingService->getSettings($section);

            $this->view->setVar('affiliate', $affiliate);
        }
    }

    /**
     * @Route("/withdraw", name="admin.setting.withdraw")
     */
    public function withdrawAction()
    {
        $section = 'withdraw';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $withdraw = $settingService->getSettings($section);

            $this->view->setVar('withdraw', $withdraw);
        }
    }

    /**
     * @Route("/invoice", name="admin.setting.invoice")
     */
    public function invoiceAction()
    {
        $section = 'invoice';

        $settingService = new SettingService();

        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $settingService->updateSettings($section, $data);

            return $this->jsonSuccess(['msg' => '更新配置成功']);

        } else {

            $invoice = $settingService->getSettings($section);

            $this->view->setVar('invoice', $invoice);
        }
    }

}
