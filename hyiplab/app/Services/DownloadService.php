<?php

namespace Hyiplab\Services;

class DownloadService
{
    public function downloadFile(string $encryptedFilePath)
    {
        $filePath = hyiplab_decrypt($encryptedFilePath);

        if (!file_exists($filePath) || !is_readable($filePath)) {
            return new \WP_Error('file_not_found', 'File not found or is not readable.');
        }

        $fileName = hyiplab_title_to_key(get_bloginfo('name')) . '_attachments.' . pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeType = mime_content_type($filePath);

        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));

        ob_clean();
        flush();
        readfile($filePath);
        exit;
    }
} 