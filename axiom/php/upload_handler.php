<?php
/**
 * File Upload Handler - Any Size Version
 * Handles profile picture and other file uploads - supports any image size
 */

define('AXIOM_ACCESS', true);
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
$auth = new Auth();

if (!$auth->isLoggedIn()) {
    sendError('Authentication required', null, 401);
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Invalid request method', null, 405);
}

try {
    $userId = $_SESSION['user_id'];
    $uploadType = $_POST['upload_type'] ?? 'profile_picture';
    
    switch ($uploadType) {
        case 'profile_picture':
            uploadProfilePicture($auth, $userId);
            break;
            
        default:
            sendError('Invalid upload type');
    }
    
} catch (Exception $e) {
    error_log("Upload handler error: " . $e->getMessage());
    sendError('File upload failed due to server error', null, 500);
}

/**
 * Upload profile picture - supports any size
 */
function uploadProfilePicture($auth, $userId) {
    // Check if file was uploaded
    if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] === UPLOAD_ERR_NO_FILE) {
        sendError('No file uploaded');
    }
    
    $file = $_FILES['profile_picture'];
    
    // Validate file (modified to allow any size)
    $errors = validateUploadedFileAnySize($file);
    if (!empty($errors)) {
        sendError('File validation failed: ' . implode(', ', $errors));
    }
    
    // Get file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Generate secure filename
    $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
    $uploadPath = PROFILE_UPLOAD_DIR . $filename;
    
    // Get current user to check for existing profile picture
    $user = $auth->getCurrentUser();
    $oldProfilePicture = $user['profile_picture'] ?? null;
    
    try {
        // Create upload directory if it doesn't exist
        if (!file_exists(PROFILE_UPLOAD_DIR)) {
            mkdir(PROFILE_UPLOAD_DIR, 0755, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Optionally resize image for web display (keeping original quality)
            // You can adjust these values or remove resizing entirely
            $resizedPath = resizeImageFlexible($uploadPath, 1200, 1200); // Max 1200px but keeps aspect ratio
            
            // Update user profile with new picture
            $db = Database::getInstance();
            $stmt = $db->getConnection()->prepare("
                UPDATE users SET profile_picture = ?, updated_at = NOW() WHERE id = ?
            ");
            
            $result = $stmt->execute([$filename, $userId]);
            
            if ($result) {
                // Delete old profile picture if exists
                if ($oldProfilePicture && file_exists(PROFILE_UPLOAD_DIR . $oldProfilePicture)) {
                    unlink(PROFILE_UPLOAD_DIR . $oldProfilePicture);
                }
                
                // Log activity
                logActivity($userId, 'profile_picture_update', 'Profile picture updated');
                
                sendSuccess('Profile picture updated successfully', [
                    'filename' => $filename,
                    'url' => str_replace(dirname(__DIR__), '', PROFILE_UPLOAD_DIR) . $filename
                ]);
            } else {
                // Delete uploaded file if database update failed
                unlink($uploadPath);
                sendError('Failed to update profile picture in database');
            }
        } else {
            sendError('Failed to upload file');
        }
        
    } catch (Exception $e) {
        // Clean up uploaded file on error
        if (file_exists($uploadPath)) {
            unlink($uploadPath);
        }
        throw $e;
    }
}

/**
 * Validate uploaded file - allows any size
 */
function validateUploadedFileAnySize($file) {
    $errors = [];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $errors[] = 'File is too large (server limit exceeded)';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = 'File is too large (form limit exceeded)';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errors[] = 'File was only partially uploaded';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $errors[] = 'No temporary directory available';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $errors[] = 'Failed to write file to disk';
                break;
            case UPLOAD_ERR_EXTENSION:
                $errors[] = 'File upload stopped by extension';
                break;
            default:
                $errors[] = 'Unknown upload error';
        }
        return $errors;
    }
    
    // Check if file exists
    if (!file_exists($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        $errors[] = 'File not properly uploaded';
        return $errors;
    }
    
    // Get file info
    $fileInfo = getimagesize($file['tmp_name']);
    $fileSize = $file['size'];
    $fileName = $file['name'];
    
    // Check if it's a valid image
    if (!$fileInfo) {
        $errors[] = 'File is not a valid image';
        return $errors;
    }
    
    // Check file type
    $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
    if (!in_array($fileInfo[2], $allowedTypes)) {
        $errors[] = 'File type not supported. Only JPEG, PNG, GIF, and WebP are allowed';
    }
    
    // Check file extension
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        $errors[] = 'File extension not allowed';
    }
    
    // Optional: Set a reasonable maximum file size (e.g., 50MB)
    // Remove or adjust this limit as needed
    $maxFileSize = 50 * 1024 * 1024; // 50MB
    if ($fileSize > $maxFileSize) {
        $errors[] = 'File size too large. Maximum allowed: ' . formatBytes($maxFileSize);
    }
    
    return $errors;
}

