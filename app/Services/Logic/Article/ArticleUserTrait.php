<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Article;

use App\Models\Article as ArticleModel;
use App\Models\ArticleUser as ArticleUserModel;
use App\Models\KgOwnership as KgOwnershipModel;
use App\Models\User as UserModel;
use App\Repos\Article as ArticleRepo;
use App\Repos\ArticleUser as ArticleUserRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Group\GroupPermissionTrait;

trait ArticleUserTrait
{

    use GroupPermissionTrait;

    /**
     * @var bool
     */
    protected $ownedArticle = false;

    /**
     * @var bool
     */
    protected $joinedArticle = false;

    /**
     * @var ArticleUserModel|null
     */
    protected $articleUser;

    protected function setArticleUser(ArticleModel $article, UserModel $user)
    {
        if ($user->id == 0) {
            if ($article->market_price == 0) {
                $this->ownedArticle = true;
            }
            return;
        }

        $articleUserRepo = new ArticleUserRepo();

        $articleUser = $articleUserRepo->findArticleUser($article->id, $user->id);

        if (!$articleUser && $this->allowFreeAccess($article, $user)) {

            $sourceType = $this->getFreeSourceType($article, $user);

            $this->createArticleUser($article, $user, 0, $sourceType);
            $this->recountArticleUsers($article);
            $this->recountUserStudyArticles($user);
        }

        $this->articleUser = $articleUser;

        if ($articleUser) {
            $this->joinedArticle = true;
        }

        if ($article->owner_id == $user->id) {

            $this->ownedArticle = true;

        } elseif ($article->market_price == 0) {

            $this->ownedArticle = true;

        } elseif ($article->vip_price == 0 && $user->vip == 1) {

            $this->ownedArticle = true;

        } elseif ($this->groupedArticle($article, $user)) {

            $this->ownedArticle = true;

        } elseif ($articleUser) {

            $sourceTypes = [
                KgOwnershipModel::SOURCE_CHARGE,
                KgOwnershipModel::SOURCE_MANUAL,
                KgOwnershipModel::SOURCE_POINT_REDEEM,
                KgOwnershipModel::SOURCE_LUCKY_REDEEM,
            ];

            $case1 = $articleUser->deleted == 0;
            $case2 = $articleUser->expiry_time > time();
            $case3 = in_array($articleUser->source_type, $sourceTypes);

            /**
             * 之前参与过专栏，但不再满足条件，视为未参与
             */
            if ($case1 && $case2 && $case3) {
                $this->ownedArticle = true;
            } else {
                $this->joinedArticle = false;
            }
        }
    }

    protected function assignUserArticle(ArticleModel $article, UserModel $user, int $expiryTime, int $sourceType)
    {
        if ($this->allowFreeAccess($article, $user)) return null;

        $articleUserRepo = new ArticleUserRepo();

        $relation = $articleUserRepo->findArticleUser($article->id, $user->id);

        $newRelation = null;

        if (!$relation) {

            $newRelation = $this->createArticleUser($article, $user, $expiryTime, $sourceType);

        } else {

            switch ($relation->source_type) {
                case KgOwnershipModel::SOURCE_FREE:
                case KgOwnershipModel::SOURCE_VIP:
                case KgOwnershipModel::SOURCE_TEACHER:
                case KgOwnershipModel::SOURCE_GROUP:
                    $newRelation = $this->createArticleUser($article, $user, $expiryTime, $sourceType);
                    $this->deleteArticleUser($relation);
                    break;
                case KgOwnershipModel::SOURCE_MANUAL:
                    $relation->expiry_time = $expiryTime;
                    $relation->update();
                    break;
                case KgOwnershipModel::SOURCE_CHARGE:
                case KgOwnershipModel::SOURCE_POINT_REDEEM:
                case KgOwnershipModel::SOURCE_LUCKY_REDEEM:
                    if ($relation->expiry_time < time()) {
                        $newRelation = $this->createArticleUser($article, $user, $expiryTime, $sourceType);
                        $this->deleteArticleUser($relation);
                    }
                    break;
            }
        }

        $this->recountArticleUsers($article);
        $this->recountUserStudyArticles($user);

        return $newRelation ?: $relation;
    }

    protected function createArticleUser(ArticleModel $article, UserModel $user, int $expiryTime, int $sourceType)
    {
        $articleUser = new ArticleUserModel();

        $articleUser->article_id = $article->id;
        $articleUser->user_id = $user->id;
        $articleUser->expiry_time = $expiryTime;
        $articleUser->source_type = $sourceType;

        $articleUser->create();

        return $articleUser;
    }

    protected function deleteArticleUser(ArticleUserModel $relation)
    {
        $relation->deleted = 1;

        $relation->update();
    }

    protected function recountArticleUsers(ArticleModel $article)
    {
        $articleRepo = new ArticleRepo();

        $userCount = $articleRepo->countUsers($article->id);

        $article->user_count = $userCount;

        $article->update();
    }

    protected function recountUserStudyArticles(UserModel $user)
    {
        $userRepo = new UserRepo();

        $articleCount = $userRepo->countStudyArticles($user->id);

        $user->study_article_count = $articleCount;

        $user->update();
    }

    protected function allowFreeAccess(ArticleModel $article, UserModel $user)
    {
        $result = false;

        if ($article->market_price == 0) {
            $result = true;
        } elseif ($article->vip_price == 0 && $user->vip == 1) {
            $result = true;
        } elseif ($article->owner_id == $user->id) {
            $result = true;
        } elseif ($this->groupedArticle($article, $user)) {
            $result = true;
        }

        return $result;
    }

    protected function getFreeSourceType(ArticleModel $article, UserModel $user)
    {
        if ($article->owner_id == $user->id) {
            return KgOwnershipModel::SOURCE_TEACHER;
        }

        $sourceType = KgOwnershipModel::SOURCE_FREE;

        if ($article->market_price > 0) {
            if ($article->vip_price == 0 && $user->vip == 1) {
                $sourceType = KgOwnershipModel::SOURCE_VIP;
            } elseif ($this->groupedArticle($article, $user)) {
                $sourceType = KgOwnershipModel::SOURCE_GROUP;
            }
        }

        return $sourceType;
    }

}
