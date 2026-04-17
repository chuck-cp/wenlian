<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Caches\CategoryList as CategoryListCache;
use App\Models\Category as CategoryModel;

class ExamPaperList extends Builder
{

    public function handleCategories(array $papers)
    {
        $categories = $this->getCategories();

        foreach ($papers as $key => $paper) {
            $papers[$key]['category'] = $categories[$paper['category_id']] ?? null;
        }

        return $papers;
    }

    public function getCategories()
    {
        $cache = new CategoryListCache();

        $items = $cache->get(CategoryModel::TYPE_EXAM_PAPER);

        if (empty($items)) return [];

        $result = [];

        foreach ($items as $item) {
            $result[$item['id']] = [
                'id' => $item['id'],
                'name' => $item['name'],
            ];
        }

        return $result;
    }

}
