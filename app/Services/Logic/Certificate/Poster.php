<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Certificate;

use App\Models\CertificateUser as CertUserModel;
use App\Models\KgProduct as KgProductModel;
use App\Repos\Certificate as CertRepo;
use App\Repos\CertificateUser as CertUserRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Service;
use App\Services\MyStorage;
use Endroid\QrCode\QrCode;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class Poster extends Service
{

    public function handle($sn)
    {
        $certUserRepo = new CertUserRepo();

        $certUser = $certUserRepo->findBySn($sn);

        if (!empty($certUser->cert_path)) {
            return $certUser;
        }

        $userRepo = new UserRepo();

        $user = $userRepo->findById($certUser->user_id);

        $certRepo = new CertRepo();

        $cert = $certRepo->findById($certUser->cert_id);

        $bgImage = static_path('admin/img/cert/default.png');

        $fontFile = static_path('lib/font/simkai.ttf');

        $fontColor = '#758298';

        $username = sprintf('%s(%s)', $user->name, $user->id);

        $content = '酷瓜云课堂';

        $qrUrl = kg_full_url(['for' => 'home.index']);

        if ($cert->item_type == KgProductModel::ITEM_COURSE) {
            $content = sprintf('完成了课程《%s》的线上学习。', $cert->item_info['course']['title']);
            $qrUrl = kg_full_url(['for' => 'home.course.show', 'id' => $cert->item_info['course']['id']]);
        } elseif ($cert->item_type == KgProductModel::ITEM_EXAM_PAPER) {
            $content = sprintf('通过了试卷《%s》的线上测评。', $cert->item_info['exam_paper']['title']);
            $qrUrl = kg_full_url(['for' => 'home.exam_paper.show', 'id' => $cert->item_info['exam_paper']['id']]);
        } elseif ($cert->item_type == KgSaleModel::ITEM_TOPIC && !empty($cert->item_info['topic']['id'])) {
            $content = sprintf('完成了专题《%s》的关联课程学习。', $cert->item_info['topic']['title']);
            $qrUrl = kg_full_url(['for' => 'home.topic.show', 'id' => $cert->item_info['topic']['id']]);
        }

        $endTips = '特发此证，以资鼓励！';

        $siteTitle = kg_setting('site', 'title');

        $grantDate = date('Y年m月d日', $certUser->create_time);

        $manager = new ImageManager();

        $image = $manager->make($bgImage);

        $qrImage = $this->getQrImage($qrUrl, 110, 0, ['r' => 236, 'g' => 236, 'b' => 235, 'a' => 0]);

        $image->insert($qrImage, 'bottom', 500, 60);

        $image->text($username, 520, 380, function ($font) use ($fontFile, $fontColor) {
            $font->file($fontFile);
            $font->color($fontColor);
            $font->align('center');
            $font->size(30);
        });

        $contentTexts = mb_str_split($content, 30);

        /**
         * 正文折行处理
         */
        for ($i = 0; $i < count($contentTexts); $i++) {
            $image->text($contentTexts[$i], 520, 450 + $i * 40, function ($font) use ($fontFile, $fontColor) {
                $font->file($fontFile);
                $font->color($fontColor);
                $font->align('center');
                $font->size(28);
            });
        }

        $image->text($endTips, 680, 560, function ($font) use ($fontFile, $fontColor) {
            $font->file($fontFile);
            $font->color($fontColor);
            $font->align('left');
            $font->size(24);
        });

        $image->text($siteTitle, 150, 660, function ($font) use ($fontFile, $fontColor) {
            $font->file($fontFile);
            $font->color($fontColor);
            $font->size(20);
        });

        $image->text($grantDate, 750, 660, function ($font) use ($fontFile, $fontColor) {
            $font->file($fontFile);
            $font->color($fontColor);
            $font->size(20);
        });

        $certPath = $this->uploadCertImage($certUser, $image);

        $certUser->cert_path = $certPath;

        $certUser->update();

        $certUser->afterFetch();

        return $certUser;
    }

    protected function getQrImage($url, $size = 120, $margin = 10, $bgColor = null)
    {
        $qrCode = new QrCode($url);

        $qrCode->setSize($size);

        $qrCode->setMargin($margin);

        if ($bgColor) {
            $qrCode->setBackgroundColor($bgColor);
        }

        return $qrCode->writeString();
    }

    protected function uploadCertImage(CertUserModel $certUser, Image $image)
    {
        $storage = new MyStorage();

        $key = sprintf('/img/cert/%s.png', $certUser->sn);

        $content = $image->encode('png');

        $storage->putString($key, $content);

        return $key;
    }

}
