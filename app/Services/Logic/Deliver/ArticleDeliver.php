<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Deliver;

use App\Models\Article as ArticleModel;
use App\Models\KgOwnership as KgOwnershipModel;
use App\Models\User as UserModel;
use App\Repos\ArticleUser as ArticleUserRepo;
use App\Services\Logic\Article\ArticleUserTrait;
use App\Services\Logic\Service as LogicService;

class ArticleDeliver extends LogicService
{

    use ArticleUserTrait;

    public function handle(ArticleModel $article, UserModel $user)
    {
        $this->revokeArticleUser($article, $user);
        $this->handleArticleUser($article, $user);
    }

    protected function handleArticleUser(ArticleModel $article, UserModel $user)
    {
        $expiryTime = strtotime("+{$article->study_expiry} months");

        $sourceType = KgOwnershipModel::SOURCE_CHARGE;

        $this->createArticleUser($article, $user, $expiryTime, $sourceType);
        $this->recountArticleUsers($article);
        $this->recountUserStudyArticles($user);
    }

    protected function revokeArticleUser(ArticleModel $article, UserModel $user)
    {
        $articleUserRepo = new ArticleUserRepo();

        $relations = $articleUserRepo->findByArticleAndUserId($article->id, $user->id);

        if ($relations->count() == 0) return;

        foreach ($relations as $relation) {
            $this->deleteArticleUser($relation);
        }
    }

}
