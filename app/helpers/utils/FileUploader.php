<?php

namespace App\helpers\utils;

class FileUploader
{
    private $allowedExtensions;
    private $maxFileSize;
    private $uploadPath;
    private $logPath = '/logs/';
    public $errors = [];
    private $uploadedFiles = [];
    private static $instance = null;

    private function __construct()
    {
        $this->maxFileSize = FILE_UPLOAD_MAX_SIZE_MB * 1024 * 1024; // MB to bytes
        $this->uploadPath = dirname(__DIR__, 3) . '/public';
        $this->allowedExtensions = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
            'video' => ['mp4', 'avi', 'mov', 'wmv'],
            'audio' => ['mp3', 'wav', 'ogg']
        ];
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setUploadPath(string $path): self
    {
        $this->uploadPath = rtrim($path, '/') . '/';
        return $this;
    }

    public function setAllowedExtensions(array $extensions, string $type = 'all'): self
    {
        if ($type === 'all') {
            $this->allowedExtensions = $extensions;
        } else {
            $this->allowedExtensions[$type] = $extensions;
        }
        return $this;
    }

    public function setMaxFileSize(int $sizeMB): self
    {
        $this->maxFileSize = $sizeMB * 1024 * 1024;
        return $this;
    }

    public function upload($file, string $customName = '', string $type = 'all'): ?array
    {
        $this->errors = [];

        if (!$this->validateFile($file, $type)) {
            return null;
        }

        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);

        $fileName = $customName ?
            $this->sanitizeFileName($customName) . '.' . $extension :
            $this->generateSafeFileName($fileInfo['filename'], $extension);

        $yearMonth = date('Y/m');
        $uploadDir = $this->uploadPath . $yearMonth;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filePath = $uploadDir . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $fileData = [
                'original_name' => $file['name'],
                'saved_name' => $fileName,
                'path' => $yearMonth . '/' . $fileName,
                'full_path' => $filePath,
                'extension' => $extension,
                'size' => $file['size'],
                'mime_type' => $file['type']
            ];

