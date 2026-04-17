<?php
/**
 * @copyright Copyright (c) 2025 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Repos\GroupArticle as GroupArticleRepo;

class GroupArticle extends Validator
{

    public function checkById($id)
    {
        $repo = new GroupArticleRepo();

        $groupArticle = $repo->findById($id);

        if (!$groupArticle) {
            throw new BadRequestException('group_article.not_found');
        }

        return $groupArticle;
    }

    public function checkGroup($id)
    {
        $validator = new Group();

        return $validator->checkGroup($id);
    }

    public function checkArticle($id)
    {
        $validator = new Article();

        return $validator->checkArticle($id);
    }

}
