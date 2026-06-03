<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Distribution;

use App\Models\KgProduct as KgProductModel;
use App\Services\Logic\Service;
use App\Services\Logic\Url\ShareUrl as ShareUrlService;
use Endroid\QrCode\QrCode;
use Intervention\Image\ImageManager;

class Poster extends Service
{

    public function handle($id)
    {
        $referer = $this->request->getQuery('referer', 'int', 0);

        $service = new DistInfo();

        $dist = $service->handle($id);

        $bgImage = static_path('admin/img/poster/default.png');

        $fontFile = static_path('lib/font/simkai.ttf');

        $fontColor = '#485FD2';

        list($title, $cover) = $this->getTitleAndCover($dist['item']);

        $qrTips = '长按识别二维码';

        $qrUrl = $this->getQrUrl($dist['item'], $referer);

        $manager = new ImageManager();

        $image = $manager->make($bgImage);

        $coverImage = $manager->make($cover)->resize(540, 300);

        $image->insert($coverImage, 'top-left', 31, 31);

        $qrImage = $this->getQrImage($qrUrl, 160);

        $image->insert($qrImage, 'top-left', 210, 500);

        $titleTexts = mb_str_split($title, 15);

        /**
         * 标题折行处理
         */
        for ($i = 0; $i < count($titleTexts); $i++) {
            $image->text($titleTexts[$i], 300, 400 + $i * 50, function ($font) use ($fontFile, $fontColor) {
                $font->file($fontFile);
                $font->color($fontColor);
                $font->align('center');
                $font->size(30);
            });
        }

        $image->text($qrTips, 300, 700, function ($font) use ($fontFile, $fontColor) {
            $font->file($fontFile);
            $font->color($fontColor);
            $font->align('center');
            $font->size(14);
        });

        return $image;
    }

    protected function getTitleAndCover($item)
    {
        $title = $item['title'];

        $cover = kg_cos_img_url($item['cover']);

        switch ($item['type']) {
            case KgProductModel::ITEM_COURSE:
                $title = sprintf('课程：%s', $item['title']);
                break;
            case KgProductModel::ITEM_PACKAGE:
                $title = sprintf('套餐：%s', $item['title']);
                break;
            case KgProductModel::ITEM_VIP:
                $title = sprintf('会员：%s', $item['title']);
                break;
            case KgProductModel::ITEM_EXAM_PAPER:
                $title = sprintf('试卷：%s', $item['title']);
                break;
            case KgProductModel::ITEM_ARTICLE:
                $title = sprintf('专栏：%s', $item['title']);
                break;
        }

        return [$title, $cover];
    }

    protected function getQrUrl($item, $referer = 0)
    {
        $service = new ShareUrlService();

        $service->setTargetType('h5');

        $url = $service->handle('home', 0, $referer);

        switch ($item['type']) {
            case KgProductModel::ITEM_COURSE:
                $url = $service->handle('course', $item['id'], $referer);
                break;
            case KgProductModel::ITEM_PACKAGE:
                $url = $service->handle('package', $item['id'], $referer);
                break;
            case KgProductModel::ITEM_VIP:
                $url = $service->handle('vip', $item['id'], $referer);
                break;
            case KgProductModel::ITEM_EXAM_PAPER:
                $url = $service->handle('exam_paper', $item['id'], $referer);
                break;
            case KgProductModel::ITEM_ARTICLE:
                $url = $service->handle('article', $item['id'], $referer);
                break;
        }

        return $url;
    }

    protected function getQrImage($url, $size = 120, $margin = 10)
    {
        $qrCode = new QrCode($url);

        $qrCode->setSize($size);
        $qrCode->setMargin($margin);

        return $qrCode->writeString();
    }

}
