<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Library\Validators\Common as CommonValidator;
use App\Repos\ArticleUser as ArticleUserRepo;

class ArticleUser extends Validator
{

    public function checkById($id)
    {
        $repo = new ArticleUserRepo();

        $articleUser = $repo->findById($id);

        if (!$articleUser) {
            throw new BadRequestException('article_user.not_found');
        }

        return $articleUser;
    }

    public function checkArticle($id)
    {
        $validator = new Article();

        return $validator->checkArticle($id);
    }

    public function checkUser($name)
    {
        $validator = new Account();

        $account = $validator->checkAccount($name);

        $validator = new User();

        return $validator->checkUser($account->id);
    }

    public function checkExpiryTime($expiryTime)
    {
        $value = $this->filter->sanitize($expiryTime, ['trim', 'string']);

        if (!CommonValidator::date($value, 'Y-m-d H:i:s')) {
            throw new BadRequestException('article_user.invalid_expiry_time');
        }

        return strtotime($value);
    }

}
