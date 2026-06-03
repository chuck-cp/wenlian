<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Course;

use App\Repos\Course as CourseRepo;
use App\Services\Logic\Service;
use App\Services\Logic\Url\ShareUrl as ShareUrlService;
use Endroid\QrCode\QrCode;
use Intervention\Image\ImageManager;

class Poster extends Service
{

    public function handle($id)
    {
        $referer = $this->request->getQuery('referer', 'int', 0);

        $courseRepo = new CourseRepo();

        $course = $courseRepo->findById($id);

        $bgImage = static_path('admin/img/poster/default.png');

        $fontFile = static_path('lib/font/simkai.ttf');

        $fontColor = '#485FD2';

        $qrTips = '长按识别二维码';

        $qrUrl = $this->getQrUrl($course->id, $referer);

        $manager = new ImageManager();

        $image = $manager->make($bgImage);

        $coverImage = $manager->make($course->cover)->resize(540, 300);

        $image->insert($coverImage, 'top-left', 31, 31);

        $qrImage = $this->getQrImage($qrUrl, 160);

        $image->insert($qrImage, 'top-left', 210, 500);

        $titleTexts = mb_str_split($course->title, 15);

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

    protected function getQrUrl($courseId, $referer = 0)
    {
        $service = new ShareUrlService();

        $service->setTargetType('h5');

        return $service->handle('course', $courseId, $referer);
    }

    protected function getQrImage($url, $size = 120, $margin = 10)
    {
        $qrCode = new QrCode($url);

        $qrCode->setSize($size);
        $qrCode->setMargin($margin);

        return $qrCode->writeString();
    }

}
