<?php
/**
 * BloodLife — FileUpload Service
 * Secure file upload handler with MIME validation and random filename generation.
 */

require_once __DIR__ . '/../config/config.php';

class FileUploadService {

    private const MAX_FILE_SIZE = 3145728; // 3 MB
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Process avatar upload securely.
     *
     * @param array $file $_FILES['avatar'] item
     * @return string Unique saved filename
     * @throws Exception
     */
    public static function uploadAvatar(array $file): string {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception("Invalid file upload parameters.");
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception("No file was uploaded.");
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception("Exceeded file size limit of 3MB.");
            default:
                throw new Exception("Unknown error occurred during file upload.");
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new Exception("File size exceeds 3MB limit.");
        }

        // Validate MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw new Exception("Invalid image format. Allowed formats: JPG, PNG, WEBP.");
        }

        // Validate Extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new Exception("Invalid file extension.");
        }

        // Generate unique filename
        $newFilename = sprintf('avatar_%s_%s.%s', time(), bin2hex(random_bytes(8)), $extension);
        $targetDir = PUBLIC_PATH . '/uploads/avatars';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $newFilename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Failed to save uploaded file.");
        }

        return $newFilename;
    }
}
