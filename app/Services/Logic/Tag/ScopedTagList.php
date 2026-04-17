<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Tag;

use App\Caches\TagList as TagListCache;
use App\Services\Logic\Service as LogicService;

class ScopedTagList extends LogicService
{

    public function handle($scope)
    {
        $cache = new TagListCache();

        /**
         * @var $tags array
         */
        $tags = $cache->get();

        if (empty($tags)) return [];

        $result = [];

        foreach ($tags as $tag) {
            $scopeOk = false;
            if ($tag['scopes'] == 'all') {
                $scopeOk = true;
            } else {
                $scopes = json_decode($tag['scopes'], true);
                if (in_array($scope, $scopes)) {
                    $scopeOk = true;
                }
            }
            if ($scopeOk) {
                $result[] = [
                    'id' => $tag['id'],
                    'name' => $tag['name'],
                    'alias' => $tag['alias'],
                ];
            }
        }

        return $result;
    }

}
