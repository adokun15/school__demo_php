<?php
require_once __DIR__ . './db.php';
//session_start();

define('MAX_IMAGE_SIZE', 5 * 1024 * 1024);

if (!isset($_SESSION["student_id"])) {
    header("Location: /kwasu_demo/login");
    exit;
}



function getUploadErrorMessage($error)
{
    switch ($error) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file is too large.';
            
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file is too large.';
                
                case UPLOAD_ERR_PARTIAL:
                    return 'The file was only partially uploaded.';
                    
        case UPLOAD_ERR_NO_FILE:
            return 'Please choose an image file.';

        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing temporary upload folder.';

            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write the uploaded file.';

        case UPLOAD_ERR_EXTENSION:
            return 'A PHP extension stopped the file upload.';

        case UPLOAD_ERR_OK:
            return null;

        default:
        return 'An unknown upload error occurred.';
    }
    }

    


    
function fetchSingleImage($conn)
{
    $images = array();
    $statement = $conn->prepare(
        'SELECT id, student_id, image_name, image_type, image_data, uploaded_at
         FROM students_image
         WHERE student_id = ?
         ORDER BY uploaded_at DESC
         LIMIT 1
         '
    );

    if (!$statement) {
        return $images;
    }
//New code
   $student_id = $_SESSION["student_id"];
   $statement->bind_param('i', $student_id);
   $statement->execute();
   $result = $statement->get_result();
   $image = $result->fetch_assoc();
   $statement->close();

   return $image;
}


// Get one image by ID
function fetchImageById($conn, $imageId)
{
    $statement = $conn->prepare(
        'SELECT id, student_id, image_name, image_type, image_data, uploaded_at
         FROM students_image
         WHERE id = ?
         LIMIT 1'
    );

    if (!$statement) {
        return null;
    }

    $statement->bind_param('i', $imageId);
    $statement->execute();

    $result = $statement->get_result();
    $image = $result->fetch_assoc();

    $statement->close();

    return $image;
}


// Convert BLOB image data into something an <img> can display
function imageDataUri($mimeType, $imageData)
{
    return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
}


// Safely escape text for HTML
function escape($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}


