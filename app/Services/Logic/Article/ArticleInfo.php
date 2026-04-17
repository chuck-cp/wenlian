<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Article;

use App\Library\Utils\Html as HtmlUtil;
use App\Models\Article as ArticleModel;
use App\Models\User as UserModel;
use App\Repos\ArticleFavorite as ArticleFavoriteRepo;
use App\Repos\ArticleLike as ArticleLikeRepo;
use App\Services\Category as CategoryService;
use App\Services\Logic\ArticleTrait;
use App\Services\Logic\ContentTrait;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\User\ShallowUserInfo as ShallowUserInfoService;

class ArticleInfo extends LogicService
{

    use ArticleTrait;
    use ArticleUserTrait;
    use ContentTrait;

    public function handle($id)
    {
        $article = $this->checkArticle($id);

        $user = $this->getCurrentUser();

        $this->setArticleUser($article, $user);

        $result = $this->handleArticle($article, $user);

        $this->incrArticleViewCount($article);

        $this->eventsManager->fire('Article:afterView', $this, $article);

        return $result;
    }

    protected function handleArticle(ArticleModel $article, UserModel $user)
    {
        $categoryPaths = $this->handleCategoryPaths($article->category_id);
        $owner = $this->handleOwnerInfo($article->owner_id);
        $me = $this->handleMeInfo($article, $user);

        $content = $this->handleContent($article->content);

        if ($this->ownedArticle == 0) {
            $length = mb_strlen($content) / 3;
            $limit = min($length, 300);
            $content = HtmlUtil::truncate($content, $limit);
        }

        return [
            'id' => $article->id,
            'title' => $article->title,
            'cover' => $article->cover,
            'summary' => $article->summary,
            'content' => $content,
            'markdown' => $article->markdown,
            'keywords' => $article->keywords,
            'images' => $article->images,
            'tags' => $article->tags,
            'format' => $article->format,
            'featured' => $article->featured,
            'published' => $article->published,
            'closed' => $article->closed,
            'deleted' => $article->deleted,
            'market_price' => (float)$article->market_price,
            'vip_price' => (float)$article->vip_price,
            'study_expiry' => $article->study_expiry,
            'source_type' => $article->source_type,
            'source_url' => $article->source_url,
            'user_count' => $article->getUserCount(),
            'word_count' => $article->word_count,
            'view_count' => $article->view_count,
            'like_count' => $article->like_count,
            'comment_count' => $article->comment_count,
            'favorite_count' => $article->favorite_count,
            'create_time' => $article->create_time,
            'update_time' => $article->update_time,
            'category_paths' => $categoryPaths,
            'owner' => $owner,
            'me' => $me,
        ];
    }

    protected function handleCategoryPaths($categoryId)
    {
        if ($categoryId == 0) return [];

        $service = new CategoryService();

        return $service->getCategoryPaths($categoryId);
    }

    protected function handleOwnerInfo($userId)
    {
        if ($userId == 0) return new \stdClass();

        $service = new ShallowUserInfoService();

        return $service->handle($userId);
    }

    protected function handleMeInfo(ArticleModel $article, UserModel $user)
    {
        $me = [
            'allow_order' => 0,
            'allow_comment' => 0,
            'logged' => 0,
            'liked' => 0,
            'favorited' => 0,
            'owned' => 0,
        ];

        if ($user->id > 0) {

            $me['logged'] = 1;

            if (!$this->ownedArticle && $article->market_price > 0) {
                $me['allow_order'] = 1;
            }

            if ($article->closed == 0) {
                $me['allow_comment'] = 1;
            }

            if ($this->ownedArticle) {
                $me['owned'] = 1;
            }

            $likeRepo = new ArticleLikeRepo();

            $like = $likeRepo->findArticleLike($article->id, $user->id);

            if ($like && $like->deleted == 0) {
                $me['liked'] = 1;
            }

            $favoriteRepo = new ArticleFavoriteRepo();

            $favorite = $favoriteRepo->findArticleFavorite($article->id, $user->id);

            if ($favorite && $favorite->deleted == 0) {
                $me['favorited'] = 1;
            }
        }

        return $me;
    }

    protected function incrArticleViewCount(ArticleModel $article)
    {
        $article->view_count += 1;

        $article->update();
    }

}
