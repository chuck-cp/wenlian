<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services;

use App\Models\Chapter as ChapterModel;
use App\Models\ChapterVod as ChapterVodModel;
use App\Repos\Chapter as ChapterRepo;
use App\Services\Vod as VodService;
use TencentCloud\Vod\V20180717\Models\AdaptiveDynamicStreamingInfoItem;
use TencentCloud\Vod\V20180717\Models\MediaInfo;
use TencentCloud\Vod\V20180717\Models\MediaTranscodeItem;

class ChapterVod extends Service
{

    public function getPlayUrls(ChapterVodModel $vod)
    {
        /**
         * 腾讯云点播优先
         */
        if ($vod->file_id) {
            $playUrls = $this->getCosPlayUrls($vod);
            if (empty($playUrls)) {
                $this->pullMediaInfo($vod);
                $playUrls = $this->getCosPlayUrls($vod);
            }
        } else {
            $playUrls = $this->getRemotePlayUrls($vod);
        }

        /**
         *过滤播放地址为空的条目
         */
        foreach ($playUrls as $key => $value) {
            if (empty($value['url'])) unset($playUrls[$key]);
        }

        return $playUrls;
    }

    public function getCosPlayUrls(ChapterVodModel $vod)
    {
        $result = [];

        $vodService = new VodService();

        /**
         * 优先级：加密转码 -> 标准转码 -> 原始文件
         */
        if (!empty($vod->file_encrypt)) {
            foreach ($vod->file_encrypt as $file) {
                $file['url'] = $vodService->getDrmPlayUrl($file['url'], $vod->file_id);
                $result['sd'] = $file;
            }
        } elseif (!empty($vod->file_transcode)) {
            foreach ($vod->file_transcode as $file) {
                $file['url'] = $vodService->getPlayUrl($file['url']);
                $type = $this->getDefinitionType($file['height']);
                $result[$type] = $file;
            }
        } elseif (!empty($vod->file_origin)) {
            $origin = $vod->file_origin;
            $origin['url'] = $vodService->getPlayUrl($origin['url']);
            $type = $this->getDefinitionType($origin['height']);
            $result[$type] = $origin;
        }

        return $result;
    }

    public function getRemotePlayUrls(ChapterVodModel $vod)
    {
        $result = [
            'hd' => ['url' => ''],
            'sd' => ['url' => ''],
            'fd' => ['url' => ''],
        ];

        if (!empty($vod->file_remote)) {
            $result = $vod->file_remote;
        }

        return $result;
    }

    public function getTcplayerSignature(ChapterVodModel $vod)
    {
        $vodService = new VodService();

        return $vodService->getTcplayerSignature($vod->file_id);
    }

    public function pullMediaInfo(ChapterVodModel $vod)
    {
        $chapterRepo = new ChapterRepo();

        $chapter = $chapterRepo->findById($vod->chapter_id);

        $attrs = $chapter->attrs;

        $mediaInfo = $this->parseMediaInfo($vod->file_id);

        if (!empty($mediaInfo['file_origin'])) {
            $vod->file_origin = $mediaInfo['file_origin'];
            $attrs['duration'] = $mediaInfo['file_origin']['duration'] ?? 0;
        }

        if (!empty($mediaInfo['file_transcode'])) {
            $vod->file_transcode = $mediaInfo['file_transcode'];
            $attrs['transcode']['standard']['status'] = ChapterModel::TRANS_STATUS_FINISHED;
        }

        if (!empty($mediaInfo['file_encrypt'])) {
            $vod->file_encrypt = $mediaInfo['file_encrypt'];
            $attrs['transcode']['encrypt']['status'] = ChapterModel::TRANS_STATUS_FINISHED;
        }

        $vod->update();

        $vod->afterFetch();

        $chapter->attrs = $attrs;

        $chapter->update();

        $chapter->afterFetch();
    }

    public function parseMediaInfo($fileId, $type = null)
    {
        $vodService = new VodService();

        $mediaInfo = $vodService->getMediaInfo($fileId);

        $result = [
            'file_origin' => [],
            'file_transcode' => [],
            'file_encrypt' => [],
        ];

        if (!$mediaInfo) return $result;

        $fileOrigin = $this->parseFileOrigin($mediaInfo);

        if ($fileOrigin) {
            $result['file_origin'] = $fileOrigin;
        }

        $fileTranscode = $this->parseFileTranscode($mediaInfo);

        if ($fileTranscode) {
            $result['file_transcode'] = $fileTranscode;
        }

        $fileEncrypt = $this->parseFileEncrypt($mediaInfo);

        if ($fileEncrypt) {
            $result['file_encrypt'] = $fileEncrypt;
        }

        return $result[$type] ?? $result;
    }

    protected function parseFileOrigin(MediaInfo $mediaInfo)
    {
        $basicInfo = $mediaInfo->getBasicInfo();

        if (!$basicInfo) return null;

        $metaData = $mediaInfo->getMetaData();

        if (!$metaData) return null;

        return [
            'url' => $basicInfo->getMediaUrl(),
            'format' => $basicInfo->getType(),
            'width' => $metaData->getWidth(),
            'height' => $metaData->getHeight(),
            'duration' => $metaData->getDuration(),
            'size' => sprintf('%0.2f', $metaData->getSize() / 1024 / 1024),
            'rate' => intval($metaData->getBitrate() / 1024),
        ];
    }

    protected function parseFileTranscode(MediaInfo $mediaInfo)
    {
        $transcodeInfo = $mediaInfo->getTranscodeInfo();

        if (!$transcodeInfo) return null;

        /**
         * @var $items MediaTranscodeItem[]
         */
        $items = $transcodeInfo->getTranscodeSet();

        $result = [];

        foreach ($items as $item) {

            if ($item->getDefinition() == 0) {
                continue;
            }

            $result[] = [
                'url' => $item->getUrl(),
                'width' => $item->getWidth(),
                'height' => $item->getHeight(),
                'definition' => $item->getDefinition(),
                'duration' => $item->getDuration(),
                'format' => pathinfo($item->getUrl(), PATHINFO_EXTENSION),
                'size' => sprintf('%0.2f', $item->getSize() / 1024 / 1024),
                'rate' => intval($item->getBitrate() / 1024),
            ];
        }

        return $result;
    }

    protected function parseFileEncrypt(MediaInfo $mediaInfo)
    {
        $adsInfo = $mediaInfo->getAdaptiveDynamicStreamingInfo();

        if (!$adsInfo) return null;

        /**
         * @var $items AdaptiveDynamicStreamingInfoItem[]
         */
        $items = $adsInfo->getAdaptiveDynamicStreamingSet();

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'url' => $item->getUrl(),
                'definition' => $item->getDefinition(),
                'package' => $item->getPackage(),
                'drm_type' => $item->getDrmType(),
            ];
        }

        return $result;
    }

    protected function getDefinitionType($height)
    {
        $default = 'sd';

        $vodTemplates = $this->getVodTemplates();

        foreach ($vodTemplates as $key => $template) {
            if ($height >= $template['height']) {
                return $key;
            }
        }

        return $default;
    }

    protected function getVodTemplates()
    {
        return [
            'hd' => ['height' => 1080, 'rate' => 2500],
            'sd' => ['height' => 720, 'rate' => 1800],
            'fd' => ['height' => 540, 'rate' => 1000],
        ];
    }

}
