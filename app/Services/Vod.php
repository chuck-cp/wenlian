<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services;

use Firebase\JWT\JWT;
use Phalcon\Logger\Adapter\File as FileLogger;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Vod\V20180717\Models\ConfirmEventsRequest;
use TencentCloud\Vod\V20180717\Models\DeleteMediaRequest;
use TencentCloud\Vod\V20180717\Models\DescribeMediaInfosRequest;
use TencentCloud\Vod\V20180717\Models\DescribeTaskDetailRequest;
use TencentCloud\Vod\V20180717\Models\DescribeTranscodeTemplatesRequest;
use TencentCloud\Vod\V20180717\Models\EventContent;
use TencentCloud\Vod\V20180717\Models\MediaInfo;
use TencentCloud\Vod\V20180717\Models\ProcessMediaRequest;
use TencentCloud\Vod\V20180717\Models\PullEventsRequest;
use TencentCloud\Vod\V20180717\VodClient;

class Vod extends Service
{

    const END_POINT = 'vod.tencentcloudapi.com';

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var VodClient
     */
    protected $client;

    /**
     * @var FileLogger
     */
    protected $logger;

    public function __construct()
    {
        $this->settings = $this->getSettings('vod');

        /**
         * 必须强制整型，不然接口会报错
         */
        if (!empty($this->settings['sub_app_id'])) {
            $this->settings['sub_app_id'] = (int)$this->settings['sub_app_id'];
        }

        $this->logger = $this->getLogger('vod');

        $this->client = $this->getVodClient();
    }

    /**
     * 配置测试
     *
     * @return bool
     */
    public function test()
    {
        try {

            $request = new DescribeTranscodeTemplatesRequest();

            $request->setSubAppId($this->settings['sub_app_id']);

            $params = '{}';

            $request->fromJsonString($params);

            $response = $this->client->DescribeTranscodeTemplates($request);

            $this->logger->debug('Describe Transcode Templates Response: ' . $response->toJsonString());

            $result = $response->TotalCount > 0;

        } catch (TencentCloudSDKException $e) {

            $this->logger->error('Describe Transcode Templates Exception: ' . kg_json_encode([
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage(),
                    'requestId' => $e->getRequestId(),
                ]));

            $result = false;
        }

        return $result;
    }

    /**
     * 获取上传签名
     *
     * @link https://cloud.tencent.com/document/product/266/9221
     * @return string
     */
    public function getUploadSignature()
    {
        $secret = $this->getSettings('secret');

        $secretId = $secret['secret_id'];
        $secretKey = $secret['secret_key'];

        $params = [
            'secretId' => $secretId,
            'currentTimeStamp' => time(),
            'expireTime' => time() + 86400,
            'random' => rand(1000, 9999),
        ];

        if (!empty($this->settings['sub_app_id'])) {
            $params['vodSubAppId'] = (int)$this->settings['sub_app_id'];
        }

        $original = http_build_query($params);

        $hash = hash_hmac('SHA1', $original, $secretKey, true);

        return base64_encode($hash . $original);
    }

    /**
     * 获取加密播放签名
     *
     * @link https://cloud.tencent.com/document/product/266/45554
     * @param string $fileId
     * @return string|void
     */
    public function getTcplayerSignature($fileId)
    {
        $appId = (int)$this->settings['sub_app_id']; // 必须为整型
        $expiry = $this->settings['key_anti_expiry'] ?: 3600 * 3;
        $playKey = $this->settings['encrypt_play_key'];
        $templateId = (int)$this->settings['encrypt_tpl_id']; // 必须为整数

        $currentTime = time();
        $signExpire = $currentTime + $expiry;
        $urlTimeExpire = dechex($signExpire);

        $tryTime = 0; // 试看时间，0不限制
        $ipLimit = (int)$this->settings['key_anti_ip_limit']; // ip数量限制，0不限制
        $random = uniqid(); // 随机数

        $myTryTime = $tryTime;
        $myIpLimit = $ipLimit > 0 && $ipLimit < 10 ? $ipLimit : 9;

        $contentInfo = [
            'audioVideoType' => 'ProtectedAdaptive',
            'drmAdaptiveInfo' => ['privateEncryptionDefinition' => $templateId],
        ];

        $accessInfo = [
            'us' => $random,
            't' => $urlTimeExpire,
            'exper' => $myTryTime,
            'rlimit' => $myIpLimit,
        ];

        $payload = [
            'appId' => $appId,
            'fileId' => $fileId,
            'contentInfo' => $contentInfo,
            'currentTimeStamp' => $currentTime,
            'expireTimeStamp' => $signExpire,
            'urlAccessInfo' => $accessInfo,
        ];

        return JWT::encode($payload, $playKey, 'HS256');
    }

