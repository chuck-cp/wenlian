<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Library\Validators\Common as CommonValidator;
use App\Models\Article as ArticleModel;
use App\Repos\Article as ArticleRepo;
use App\Services\EditorStorage as EditorStorageService;

class Article extends Validator
{

    public function checkArticle($id)
    {
        $articleRepo = new ArticleRepo();

        $article = $articleRepo->findById($id);

        if (!$article) {
            throw new BadRequestException('article.not_found');
        }

        return $article;
    }

    public function checkOwnerId($id)
    {
        $result = 0;

        if ($id > 0) {
            $validator = new User();
            $user = $validator->checkUser($id);
            $result = $user->id;
        }

        return $result;
    }

    public function checkCategoryId($id)
    {
        $result = 0;

        if ($id > 0) {
            $validator = new Category();
            $category = $validator->checkCategory($id);
            $result = $category->id;
        }

        return $result;
    }

    public function checkFormat($format)
    {
        $value = $this->filter->sanitize($format, ['trim', 'string']);

        if (!in_array($value, ['html', 'markdown'])) {
            throw new BadRequestException('article.invalid_format');
        }

        return $value;
    }

    public function checkTitle($title)
    {
        $value = $this->filter->sanitize($title, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2) {
            throw new BadRequestException('article.title_too_short');
        }

        if ($length > 50) {
            throw new BadRequestException('article.title_too_long');
        }

        return $value;
    }

    public function checkCover($cover)
    {
        $value = $this->filter->sanitize($cover, ['trim', 'string']);

        if (!CommonValidator::image($value)) {
            throw new BadRequestException('article.invalid_cover');
        }

        return kg_cos_img_style_trim($value);
    }

    public function checkSummary($summary)
    {
        $value = $this->filter->sanitize($summary, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length > 255) {
            throw new BadRequestException('article.summary_too_long');
        }

        return $value;
    }

    public function checkHtmlContent($content)
    {
        $value = $this->filter->sanitize($content, ['trim']);

        $storage = new EditorStorageService();

        $value = $storage->handle($value);

        $value = $this->checkContent($value);

        return kg_clean_html($value);
    }

    public function checkMarkdownContent($content)
    {
        $value = $this->filter->sanitize($content, ['trim']);

        return $this->checkContent($value);
    }

    protected function checkContent($content)
    {
        $length = kg_strlen($content);

        if ($length < 10) {
            throw new BadRequestException('article.content_too_short');
        }

        if ($length > 60000) {
            throw new BadRequestException('article.content_too_long');
        }

        return $content;
    }

    public function checkKeywords($keywords)
    {
        $keywords = $this->filter->sanitize($keywords, ['trim', 'string']);

        $length = kg_strlen($keywords);

        if ($length > 100) {
            throw new BadRequestException('article.keyword_too_long');
        }

        return kg_parse_keywords($keywords);
    }

    public function checkSourceType($type)
    {
        if (!array_key_exists($type, ArticleModel::sourceTypes())) {
            throw new BadRequestException('article.invalid_source_type');
        }

        return $type;
    }

    public function checkSourceUrl($url)
    {
        $url = $this->filter->sanitize($url, ['trim', 'string']);

        if (!CommonValidator::url($url)) {
            throw new BadRequestException('article.invalid_source_url');
        }

        return $url;
    }

    public function checkUserCount($userCount)
    {
        $value = $this->filter->sanitize($userCount, ['trim', 'int']);

        if ($value < 0 || $value > 999999) {
            throw new BadRequestException('article.invalid_user_count');
        }

        return $value;
    }

    public function checkMarketPrice($price)
    {
        $value = $this->filter->sanitize($price, ['trim', 'float']);

        if ($value < 0 || $value > 999999) {
            throw new BadRequestException('article.invalid_market_price');
        }

        return $value;
    }

    public function checkVipPrice($price)
    {
        $value = $this->filter->sanitize($price, ['trim', 'float']);

        if ($value < 0 || $value > 999999) {
            throw new BadRequestException('article.invalid_vip_price');
        }

        return $value;
    }

    public function checkStudyExpiry($expiry)
    {
        $options = ArticleModel::studyExpiryOptions();

        if (!isset($options[$expiry])) {
            throw new BadRequestException('article.invalid_study_expiry');
        }

        return $expiry;
    }

    public function checkFeatureStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('article.invalid_feature_status');
        }

        return $status;
    }

    public function checkPublishStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('article.invalid_publish_status');
        }

        return $status;
    }

    public function checkCloseStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('article.invalid_close_status');
        }

        return $status;
    }

}