/**
 * Resize image flexibly - maintains aspect ratio, optional maximum dimensions
 */
function resizeImageFlexible($sourcePath, $maxWidth = null, $maxHeight = null) {
    // Get image info
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        return $sourcePath; // Return original if not an image
    }
    
    $sourceWidth = $imageInfo[0];
    $sourceHeight = $imageInfo[1];
    $imageType = $imageInfo[2];
    
    // If no max dimensions specified, return original
    if ($maxWidth === null && $maxHeight === null) {
        return $sourcePath;
    }
    
    // Calculate new dimensions only if image exceeds max dimensions
    $needsResize = false;
    $newWidth = $sourceWidth;
    $newHeight = $sourceHeight;
    
    if ($maxWidth && $sourceWidth > $maxWidth) {
        $needsResize = true;
    }
    if ($maxHeight && $sourceHeight > $maxHeight) {
        $needsResize = true;
    }
    
    if ($needsResize) {
        // Calculate resize ratio
        $ratioW = $maxWidth ? $maxWidth / $sourceWidth : 1;
        $ratioH = $maxHeight ? $maxHeight / $sourceHeight : 1;
        $ratio = min($ratioW, $ratioH);
        
        $newWidth = (int)($sourceWidth * $ratio);
        $newHeight = (int)($sourceHeight * $ratio);
    } else {
        // Image is within limits, no resize needed
        return $sourcePath;
    }
    
    // Create image resource from source
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagecreatefromwebp')) {
                $sourceImage = imagecreatefromwebp($sourcePath);
            } else {
                return $sourcePath;
            }
            break;
        default:
            return $sourcePath; // Unsupported type
    }
    
    if (!$sourceImage) {
        return $sourcePath;
    }
    
    // Create new image
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG and GIF
    if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Resize image
    imagecopyresampled(
        $newImage, $sourceImage,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $sourceWidth, $sourceHeight
    );
    
    // Save resized image (overwrite original)
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            imagejpeg($newImage, $sourcePath, 95); // Higher quality
            break;
        case IMAGETYPE_PNG:
            imagepng($newImage, $sourcePath, 6); // Better compression
            break;
        case IMAGETYPE_GIF:
            imagegif($newImage, $sourcePath);
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagewebp')) {
                imagewebp($newImage, $sourcePath, 95); // Higher quality
            }
            break;
    }
    
    // Clean up memory
    imagedestroy($sourceImage);
    imagedestroy($newImage);
    
    return $sourcePath;
}

/**
 * Generate thumbnail - unchanged
 */
function generateThumbnail($sourcePath, $thumbnailPath, $width = 150, $height = 150) {
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }
    
    $sourceWidth = $imageInfo[0];
    $sourceHeight = $imageInfo[1];
    $imageType = $imageInfo[2];
    
    // Create source image
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagecreatefromwebp')) {
                $sourceImage = imagecreatefromwebp($sourcePath);
            } else {
                return false;
            }
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) {
        return false;
    }
    
    // Calculate crop dimensions (square crop from center)
    $cropSize = min($sourceWidth, $sourceHeight);
    $cropX = ($sourceWidth - $cropSize) / 2;
    $cropY = ($sourceHeight - $cropSize) / 2;
    
    // Create thumbnail
    $thumbnail = imagecreatetruecolor($width, $height);
    
    // Preserve transparency
    if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefilledrectangle($thumbnail, 0, 0, $width, $height, $transparent);
    }
    
    // Resize and crop
    imagecopyresampled(
        $thumbnail, $sourceImage,
        0, 0, $cropX, $cropY,
        $width, $height, $cropSize, $cropSize
    );
    
    // Save thumbnail
    imagejpeg($thumbnail, $thumbnailPath, 90);
    
    // Clean up
    imagedestroy($sourceImage);
    imagedestroy($thumbnail);
    
    return true;
}

/**
 * Format bytes to human readable format
 */
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>