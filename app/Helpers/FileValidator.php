<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileValidator
{
    /**
     * Allowed MIME types by category
     */
    protected static $allowedMimeTypes = [
        'document' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
        ],
        'archive' => [
            'application/zip',
            'application/x-zip-compressed',
        ],
        'image' => [
            'image/jpeg',
            'image/png',
            'image/gif',
        ],
        'text' => [
            'text/plain',
        ],
    ];

    /**
     * File extension to MIME type mapping for extra validation
     */
    protected static $extensionMimeMap = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'txt' => 'text/plain',
        'zip' => ['application/zip', 'application/x-zip-compressed'],
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
    ];

    /**
     * Validate a file based on allowed types
     * 
     * @param UploadedFile $file The uploaded file
     * @param array $allowedTypes Array of allowed file types (document, archive, image, text)
     * @return bool True if valid, throws exception if invalid
     * @throws FileException If validation fails
     */
    public static function validate(UploadedFile $file, array $allowedTypes): bool
    {
        // Validate filename to prevent path traversal and malicious files
        $filename = $file->getClientOriginalName();
        if (!self::isValidFilename($filename)) {
            throw new FileException("Invalid filename detected");
        }

        // First check extension
        $extension = strtolower($file->getClientOriginalExtension());
        $validExtension = false;

        foreach ($allowedTypes as $type) {
            if (!isset(self::$allowedMimeTypes[$type])) {
                continue;
            }

            foreach (self::$allowedMimeTypes[$type] as $mimeType) {
                $expectedExts = array_keys(array_filter(self::$extensionMimeMap, function($mt) use ($mimeType) {
                    return is_array($mt) ? in_array($mimeType, $mt) : $mt === $mimeType;
                }));

                if (in_array($extension, $expectedExts)) {
                    $validExtension = true;
                    break 2;
                }
            }
        }

        if (!$validExtension) {
            throw new FileException("Invalid file extension: $extension");
        }

        // Then check MIME type
        $detectedMimeType = $file->getMimeType();
        $validMimeType = false;

        // Gather all allowed mime types for the specified categories
        $allowedMimes = [];
        foreach ($allowedTypes as $type) {
            if (isset(self::$allowedMimeTypes[$type])) {
                $allowedMimes = array_merge($allowedMimes, self::$allowedMimeTypes[$type]);
            }
        }

        if (!in_array($detectedMimeType, $allowedMimes)) {
            throw new FileException("Invalid file type detected: $detectedMimeType");
        }

        // For extra safety, check if extension matches mime type
        if (isset(self::$extensionMimeMap[$extension])) {
            $expectedMime = self::$extensionMimeMap[$extension];
            if (is_array($expectedMime)) {
                if (!in_array($detectedMimeType, $expectedMime)) {
                    throw new FileException("File extension doesn't match its content type");
                }
            } else if ($detectedMimeType !== $expectedMime) {
                throw new FileException("File extension doesn't match its content type");
            }
        }

        return true;
    }

    /**
     * Sanitize filename to prevent path traversal and XSS
     * 
     * @param string $filename Original filename
     * @return string Sanitized filename
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remove any path components
        $filename = basename($filename);
        
        // Remove special characters that could be used for XSS
        $filename = preg_replace('/[^\w\.-]/i', '_', $filename);
        
        // Limit filename length
        if (strlen($filename) > 255) {
            $filename = substr($filename, 0, 255);
        }
        
        return $filename;
    }
    
    /**
     * Check if filename is valid and secure
     * 
     * @param string $filename Filename to validate
     * @return bool True if filename is valid
     */
    public static function isValidFilename(string $filename): bool
    {
        // Reject empty filenames
        if (empty($filename)) {
            return false;
        }
        
        // Check for path traversal attempts
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            return false;
        }
        
        // Check length
        if (strlen($filename) > 255) {
            return false;
        }
        
        // Block potentially dangerous extensions
        $dangerousExtensions = ['php', 'phar', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps', 'cgi', 'exe', 'pl', 'sh', 'asp', 'aspx', 'bat', 'cmd'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($extension, $dangerousExtensions)) {
            return false;
        }
        
        return true;
    }

    /**
     * Get validation rule for file types
     * 
     * @param array $types Array of allowed file types (document, archive, image, text)
     * @return string Validation rule for mime types
     */
    public static function getValidationRule(array $types): string
    {
        $mimeTypes = [];
        foreach ($types as $type) {
            if (isset(self::$allowedMimeTypes[$type])) {
                $mimeTypes = array_merge($mimeTypes, self::$allowedMimeTypes[$type]);
            }
        }
        
        return 'mimes:' . implode(',', array_keys(array_intersect(self::$extensionMimeMap, $mimeTypes)));
    }
}
