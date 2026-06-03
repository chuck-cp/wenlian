<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services\Traits;

use App\Models\KgSale as KgSaleModel;

trait DigitalCardSearchTrait
{

    protected function handleDigitalCardSearchParams($params)
    {
        $itemId = null;

        if (!empty($params['item_type'])) {
            if ($params['item_type'] == KgSaleModel::ITEM_COURSE) {
                $itemId = $params['xm_course_id'] ?? null;
            } elseif ($params['item_type'] == KgSaleModel::ITEM_PACKAGE) {
                $itemId = $params['xm_package_id'] ?? null;
            } elseif ($params['item_type'] == KgSaleModel::ITEM_VIP) {
                $itemId = $params['xm_vip_id'] ?? null;
            } elseif ($params['item_type'] == KgSaleModel::ITEM_EXAM_PAPER) {
                $itemId = $params['xm_paper_id'] ?? null;
            } elseif ($params['item_type'] == KgSaleModel::ITEM_ARTICLE) {
                $itemId = $params['xm_article_id'] ?? null;
            }
        }

        if (!empty($itemId)) {
            $params['item_id'] = $itemId;
        }

        return $params;
    }

}