// Format the upload date
function formatUploadedAt($date)
{
    return date('M j, Y g:i A', strtotime($date));
}

    /*
    //Prefetch imagesss
    function fetchImages($conn)
    {
        $images = array();
        
        $result = $conn->query(
            'SELECT id, image_name, image_type, image_data, uploaded_at
         FROM students_image
         ORDER BY uploaded_at DESC'
    );
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $images[] = $row;
            }
            
            $result->free();
            }
            
            return $images;
            }
            
            function fetchImageById($conn, $imageId)
            {
    $statement = $conn->prepare(
        'SELECT id, student_id, image_name, image_type, image_data, uploaded_at
         FROM students_image
         WHERE id = ?
         LIMIT 1'
    );
    
    if (!$statement) {
        return null;
        }
        
        $statement->bind_param('i', $imageId);
        $statement->execute();
        
        $result = $statement->get_result();
    $image = $result->fetch_assoc();
    
    $statement->close();

    return $image;
    }









//Validate image
function handleImageUpload($conn, $file)
{
    if ($file === null) {
        return array('error', 'Please choose an image file.', null);
    }

    if (isset($file['error'])) {
        $fileError = $file['error'];
        } else {
            $fileError = UPLOAD_ERR_NO_FILE;
        }
        
        if ($fileError === UPLOAD_ERR_NO_FILE) {
            return array('error', 'Please choose an image file.', null);
            }
            
    $uploadError = getUploadErrorMessage((int) $fileError);
    
    if ($uploadError !== null) {
        return array('error', $uploadError, null);
        }
        
        $fileSize = isset($file['size']) ? $file['size'] : 0;
        
        if ($fileSize > MAX_IMAGE_SIZE) {
            return array('error', 'Image size must be 5 MB or less.', null);
            }
            
            $tmpName = isset($file['tmp_name']) ? $file['tmp_name'] : '';
            
            if ($tmpName === '' || !is_uploaded_file($tmpName)) {
                return array('error', 'The uploaded file is not valid.', null);
                }
                
                $imageInfo = @getimagesize($tmpName);
                
                if ($imageInfo === false || empty($imageInfo['mime'])) {
                    return array('error', 'Please upload a valid image file.', null);
                    }
                    
                    $allowedMimeTypes = array(
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/webp'
                        );
                        
                        $mimeType = $imageInfo['mime'];
                        
                        if (!in_array($mimeType, $allowedMimeTypes, true)) {
                            return array(
                                'error',
                                'Only JPG, PNG, GIF, and WebP files are allowed.',
                                null
                                );
                                }
                                
                                $imageData = file_get_contents($tmpName);
                                
                                if ($imageData === false) {
         return array('error', 'Unable to read the uploaded file.', null);
    }

    $imageName = isset($file['name']) ? trim((string) $file['name']) : '';

    if ($imageName === '') {
        $imageName = 'image';
        }
        
        
        $student_id = 1
        
        try {
        $statement = $conn->prepare(
            'INSERT INTO students_image (student_id, image_name, image_type, image_data) VALUES (?, ?, ?, ?)'
            );
            
            $statement->bind_param('isss', $student_id,  $imageName, $mimeType, $imageData);
            $statement->execute();
            
            $imageId = $statement->insert_id;
            
            $statement->close();

            $uploadedImage = fetchImageById($conn, $imageId);
            
            return array(
                'success',
                'Image uploaded successfully.',
                $uploadedImage
                );
                
    } catch (Exception $exception) {
        return array(
            'error',
            'Image upload failed. Please try again.',
            null
        );
    }
    }*/
    function handleImageUpload($conn, $file)
{
    if ($file === null) {
        return array('error', 'Please choose an image file.', null);
        }
        
        $fileError = isset($file['error'])
        ? $file['error']
        : UPLOAD_ERR_NO_FILE;
        
        if ($fileError === UPLOAD_ERR_NO_FILE) {
            return array('error', 'Please choose an image file.', null);
    }
    
    $uploadError = getUploadErrorMessage((int) $fileError);
    
    if ($uploadError !== null) {
        return array('error', $uploadError, null);
        }
        
        $fileSize = isset($file['size'])
        ? $file['size']


        : 0;
        

    
        if ($fileSize > MAX_IMAGE_SIZE) {
            return array('error', 'Image size must be 5 MB or less.', null);
            }
            
            $tmpName = isset($file['tmp_name'])
            ? $file['tmp_name']
            : '';
            
            if ($tmpName === '' || !is_uploaded_file($tmpName)) {
                return array('error', 'The uploaded file is not valid.', null);
                }
                
                $imageInfo = @getimagesize($tmpName);
                
                if ($imageInfo === false || empty($imageInfo['mime'])) {
                    return array('error', 'Please upload a valid image file.', null);
                    }
                    
    $allowedMimeTypes = array(
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    );
    
    $mimeType = $imageInfo['mime'];
    
    if (!in_array($mimeType, $allowedMimeTypes, true)) {
        return array(
            'error',
            'Only JPG, PNG, GIF, and WebP files are allowed.',
            null
            );
    }
    
    $imageData = file_get_contents($tmpName);
    
    if ($imageData === false) {
        return array(
            'error',
            'Unable to read the uploaded file.',
            null
            );
            }
            
            $imageName = isset($file['name'])
            ? trim((string) $file['name'])
            : '';
            
            if ($imageName === '') {
                $imageName = 'image';
                }
                
                /*
                * TEMPORARY STUDENT ID
                *
                * Replace this with the actual logged-in student's ID later.
                */
    $student_id = $_SESSION["student_id"];
    
    try {

        $statement = $conn->prepare(
            'INSERT INTO students_image
    (student_id, image_name, image_type, image_data)
VALUES
    (?, ?, ?, ?)
ON DUPLICATE KEY UPDATE
    image_name = VALUES(image_name),
    image_type = VALUES(image_type),
    image_data = VALUES(image_data),
    uploaded_at = CURRENT_TIMESTAMP'
       
       /*
       INSERT INTO students_image
            (student_id, image_name, image_type, image_data)
            VALUES (?, ?, ?, ?)'
       */
       
       
       
       
       );

        if (!$statement) {
            return array(
                'error',
                'Unable to prepare image upload.',
                null
            );
        }

        $statement->bind_param(
            'isss',
            $student_id,
            $imageName,
            $mimeType,
            $imageData
        );

        $statement->execute();

        $imageId = $statement->insert_id;

        $statement->close();

        $uploadedImage = fetchImageById($conn, $imageId);

        return array(
            'success',
            'Image uploaded successfully.',
            $uploadedImage
        );

    } catch (Exception $exception) {

        return array(
            'error',
            'Image upload failed. Please try again.',
            null
        );
    }
}

/* Upload to server */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $uploadedFile = isset($_FILES['image'])
        ? $_FILES['image']
        : null;

    $uploadResult = handleImageUpload($conn, $uploadedFile);

    $messageType = $uploadResult[0];
    $message = $uploadResult[1];
    $latestImage = $uploadResult[2];
   
    if($latestImage) {
        header("Location: /kwasu_demo/dashboard");
        exit;
    }
}

$image = fetchSingleImage($conn);
?>
