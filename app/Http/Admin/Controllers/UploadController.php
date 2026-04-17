<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Upload as UploadService;
use App\Services\MyStorage as StorageService;
use App\Services\Vod as VodService;
use App\Validators\Validator as AppValidator;

/**
 * @RoutePrefix("/admin/upload")
 */
class UploadController extends Controller
{

    public function initialize()
    {
        $authUser = $this->getAuthUser();

        $validator = new AppValidator();

        $validator->checkAuthUser($authUser->id);
    }

    /**
     * @Post("/icon/img", name="admin.upload.icon_img")
     */
    public function uploadIconImageAction()
    {
        $service = new StorageService();

        $file = $service->uploadIconImage();

        if (!$file) {
            return $this->jsonError(['msg' => '上传文件失败']);
        }

        $data = [
            'id' => $file->id,
            'name' => $file->name,
            'url' => $service->getImageUrl($file->path),
        ];

        return $this->jsonSuccess(['data' => $data]);
    }

    /**
     * @Post("/cover/img", name="admin.upload.cover_img")
     */
    public function uploadCoverImageAction()
    {
        $service = new StorageService();

        $file = $service->uploadCoverImage();

        if (!$file) {
            return $this->jsonError(['msg' => '上传文件失败']);
        }

        $data = [
            'id' => $file->id,
            'name' => $file->name,
            'url' => $service->getImageUrl($file->path),
        ];

        return $this->jsonSuccess(['data' => $data]);
    }

    /**
     * @Post("/avatar/img", name="admin.upload.avatar_img")
     */
    public function uploadAvatarImageAction()
    {
        $service = new StorageService();

        $file = $service->uploadAvatarImage();

        if (!$file) {
            return $this->jsonError(['msg' => '上传文件失败']);
        }

        $data = [
            'id' => $file->id,
            'name' => $file->name,
            'url' => $service->getImageUrl($file->path),
        ];

        return $this->jsonSuccess(['data' => $data]);
    }

    /**
     * @Post("/content/img", name="admin.upload.content_img")
     */
    public function uploadContentImageAction()
    {
        $service = new StorageService();

        $file = $service->uploadContentImage();

        if (!$file) {
            return $this->jsonError([
                'message' => '上传文件失败',
                'error' => 1,
            ]);
        }

        return $this->jsonSuccess([
            'url' => $service->getImageUrl($file->path),
            'error' => 0,
        ]);
    }

    /**
     * @Post("/vditor/img", name="admin.upload.vditor_img")
     */
    public function uploadVditorImageAction()
    {
        $service = new StorageService();

        $file = $service->uploadContentImage();

        if (!$file) {
            return $this->jsonError(['msg' => '上传文件失败']);
        }

        $data = [
            'id' => $file->id,
            'name' => $file->name,
            'url' => $service->getImageUrl($file->path),
        ];

        return $this->jsonSuccess(['data' => $data]);
    }

    /**
     * @Post("/vditor/img/remote", name="admin.upload.remote_vditor_img")
     */
    public function uploadRemoteVditorImageAction()
    {
        $originalUrl = $this->request->getPost('url', ['trim', 'string']);

        $service = new StorageService();

        $file = $service->uploadRemoteContentImage($originalUrl);

        $newUrl = $originalUrl;

        if ($file) {
            $newUrl = $service->getImageUrl($file->path);
        }

        /**
         * 编辑器要求返回的数据结构
         */
        $data = [
            'url' => $newUrl,
            'originalURL' => $originalUrl,
        ];

        return $this->jsonSuccess(['data' => $data]);
    }

    /**
     * @Post("/exam/question/img", name="admin.upload.exam_question_img")
     */
    public function uploadExamQuestionImageAction()
    {
        $service = new StorageService();

        $file = $service->uploadExamQuestionImage();

        if (!$file) {
            return $this->jsonError([
                'message' => '上传图片失败',
                'error' => 1,
            ]);
        }

        return $this->jsonSuccess([
            'url' => $service->getImageUrl($file->path),
            'error' => 0,
        ]);
    }

    /**
     * @Post("/default/img", name="admin.upload.default_img")
     */
    public function uploadDefaultImageAction()
    {
        $service = new UploadService();

        $items = [];

        $items['category_icon'] = $service->uploadDefaultCategoryIcon();
        $items['user_avatar'] = $service->uploadDefaultUserAvatar();
        $items['article_cover'] = $service->uploadDefaultArticleCover();
        $items['course_cover'] = $service->uploadDefaultCourseCover();
        $items['package_cover'] = $service->uploadDefaultPackageCover();
        $items['topic_cover'] = $service->uploadDefaultTopicCover();
        $items['paper_cover'] = $service->uploadDefaultPaperCover();
        $items['slide_cover'] = $service->uploadDefaultSlideCover();
        $items['gift_cover'] = $service->uploadDefaultGiftCover();
        $items['vip_cover'] = $service->uploadDefaultVipCover();

        foreach ($items as $key => $item) {
            $msg = sprintf('上传文件失败: %s', $key);
            if (!$item) {
                return $this->jsonError(['msg' => $msg]);
            }
        }

        return $this->jsonSuccess(['msg' => '上传文件成功']);
    }

    /**
     * @Post("/doc/file", name="admin.upload.doc_file")
     */
    public function uploadDocFileAction()
    {
        $service = new StorageService();

        $file = $service->uploadDocFile();

        if (!$file) {
            return $this->jsonError(['msg' => '上传文件失败']);
        }

        $data = [
            'id' => $file->id,
            'name' => $file->name,
            'path' => $file->path,
        ];

        return $this->jsonSuccess(['data' => $data]);
    }

    /**
     * @Post("/invoice/file", name="admin.upload.invoice_file")
     */
    public function uploadInvoiceFileAction()
    {
        $service = new StorageService();

        $file = $service->uploadInvoiceFile();

        if (!$file) {
            return $this->jsonError(['msg' => '上传文件失败']);
        }

        $data = [
            'id' => $file->id,
            'name' => $file->name,
            'path' => $file->path,
        ];

        return $this->jsonSuccess(['data' => $data]);
    }

    /**
     * @Post("/tmp/file", name="admin.upload.tmp_file")
     */
    public function uploadTmpFileAction()
    {
        $service = new StorageService();

        $file = $service->uploadTempFile();

        if (!$file) {
            return $this->jsonError(['msg' => '上传文件失败']);
        }

        return $this->jsonSuccess(['file' => $file]);
    }

    /**
     * @Post("/credentials", name="admin.upload.credentials")
     */
    public function credentialsAction()
    {
        $service = new StorageService();

        $token = $service->getFederationToken();

        $data = [
            'credentials' => $token->getCredentials(),
            'expiredTime' => $token->getExpiredTime(),
            'startTime' => time(),
        ];

        return $this->jsonSuccess($data);
    }

    /**
     * @Post("/vod/sign", name="admin.upload.vod_sign")
     */
    public function vodSignatureAction()
    {
        $service = new VodService();

        $sign = $service->getUploadSignature();

        return $this->jsonSuccess(['sign' => $sign]);
    }

}
