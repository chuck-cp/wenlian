<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Distribution;

use App\Models\Groupon as GrouponModel;
use App\Models\KgSale as KgSaleModel;

trait DistInfoTrait
{

    /**
     * @var string
     */
    protected $cosUrl;

    protected function getStatusType($startTime, $endTime)
    {
        $result = 0;

        if ($startTime > time()) {
            $result = GrouponModel::STATUS_PENDING;
        } elseif ($endTime > time()) {
            $result = GrouponModel::STATUS_ACTIVE;
        } elseif ($endTime < time()) {
            $result = GrouponModel::STATUS_EXPIRED;
        }

        return $result;
    }

    protected function handleItemInfo($itemType, $itemInfo)
    {
        $result = [
            'id' => 0,
            'type' => 0,
            'price' => 0,
            'title' => '',
            'cover' => '',
        ];

        if ($itemType == KgSaleModel::ITEM_COURSE) {
            $result = [
                'id' => $itemInfo['course']['id'],
                'title' => $itemInfo['course']['title'],
                'price' => $itemInfo['course']['price'],
                'cover' => $this->cosUrl . $itemInfo['course']['cover'],
                'type' => $itemType,
            ];
        } elseif ($itemType == KgSaleModel::ITEM_PACKAGE) {
            $result = [
                'id' => $itemInfo['package']['id'],
                'title' => $itemInfo['package']['title'],
                'price' => $itemInfo['package']['price'],
                'cover' => $this->cosUrl . $itemInfo['package']['cover'],
                'type' => $itemType,
            ];
        } elseif ($itemType == KgSaleModel::ITEM_VIP) {
            $result = [
                'id' => $itemInfo['vip']['id'],
                'title' => $itemInfo['vip']['title'],
                'price' => $itemInfo['vip']['price'],
                'cover' => $this->cosUrl . $itemInfo['vip']['cover'],
                'type' => $itemType,
            ];
        } elseif ($itemType == KgSaleModel::ITEM_EXAM_PAPER) {
            $result = [
                'id' => $itemInfo['exam_paper']['id'],
                'title' => $itemInfo['exam_paper']['title'],
                'price' => $itemInfo['exam_paper']['price'],
                'cover' => $this->cosUrl . $itemInfo['exam_paper']['cover'],
                'type' => $itemType,
            ];
        } elseif ($itemType == KgSaleModel::ITEM_ARTICLE) {
            $result = [
                'id' => $itemInfo['article']['id'],
                'title' => $itemInfo['article']['title'],
                'price' => $itemInfo['article']['price'],
                'cover' => $this->cosUrl . $itemInfo['article']['cover'],
                'type' => $itemType,
            ];
        }

        return $result;
    }

}
