<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Caches\CategoryAllList as CategoryAllListCache;
use App\Models\Category as CategoryModel;

class ExamQuestionList extends Builder
{

    public function handleCategories(array $questions)
    {
        $categories = $this->getCategories();

        foreach ($questions as $key => $question) {
            $questions[$key]['category'] = $categories[$question['category_id']] ?? null;
        }

        return $questions;
    }

    public function getCategories()
    {
        $cache = new CategoryAllListCache();

        $items = $cache->get(CategoryModel::TYPE_EXAM_QUESTION);

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
