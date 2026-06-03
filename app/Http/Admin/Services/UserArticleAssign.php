<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Models\KgOwnership as KgOwnershipModel;
use App\Repos\Article as ArticleRepo;
use App\Services\Logic\Article\ArticleUserTrait;
use App\Validators\ArticleUser as ArticleUserValidator;

class UserArticleAssign extends Service
{

    use ArticleUserTrait;

    public function getXmArticles()
    {
        $articleRepo = new ArticleRepo();

        $where = [
            'published' => 1,
            'deleted' => 0,
            'free' => 0,
        ];

        $items = $articleRepo->findAll($where);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
                'value' => $item->id,
            ];
        }

        return $result;
    }

    public function assignArticle($id)
    {
        $post = $this->request->getPost();

        $validator = new ArticleUserValidator();

        $user = $validator->checkUser($id);

        $expiryTime = $validator->checkExpiryTime($post['expiry_time']);

        $articleIds = $post['xm_article_ids'] ? explode(',', $post['xm_article_ids']) : [];

        if (empty($articleIds)) return;

        $articleRepo = new ArticleRepo();

        $articles = $articleRepo->findByIds($articleIds);

        $sourceType = KgOwnershipModel::SOURCE_MANUAL;

        foreach ($articles as $article) {
            $this->assignUserArticle($article, $user, $expiryTime, $sourceType);
        }
    }

}
