<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services;

use App\Library\Utils\FileInfo;
use App\Models\Upload as UploadModel;
use App\Repos\Upload as UploadRepo;
use InvalidArgumentException;
use RuntimeException;

class MyStorage extends Storage
{

    /**
     * mime类型
     */
    const MIME_IMAGE = 'image';
    const MIME_VIDEO = 'video';
    const MIME_AUDIO = 'audio';
    const MIME_FILE = 'file';

    /**
     * 上传远程内容文件
     *
     * @param string $url
     *
     * @return UploadModel|bool
     */
    public function uploadRemoteContentImage($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $originalName = pathinfo($path, PATHINFO_BASENAME);

        $fileName = $this->generateFileName($extension);

        $filePath = tmp_path($fileName);

        $contents = file_get_contents($url);

        if (file_put_contents($filePath, $contents) === false) {
            return false;
        }

        $keyName = "/img/content/{$fileName}";

        $uploadPath = $this->putFile($keyName, $filePath);

        if (!$uploadPath) {
            throw new RuntimeException('Upload File Failed');
        }

        $md5 = md5_file($filePath);

        $uploadRepo = new UploadRepo();

        $upload = $uploadRepo->findByMd5($md5);

        if (!$upload) {

            $upload = new UploadModel();

            $upload->name = $originalName;
            $upload->mime = mime_content_type($filePath);
            $upload->size = filesize($filePath);
            $upload->type = UploadModel::TYPE_CONTENT_IMG;
            $upload->path = $uploadPath;
            $upload->md5 = $md5;

            $upload->create();
        }

        unlink($filePath);

        return $upload;
    }

    /**
     * 上传临时文件
     *
     * @return bool|string
     */
    public function uploadTempFile()
    {
        if ($this->request->hasFiles(true)) {

            $files = $this->request->getUploadedFiles(true);

            $list = [];

            foreach ($files as $file) {
                $ext = $this->getFileExtension($file->getName());
                $dot = $ext ? '.' : '';
                $name = sprintf('%s%s%s', kg_uniqid(), $dot, $ext);
                $destination = tmp_path($name);
                $file->moveTo($destination);
                $list[] = [
                    'name' => $file->getName(),
                    'type' => $file->getType(),
                    'size' => $file->getSize(),
                    'path' => $destination,
                ];
            }

            return $list[0] ?: false;
        }

        return false;
    }

    /**
     * 上传测试文件
     *
     * @return bool|string
     */
    public function uploadTestFile()
    {
        $key = 'hello_world.txt';
        $value = 'hello world';

        return $this->putString($key, $value);
    }

    /**
     * 上传封面图片
     *
     * @return UploadModel|bool
     */
    public function uploadCoverImage()
    {
        return $this->upload('/img/cover', self::MIME_IMAGE, UploadModel::TYPE_COVER_IMG);
    }

    /**
     * 上传内容图片
     *
     * @return UploadModel|bool
     */
    public function uploadContentImage()
    {
        return $this->upload('/img/content', self::MIME_IMAGE, UploadModel::TYPE_CONTENT_IMG);
    }

    /**
     * 上传头像图片
     *
     * @return UploadModel|bool
     */
    public function uploadAvatarImage()
    {
        return $this->upload('/img/avatar', self::MIME_IMAGE, UploadModel::TYPE_AVATAR_IMG);
    }

    /**
     * 上传图标图片
     *
     * @return UploadModel|bool
     */
    public function uploadIconImage()
    {
        return $this->upload('/img/icon', self::MIME_IMAGE, UploadModel::TYPE_ICON_IMG);
    }

    /**
     * 上传文档文件
     *
     * @return UploadModel|bool
     */
    public function uploadDocFile()
    {
        return $this->upload('/doc', self::MIME_FILE, UploadModel::TYPE_DOC_FILE);
    }

    /**
     * 上传发票文件
     *
     * @return UploadModel|bool
     */
    public function uploadInvoiceFile()
    {
        return $this->upload('/invoice', self::MIME_FILE, UploadModel::TYPE_INVOICE_FILE);
    }

    /**
     * 上传试题图片
     *
     * @return UploadModel|bool
     */
    public function uploadExamQuestionImage()
    {
        return $this->upload('/exam/question', self::MIME_IMAGE, UploadModel::TYPE_EXAM_QUESTION_IMG);
    }

    /**
     * 上传答案文件
     *
     * @return UploadModel|bool
     */
    public function uploadExamAnswerFile()
    {
        return $this->upload('/exam/answer', self::MIME_FILE, UploadModel::TYPE_EXAM_ANSWER_FILE);
    }

    /**
     * 上传文件
     *
     * @param string $prefix
     * @param string $mimeType
     * @param int $uploadType
     * @param string $fileName
     * @return UploadModel|bool
     */
    protected function upload($prefix, $mimeType, $uploadType, $fileName = null)
    {
        $list = [];

        if ($this->request->hasFiles(true)) {

            $files = $this->request->getUploadedFiles(true);

            $uploadRepo = new UploadRepo();

            foreach ($files as $file) {

                if (!$this->checkFile($file->getRealType(), $mimeType)) {
                    $message = sprintf('MimeType: "%s" not in secure whitelist', $file->getRealType());
                    throw new InvalidArgumentException($message);
                }

                $md5 = md5_file($file->getTempName());

                $upload = $uploadRepo->findByMd5($md5);

                if (!$upload) {

                    $name = $this->filter->sanitize($file->getName(), ['trim', 'string']);

                    $extension = $this->getFileExtension($file->getName());

                    if (empty($fileName)) {
                        $keyName = $this->generateFileName($extension, $prefix);
                    } else {
                        $keyName = $prefix . '/' . $fileName;
                    }

                    $path = $this->putFile($keyName, $file->getTempName());

                    if (!$path) {
                        throw new RuntimeException('Upload File Failed');
                    }

                    $upload = new UploadModel();

                    $upload->name = $name;
                    $upload->mime = $file->getRealType();
                    $upload->size = $file->getSize();
                    $upload->type = $uploadType;
                    $upload->path = $path;
                    $upload->md5 = $md5;

                    $upload->create();
                }

                $list[] = $upload;
            }
        }

        return $list[0] ?: false;
    }

    /**
     * 检查上传文件
     *
     * @param string $mime
     * @param string $alias
     * @return bool
     */
    protected function checkFile($mime, $alias)
    {
        switch ($alias) {
            case self::MIME_IMAGE:
                $result = FileInfo::isImage($mime);
                break;
            case self::MIME_VIDEO:
                $result = FileInfo::isVideo($mime);
                break;
            case self::MIME_AUDIO:
                $result = FileInfo::isAudio($mime);
                break;
            default:
                $result = FileInfo::isSecure($mime);
                break;
        }

        return $result;
    }

}