            $this->uploadedFiles[] = $fileData;
            return $fileData;
        }

        $this->errors[] = 'Dosya yüklenirken bir hata oluştu.';
        return null;
    }

    public function uploadMultiple(array $files, string $type = 'all'): array
    {
        $uploadedFiles = [];

        foreach ($files['name'] as $key => $value) {
            $file = [
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'error' => $files['error'][$key],
                'size' => $files['size'][$key]
            ];

            $result = $this->upload($file, '', $type);
            if ($result) {
                $uploadedFiles[] = $result;
            }
        }

        return $uploadedFiles;
    }

    private function validateFile(array $file, string $type): bool
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage($file['error']);
            return false;
        }

        if ($file['size'] > $this->maxFileSize) {
            $this->errors[] = 'Dosya boyutu çok büyük. Maksimum: ' . (FILE_UPLOAD_MAX_SIZE_MB) . 'MB';
            return false;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExt = $type === 'all' ? array_merge(...array_values($this->allowedExtensions)) : $this->allowedExtensions[$type];

        if (!in_array($extension, $allowedExt)) {
            $this->errors[] = 'Geçersiz dosya uzantısı. İzin verilenler: ' . implode(', ', $allowedExt);
            return false;
        }

        if (!$this->validateMimeType($file)) {
            return false;
        }

        return true;
    }

    private function validateMimeType(array $file): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimeTypes = [
            'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'document' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'video' => ['video/mp4', 'video/avi', 'video/quicktime'],
            'audio' => ['audio/mpeg', 'audio/wav', 'audio/ogg']
        ];

        $allMimeTypes = array_merge(...array_values($allowedMimeTypes));

        if (!in_array($mimeType, $allMimeTypes)) {
            $this->errors[] = 'Geçersiz dosya türü.';
            return false;
        }

        return true;
    }

    private function sanitizeFileName(string $fileName): string
    {
        $tr = ['ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ş', 'Ş', 'ö', 'Ö', 'ç', 'Ç'];
        $eng = ['i', 'I', 'g', 'G', 'u', 'U', 's', 'S', 'o', 'O', 'c', 'C'];
        $fileName = str_replace($tr, $eng, $fileName);

        $fileName = preg_replace('/[^A-Za-z0-9-_]/', '-', $fileName);

        $fileName = preg_replace('/-+/', '-', $fileName);

        return trim($fileName, '-');
    }

    private function generateSafeFileName(string $baseName, string $extension): string
    {
        $baseName = $this->sanitizeFileName($baseName);
        return $baseName . '_' . time() . '_' . uniqid() . '.' . $extension;
    }

    private function getUploadErrorMessage(int $error): string
    {
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
                return 'Dosya boyutu PHP yapılandırmasındaki maksimum boyutu aşıyor.';
            case UPLOAD_ERR_FORM_SIZE:
                return 'Dosya boyutu form yapılandırmasındaki maksimum boyutu aşıyor.';
            case UPLOAD_ERR_PARTIAL:
                return 'Dosya kısmen yüklendi.';
            case UPLOAD_ERR_NO_FILE:
                return 'Dosya yüklenmedi.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Geçici klasör bulunamadı.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Dosya diske yazılamadı.';
            case UPLOAD_ERR_EXTENSION:
                return 'Bir PHP uzantısı dosya yüklemesini durdurdu.';
            default:
                return 'Bilinmeyen bir hata oluştu.';
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    public function deleteFile(string $filePath): bool
    {
        $fullPath = $this->uploadPath . $filePath;

        if (file_exists($fullPath) && unlink($fullPath)) {
            return true;
        }

        $this->errors[] = 'Dosya silinirken bir hata oluştu.';
        return false;
    }

    public function createThumbnail(string $imagePath, int $width, int $height): ?string
    {
        $fullPath = $this->uploadPath . $imagePath;
        if (!file_exists($fullPath)) {
            $this->errors[] = 'Kaynak resim bulunamadı.';
            return null;
        }

        $imageInfo = getimagesize($fullPath);
        if (!$imageInfo) {
            $this->errors[] = 'Geçersiz resim dosyası.';
            return null;
        }

        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($fullPath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($fullPath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($fullPath);
                break;
            default:
                $this->errors[] = 'Desteklenmeyen resim formatı.';
                return null;
        }

        $thumb = imagecreatetruecolor($width, $height);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $imageInfo[0], $imageInfo[1]);

        $pathInfo = pathinfo($fullPath);
        $thumbPath = $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];

        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumb, $thumbPath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumb, $thumbPath, 9);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumb, $thumbPath);
                break;
        }

        imagedestroy($source);
        imagedestroy($thumb);

        return str_replace($this->uploadPath, '', $thumbPath);
    }

    public function moveFile(string $currentPath, string $newPath): bool
    {
        $fullCurrentPath = $this->uploadPath . $currentPath;
        $fullNewPath = $this->uploadPath . $newPath;

        $targetDir = dirname($fullNewPath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (rename($fullCurrentPath, $fullNewPath)) {
            return true;
        }

        $this->errors[] = 'Dosya taşınırken bir hata oluştu.';
        return false;
    }

    public function getFileInfo(string $filePath): ?array
    {
        $fullPath = $this->uploadPath . $filePath;

        if (!file_exists($fullPath)) {
            $this->errors[] = 'Dosya bulunamadı.';
            return null;
        }

        return [
            'name' => basename($fullPath),
            'path' => $filePath,
            'full_path' => $fullPath,
            'size' => filesize($fullPath),
            'extension' => pathinfo($fullPath, PATHINFO_EXTENSION),
            'mime_type' => mime_content_type($fullPath),
            'created_at' => date('Y-m-d H:i:s', filectime($fullPath)),
            'modified_at' => date('Y-m-d H:i:s', filemtime($fullPath)),
            'is_image' => getimagesize($fullPath) !== false
        ];
    }

    public function setLogFileData($message)
    {
        $fileName = date('Y-m-d') . '.log';
        $logDir = dirname(__DIR__, 3) . $this->logPath;
        $file = $logDir . $fileName;
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        if (file_exists($file)) {
            $fileContent = file_get_contents($file);
            $fileContent .= $message . "\n";
            file_put_contents($file, $fileContent);
        } else {
            $fileContent = $message . "\n";
            file_put_contents($file, $fileContent);
        }
    }
}
