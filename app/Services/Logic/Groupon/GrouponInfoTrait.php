<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Groupon;

use App\Models\Groupon as GrouponModel;
use App\Models\KgProduct as KgProductModel;

trait GrouponInfoTrait
{

    /**
     * @var string cos存储URL
     */
    protected $cosUrl;

    protected function getStatusType($startTime, $endTime)
    {
        $type = 0;

        if ($startTime > time()) {
            $type = GrouponModel::STATUS_PENDING;
        } elseif ($endTime > time()) {
            $type = GrouponModel::STATUS_ACTIVE;
        } elseif ($endTime < time()) {
            $type = GrouponModel::STATUS_EXPIRED;
        }

        return $type;
    }

    protected function handleItemInfo($itemType, $itemInfo)
    {
        $result = [
            'id' => 0,
            'type' => 0,
            'price' => 0.00,
            'title' => '',
            'cover' => '',
        ];

        if ($itemType == KgProductModel::ITEM_COURSE) {
            $result = [
                'id' => $itemInfo['course']['id'],
                'title' => $itemInfo['course']['title'],
                'price' => (float)$itemInfo['course']['price'],
                'cover' => $this->cosUrl . $itemInfo['course']['cover'],
                'type' => $itemType,
            ];
        } elseif ($itemType == KgProductModel::ITEM_PACKAGE) {
            $result = [
                'id' => $itemInfo['package']['id'],
                'title' => $itemInfo['package']['title'],
                'price' => (float)$itemInfo['package']['price'],
                'cover' => $this->cosUrl . $itemInfo['package']['cover'],
                'type' => $itemType,
            ];
        } elseif ($itemType == KgProductModel::ITEM_VIP) {
            $result = [
                'id' => $itemInfo['vip']['id'],
                'title' => $itemInfo['vip']['title'],
                'price' => (float)$itemInfo['vip']['price'],
                'cover' => $this->cosUrl . $itemInfo['vip']['cover'],
                'type' => $itemType,
            ];
        } elseif ($itemType == KgProductModel::ITEM_EXAM_PAPER) {
            $result = [
                'id' => $itemInfo['exam_paper']['id'],
                'title' => $itemInfo['exam_paper']['title'],
                'price' => (float)$itemInfo['exam_paper']['price'],
                'cover' => $this->cosUrl . $itemInfo['exam_paper']['cover'],
                'type' => $itemType,
            ];
        } elseif ($itemType == KgProductModel::ITEM_ARTICLE) {
            $result = [
                'id' => $itemInfo['article']['id'],
                'title' => $itemInfo['article']['title'],
                'price' => (float)$itemInfo['article']['price'],
                'cover' => $this->cosUrl . $itemInfo['article']['cover'],
                'type' => $itemType,
            ];
        }

        return $result;
    }

}
