<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services;

use App\Library\Validators\Common as CommonValidator;
use App\Repos\Account as AccountRepo;
use App\Repos\Course as CourseRepo;
use GuzzleHttp\Client as HttpClient;
use Phalcon\Logger\Adapter\File as FileLogger;

class WeWorkNotice extends Service
{

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var FileLogger
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $enabled;

    public function __construct()
    {
        $this->settings = $this->getSettings('wework.robot');

        $this->logger = $this->getLogger('wework');

        $this->enabled = $this->settings['enabled'] == 1;
    }

    /**
     * 测试消息
     *
     * @return bool
     */
    public function test()
    {
        $params = [
            'msgtype' => 'text',
            'text' => ['content' => '我是一条测试消息啦！'],
        ];

        return $this->send($params);
    }

    /**
     * 给技术人员发消息
     *
     * @param string $content
     * @return bool
     */
    public function atTechSupport($content)
    {
        $atMobiles = $this->parseAtMobiles($this->settings['ts_mobiles']);
        $atContent = $this->buildAtContent($content, $atMobiles);

        $params = [
            'msgtype' => 'text',
            'text' => [
                'content' => $atContent,
                'mentioned_mobile_list' => $atMobiles,
            ],
        ];

        return $this->send($params);
    }

    /**
     * 给客服人员发消息
     *
     * @param string $content
     * @return bool
     */
    public function atCustomService($content)
    {
        $atMobiles = $this->parseAtMobiles($this->settings['cs_mobiles']);
        $atContent = $this->buildAtContent($content, $atMobiles);

        $params = [
            'msgtype' => 'text',
            'text' => [
                'content' => $atContent,
                'mentioned_mobile_list' => $atMobiles,
            ],
        ];

        return $this->send($params);
    }

    /**
     * 给财务人员发消息
     *
     * @param string $content
     * @return bool
     */
    public function atFinanceService($content)
    {
        $atMobiles = $this->parseAtMobiles($this->settings['fs_mobiles']);
        $atContent = $this->buildAtContent($content, $atMobiles);

        $params = [
            'msgtype' => 'text',
            'text' => [
                'content' => $atContent,
                'mentioned_mobile_list' => $atMobiles,
            ],
        ];

        return $this->send($params);
    }

    /**
     * 给课程讲师发消息
     *
     * @param int $courseId
     * @param string $content
     * @return bool
     */
    public function atCourseTeacher($courseId, $content)
    {
        $courseRepo = new CourseRepo();

        $course = $courseRepo->findById($courseId);

        $accountRepo = new AccountRepo();

        $account = $accountRepo->findById($course->teacher_id);

        if (empty($account->phone)) return false;

        $atMobiles = [$account->phone];

        $atContent = $this->buildAtContent($content, $atMobiles);

        $params = [
            'msgtype' => 'text',
            'text' => [
                'content' => $atContent,
                'mentioned_mobile_list' => $atMobiles,
            ],
        ];

        return $this->send($params);
    }

    /**
     * 发送消息
     *
     * @param array $params
     * @return bool
     */
    public function send($params)
    {
        $postUrl = $this->settings['webhook_url'];

        try {

            $client = new HttpClient();

            $response = $client->post($postUrl, ['json' => $params]);

            $content = $response->getBody()->getContents();

            $content = json_decode($content, true);

            $this->logger->debug('Send Message Request: ' . kg_json_encode($params));

            $this->logger->debug('Send Message Response: ' . kg_json_encode($content));

            $result = $content['errcode'] == 0;

            if (!$result) {
                $this->logger->error('Send Message Failed: ' . kg_json_encode($content));
            }

        } catch (\Exception $e) {

            $this->logger->error('Send Message Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            $result = false;
        }

        return $result;
    }

    /**
     * @param string $mobiles
     * @return array
     */
    protected function parseAtMobiles($mobiles)
    {
        if (empty($mobiles)) return [];

        $mobiles = str_replace(['，', '｜', '|'], ',', $mobiles);

        $mobiles = explode(',', $mobiles);

        $result = [];

        foreach ($mobiles as $mobile) {
            if (CommonValidator::phone($mobile)) {
                $result[] = $mobile;
            }
        }

        return $result;
    }

    /**
     * @param string $content
     * @param array $mobiles
     * @return string
     */
    protected function buildAtContent($content, $mobiles = [])
    {
        if (empty($mobiles)) return $content;

        $result = '';

        foreach ($mobiles as $mobile) {
            $result .= sprintf('<@%s> ', $mobile);
        }

        $result .= $content;

        return $result;
    }

}