    /**
     * 获取DrmToken
     *
     * @link https://cloud.tencent.com/document/product/266/103884
     * @param string $fileId
     * @return string
     */
    public function getDrmToken($fileId)
    {
        $appId = (int)$this->settings['sub_app_id'];
        $expiry = $this->settings['key_anti_expiry'] ?: 3600 * 3;
        $playKey = $this->settings['encrypt_play_key'];

        $currentTime = time();
        $expireTime = $currentTime + $expiry;
        $random = rand(1000, 9999);

        $header = json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT',
        ]);

        $base64Header = JWT::urlsafeB64Encode($header);

        $payload = json_encode([
            'type' => 'DrmToken',
            'appId' => $appId,
            'fileId' => $fileId,
            'currentTimeStamp' => $currentTime,
            'expireTimeStamp' => $expireTime,
            'random' => $random,
            'issuer' => 'client',
        ]);

        $base64Payload = JWT::urlsafeB64Encode($payload);

        $data = sprintf('%s.%s', $base64Header, $base64Payload);

        $signature = JWT::sign($data, $playKey);

        $base64Signature = JWT::urlsafeB64Encode($signature);

        return implode('~', [$base64Header, $base64Payload, $base64Signature]);
    }

    /**
     * 获取Drm播放地址
     *
     * @param string $url
     * @param string $fileId
     * @return string
     */
    public function getDrmPlayUrl($url, $fileId)
    {
        $playUrl = $this->getPlayUrl($url);

        $token = $this->getDrmToken($fileId);

        list($path, $query) = explode('?', $playUrl);

        $dirname = pathinfo($path, PATHINFO_DIRNAME);
        $basename = pathinfo($path, PATHINFO_BASENAME);

        return sprintf('%s/voddrm.token.%s.%s?%s', $dirname, $token, $basename, $query);
    }

    /**
     * 获取播放地址
     *
     * @link https://cloud.tencent.com/document/product/266/14047
     * @param string $url
     * @return string
     */
    public function getPlayUrl($url)
    {
        if ($this->settings['key_anti_enabled'] == 0) {
            return $url;
        }

        $key = $this->settings['key_anti_key'];
        $expiry = $this->settings['key_anti_expiry'] ?: 3600 * 3;

        $path = parse_url($url, PHP_URL_PATH);
        $pos = strrpos($path, '/');
        $fileName = substr($path, $pos + 1);
        $dirName = str_replace($fileName, '', $path);

        $tryTime = 0; // 试看时间，0不限制
        $expiredTime = dechex(time() + $expiry);
        $ipLimit = (int)$this->settings['key_anti_ip_limit']; // ip数量限制，0不限制
        $random = uniqid(); // 随机数

        /**
         * 腾讯坑爹的参数类型和文档，先凑合吧
         * 不限制试看 => 必须exper=0（不能设置为空）
         * 不限制IP => 必须rlimit为空（不能设置为0）
         */
        $myTryTime = $tryTime;
        $myIpLimit = $ipLimit > 0 && $ipLimit < 10 ? $ipLimit : 9;
        $sign = $key . $dirName . $expiredTime . $myTryTime . $myIpLimit . $random;

        $query = [
            't' => $expiredTime,
            'exper' => $myTryTime,
            'rlimit' => $myIpLimit,
            'us' => $random,
            'sign' => md5($sign),
        ];

        return $url . '?' . http_build_query($query);
    }

    /**
     * 拉取事件
     *
     * @link https://cloud.tencent.com/document/product/266/33433
     * @return bool|EventContent[]
     */
    public function pullEvents()
    {
        try {

            $request = new PullEventsRequest();

            $request->setSubAppId($this->settings['sub_app_id']);

            $params = '{}';

            $request->fromJsonString($params);

            $this->logger->debug('Pull Events Request: ' . $params);

            $response = $this->client->PullEvents($request);

            $this->logger->debug('Pull Events Response: ' . $response->toJsonString());

            $result = $response->getEventSet();

        } catch (TencentCloudSDKException $e) {

            $this->logger->error('Pull Events Exception: ' . kg_json_encode([
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage(),
                    'requestId' => $e->getRequestId(),
                ]));

            $result = false;
        }

        return $result;
    }

    /**
     * 确认事件
     *
     * @link https://cloud.tencent.com/document/product/266/33434
     * @param array $eventHandles
     * @return array|bool
     */
    public function confirmEvents($eventHandles)
    {
        try {

            $request = new ConfirmEventsRequest();

            $request->setSubAppId($this->settings['sub_app_id']);

            $params = json_encode(['EventHandles' => $eventHandles]);

            $request->fromJsonString($params);

            $this->logger->debug('Confirm Events Request: ' . $params);

            $response = $this->client->ConfirmEvents($request);

            $this->logger->debug('Confirm Events Response: ' . $response->toJsonString());

            $result = json_decode($response->toJsonString(), true);

        } catch (TencentCloudSDKException $e) {

            $this->logger->error('Confirm Events Exception: ' . kg_json_encode([
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage(),
                    'requestId' => $e->getRequestId(),
                ]));

            $result = false;
        }

        return $result;
    }

    /**
     * 删除媒体
     *
     * @link https://cloud.tencent.com/document/product/266/31764
     * @param string $fileId
     * @param array $deleteParts
     * @return bool
     */
    public function deleteMedia($fileId, $deleteParts = [])
    {
        try {

            $request = new DeleteMediaRequest();

            $request->setSubAppId($this->settings['sub_app_id']);

            $query = ['FileId' => $fileId];

            if (count($deleteParts) > 0) {
                $query['DeleteParts'] = $deleteParts;
            }

            $params = json_encode($query);

            $request->fromJsonString($params);

            $this->logger->debug('Delete Media Request: ' . $params);

            $response = $this->client->DeleteMedia($request);

            $this->logger->debug('Delete Media Response: ' . $response->toJsonString());

            $result = !empty($response->RequestId);

        } catch (TencentCloudSDKException $e) {

            $this->logger->error('Delete Media : ' . kg_json_encode([
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage(),
                    'requestId' => $e->getRequestId(),
                ]));

            $result = false;
        }

        return $result;
    }

    /**
     * 获取媒体信息
     *
     * @param string $fileId
     * @return MediaInfo|bool
     */
    public function getMediaInfo($fileId)
    {
        try {

            $request = new DescribeMediaInfosRequest();

            $request->setSubAppId($this->settings['sub_app_id']);

            $fileIds = [$fileId];

            $params = json_encode(['FileIds' => $fileIds]);

            $request->fromJsonString($params);

            $this->logger->debug('Describe Media Info Request: ' . $params);

            $response = $this->client->DescribeMediaInfos($request);

            $this->logger->debug('Describe Media Info Response: ' . $response->toJsonString());

            if (!$response->getMediaInfoSet()) {
                return false;
            }

            /**
             * @var $result MediaInfo
             */
            $result = $response->getMediaInfoSet()[0];

            if (!$result->getMetaData()) {
                return false;
            }

        } catch (TencentCloudSDKException $e) {

            $this->logger->error('Describe Media Info Exception: ' . kg_json_encode([
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage(),
                    'requestId' => $e->getRequestId(),
                ]));

            $result = false;
        }

        return $result;
    }

    /**
     * 获取任务信息
     *
     * @param string $taskId
     * @return array|bool
     */
    public function getTaskInfo($taskId)
    {
        try {

            $request = new DescribeTaskDetailRequest();

            $request->setSubAppId($this->settings['sub_app_id']);

            $params = json_encode(['TaskId' => $taskId]);

            $request->fromJsonString($params);

            $this->logger->debug('Describe Task Detail Request: ' . $params);

            $response = $this->client->DescribeTaskDetail($request);

            $this->logger->debug('Describe Task Detail Response: ' . $response->toJsonString());

            $result = json_decode($response->toJsonString(), true);

            if (!isset($result['TaskType'])) {
                return false;
            }

        } catch (TencentCloudSDKException $e) {

            $this->logger->error('Describe Task Detail Exception: ' . kg_json_encode([
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage(),
                    'requestId' => $e->getRequestId(),
                ]));

            $result = false;
        }

        return $result;
    }

    /**
     * 创建视频加密任务
     *
     * @param string $fileId
     * @return string|bool
     */
    public function createEncryptVideoTask($fileId)
    {
        $mediaInfo = $this->getMediaInfo($fileId);

        $metaData = $mediaInfo->getMetaData();

        if (!$metaData) return false;

        $encryptTemplate = $this->getVideoEncryptTemplate();

        if (!$encryptTemplate) return false;

        $watermarkTemplate = $this->getWatermarkTemplate();

        $adsTaskSet = [];

        $adsTaskSetItem = [
            'Definition' => $encryptTemplate,
        ];

        if ($watermarkTemplate) {
            $adsTaskSetItem['WatermarkSet'] = [
                ['Definition' => $watermarkTemplate],
            ];
        }

        $adsTaskSet[] = $adsTaskSetItem;

        $params = json_encode([
            'FileId' => $fileId,
            'MediaProcessTask' => [
                'AdaptiveDynamicStreamingTaskSet' => $adsTaskSet,
            ],
        ]);

        try {

            $request = new ProcessMediaRequest();

            $request->setSubAppId($this->settings['sub_app_id']);

            $request->fromJsonString($params);

            $this->logger->debug('Process Media Request: ' . $params);

            $response = $this->client->ProcessMedia($request);

            $this->logger->debug('Process Media Response: ' . $response->toJsonString());

            $result = $response->TaskId ?: false;

        } catch (TencentCloudSDKException $e) {

            $this->logger->error('Process Media Exception: ' . kg_json_encode([
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage(),
                    'requestId' => $e->getRequestId(),
                ]));

            $result = false;
        }

        return $result;
    }

    /**
     * 创建视频转码任务
     *
     * @param string $fileId
     * @return string|bool
     */
    public function createTransVideoTask($fileId)
    {
        $mediaInfo = $this->getMediaInfo($fileId);

        $metaData = $mediaInfo->getMetaData();

        if (!$metaData) return false;

        $videoTransTemplates = $this->getVideoTransTemplates();

        $watermarkTemplate = $this->getWatermarkTemplate();

        $transcodeTaskSet = [];

        foreach ($videoTransTemplates as $key => $template) {

            $caseA = $metaData->getHeight() >= $template['height'];
            $caseB = $metaData->getBitrate() >= 1000 * $template['bit_rate'];

            if ($caseA || $caseB) {

                $item = ['Definition' => $key];

                if ($watermarkTemplate) {
                    $item['WatermarkSet'][] = ['Definition' => $watermarkTemplate];
                }

                $transcodeTaskSet[] = $item;
            }
        }

        /**
         * 无匹配转码模板，取第一项转码
         */
        if (empty($transcodeTaskSet)) {

            $keys = array_keys($videoTransTemplates);

            $item = ['Definition' => $keys[0]];

            if ($watermarkTemplate) {
                $item['WatermarkSet'][] = ['Definition' => $watermarkTemplate];
            }

            $transcodeTaskSet[] = $item;
        }

        $params = json_encode([
            'FileId' => $fileId,
            'MediaProcessTask' => [
                'TranscodeTaskSet' => $transcodeTaskSet,
            ],
        ]);

        try {

            $request = new ProcessMediaRequest();

            $request->setSubAppId($this->settings['sub_app_id']);

            $request->fromJsonString($params);

            $this->logger->debug('Process Media Request: ' . $params);

            $response = $this->client->ProcessMedia($request);

            $this->logger->debug('Process Media Response: ' . $response->toJsonString());

            $result = $response->TaskId ?: false;

        } catch (TencentCloudSDKException $e) {

            $this->logger->error('Process Media Exception ' . kg_json_encode([
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage(),
                    'requestId' => $e->getRequestId(),
                ]));

            $result = false;
        }

        return $result;
    }

    /**
     * 创建音频转码任务
     *
     * @param string $fileId
     * @return string|bool
     */
    public function createTransAudioTask($fileId)
    {
        $mediaInfo = $this->getMediaInfo($fileId);

        $metaData = $mediaInfo->getMetaData();

        if (!$metaData) return false;

        $audioTransTemplates = $this->getAudioTransTemplates();

        $transcodeTaskSet = [];

        foreach ($audioTransTemplates as $key => $template) {

            if ($metaData->getBitrate() >= 1000 * $template['bit_rate']) {

                $item = ['Definition' => $key];

                $transcodeTaskSet[] = $item;
            }
        }

        /**
         * 无匹配转码模板，取第一项转码
         */
        if (empty($transcodeTaskSet)) {

            $keys = array_keys($audioTransTemplates);

            $item = ['Definition' => $keys[0]];

            $transcodeTaskSet[] = $item;
        }

        $params = json_encode([
            'FileId' => $fileId,
            'MediaProcessTask' => [
                'TranscodeTaskSet' => $transcodeTaskSet,
            ],
        ]);

        try {

            $request = new ProcessMediaRequest();

            $request->setSubAppId($this->settings['sub_app_id']);

            $request->fromJsonString($params);

            $this->logger->debug('Process Media Request ' . $params);

            $response = $this->client->ProcessMedia($request);

            $this->logger->debug('Process Media Response ' . $response->toJsonString());

            $result = $response->TaskId ?: false;

        } catch (TencentCloudSDKException $e) {

            $this->logger->error('Process Media Exception ' . kg_json_encode([
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage(),
                    'requestId' => $e->getRequestId(),
                ]));

            $result = false;
        }

        return $result;
    }

    /**
     * 获取视频加密模板
     *
     * @return mixed
     */
    public function getVideoEncryptTemplate()
    {
        $result = null;

        if ($this->settings['encrypt_trans_enabled'] == 1 && $this->settings['encrypt_tpl_id'] > 0) {
            $result = (int)$this->settings['encrypt_tpl_id'];
        }

        return $result;
    }

    /**
     * 获取水印模板
     *
     * @return mixed
     */
    public function getWatermarkTemplate()
    {
        $result = null;

        if ($this->settings['wmk_enabled'] == 1 && $this->settings['wmk_tpl_id'] > 0) {
            $result = (int)$this->settings['wmk_tpl_id'];
        }

        return $result;
    }

    /***
     * 获取视频转码模板
     *
     * @return array
     */
    public function getVideoTransTemplates()
    {
        $normalTemplates = [
            'hls' => [
                100220 => ['quality' => 'fd', 'height' => 540, 'bit_rate' => 1000, 'frame_rate' => 25],
                100230 => ['quality' => 'sd', 'height' => 720, 'bit_rate' => 1800, 'frame_rate' => 25],
                100240 => ['quality' => 'hd', 'height' => 1080, 'bit_rate' => 2500, 'frame_rate' => 25],
            ],
            'mp4' => [
                100020 => ['quality' => 'fd', 'height' => 540, 'bit_rate' => 1000, 'frame_rate' => 25],
                100030 => ['quality' => 'sd', 'height' => 720, 'bit_rate' => 1800, 'frame_rate' => 25],
                100040 => ['quality' => 'hd', 'height' => 1080, 'bit_rate' => 2500, 'frame_rate' => 25],
            ]
        ];

        /**
         * 极速高清暂时没有预设HLS封装
         */
        $tesTemplates = [
            'mp4' => [
                100820 => ['quality' => 'fd', 'height' => 540, 'bit_rate' => 1000, 'frame_rate' => 25],
                100830 => ['quality' => 'sd', 'height' => 720, 'bit_rate' => 1800, 'frame_rate' => 25],
                100840 => ['quality' => 'hd', 'height' => 1080, 'bit_rate' => 2500, 'frame_rate' => 25],
            ]
        ];

        $format = $this->settings['video_format'] ?: 'hls';

        if ($this->settings['transcode_type'] == 'tes') {
            $templates = $tesTemplates[$format] ?? $tesTemplates['mp4'];
        } else {
            $templates = $normalTemplates[$format] ?? $normalTemplates['hls'];
        }

        $quality = ['sd'];

        if (!empty($this->settings['video_quality'])) {
            $quality = json_decode($this->settings['video_quality'], true);
        }

        $result = [];

        foreach ($templates as $key => $item) {
            if (in_array($item['quality'], $quality)) {
                $result[$key] = $item;
            }
        }

        return $result;
    }

    /**
     * 获取音频转码模板
     *
     * @return array
     */
    public function getAudioTransTemplates()
    {
        $mp3Templates = [
            1010 => ['quality' => 'sd', 'bit_rate' => 128, 'sample_rate' => 44100],
        ];

        $m4aTemplates = [
            1120 => ['quality' => 'sd', 'bit_rate' => 96, 'sample_rate' => 44100],
        ];

        $format = $this->settings['audio_format'] ?: 'mp3';

        $quality = !empty($this->settings['audio_quality']) ? json_decode($this->settings['audio_quality'], true) : ['sd'];

        $templates = $format == 'mp3' ? $mp3Templates : $m4aTemplates;

        $result = [];

        foreach ($templates as $key => $item) {
            if (in_array($item['quality'], $quality)) {
                $result[$key] = $item;
            }
        }

        return $result;
    }

    /**
     * 获取VodClient
     *
     * @return VodClient
     */
    public function getVodClient()
    {
        $secret = $this->getSettings('secret');

        $secretId = $secret['secret_id'];
        $secretKey = $secret['secret_key'];

        $region = $this->settings['storage_type'] == 'fixed' ? $this->settings['storage_region'] : '';

        $credential = new Credential($secretId, $secretKey);

        $httpProfile = new HttpProfile();

        $httpProfile->setEndpoint(self::END_POINT);

        $clientProfile = new ClientProfile();

        $clientProfile->setHttpProfile($httpProfile);

        return new VodClient($credential, $region, $clientProfile);
    }

}
