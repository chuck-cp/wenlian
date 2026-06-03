<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Caches;

use App\Models\Tag as TagModel;
use Phalcon\Mvc\Model\Resultset;

class TagList extends Cache
{

    protected $lifetime = 365 * 86400;

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getKey($id = null)
    {
        return "tag_list";
    }

    /**
     * @param null $id
     * @return array
     */
    public function getContent($id = null)
    {
        /**
         * @var Resultset $tags
         */
        $tags = TagModel::query()
            ->columns(['id', 'name', 'alias', 'scopes'])
            ->where('published = 1')
            ->andWhere('deleted = 0')
            ->orderBy('priority ASC')
            ->execute();

        if ($tags->count() == 0) {
            return [];
        }

        return $tags->toArray();
    }

}
