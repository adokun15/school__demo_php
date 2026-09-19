<?php
session_start();
require_once __DIR__ . '/../model/image.model.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>Welcome to your dashboard!</title>

<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: sans-serif;
    }

    body {
        background: whitesmoke;
        color: black;
        min-height: 100vh;
        padding: 40px 20px;
    }

    section {
        max-width: 800px;
        margin: 0 auto;
    }

    h1 {
        font-size: 22px;
        margin-bottom: 8px;
    }

    section > p {
        color: grey;
        margin-bottom: 30px;
    }

    article, form {
        background: white;
        border: 1px solid #e7e7e7;
        border-radius: 10px;
        padding: 25px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }

    article div {
        padding-bottom: 18px;
        border-bottom: 1px solid green;
    }

    article div:last-child {
        border-bottom: none;
    }

    h2 {
        font-size: 13px;
        font-weight: normal;
        color: #777;
        margin-bottom: 7px;
        text-transform: uppercase;
    }

    article p {
        font-size: 16px;
        font-weight: 500;
    }

    button {
        margin-top: 20px;
        padding: 11px 18px;
        border: none;
        border-radius: 7px;
        background: green;
        color: white;
        font-size: 14px;
        cursor: pointer;
    }

    button:hover {
        background: #444;
    }

    form { 
        margin: 25px auto;
        display: block;
    }

    @media (max-width: 600px) {

        body {
            padding: 25px 15px;
        }

        h1 {
            font-size: 23px;
        }

        article {
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 20px;
        }

    }

    .gallery-card img {
    width: 100%;
    max-width: 300px;
    height: 250px;
    object-fit: cover;
    border-radius: 8px;
}

.profile-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
}

.profile-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eee;
    font-size: 40px;
}
</style>

</head>

<body>

<section>

    <form method="post" enctype="multipart/form-data" class="upload-form">
        <h1>Welcome to Kwasu</h1>
        <h2>Add your profile picture</h2>
   
   <label style="margin-top: 100px; display: block;" for="image">Select image</label>

        <input
            type="file"
            name="image"
            id="image"
            accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp"
        >      
        
        <div id="preview-container" style="display: none;">
            <p>Preview:</p>
            <img
            id="image-preview"
            src=""
            alt="Image preview"
            style="
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            "
    >
</div>

<button id="submission-button" type="submit" style="display: none;">Upload Image</button>
    </form>
    <p>Your Current image</p>
        <article class="gallery-card">
            <?php if ($image): ?>
                <img
                src="<?= imageDataUri($image['image_type'], $image['image_data']); ?>"
                alt="<?= escape($image['image_name']); ?>"
                >
                
 <?php else: ?>
    <div class="profile-image profile-placeholder">
        <span>.</span>
    </div>

 <?php endif; ?>
        


    </article>
    <div style="margin: 12px 0px; display: flex; gap: 15px;">  

        <div class="actions">

            <a class="logout" href="logout">
                Log out
            </a>

        </div>

        <div class="actions" type="button">

            <a href="/kwasu_demo/dashboard">     
                Go back
            </a>     

        </div>

    </div>

</section>

<script>
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const previewContainer = document.getElementById('preview-container');
    
    const ImageSubmissionButton = document.getElementById('submission-button');
    
    imageInput.addEventListener('change', function () {
        const file = imageInput.files[0];
        if (!file) {
            previewContainer.style.display = 'none';
            ImageSubmissionButton.style.display = 'none';
            imagePreview.src = '';
            return;
        }

        ImageSubmissionButton.style.display = 'block';
        
        var reader = new FileReader();
        
        reader.onload = function (event) {
            imagePreview.src = event.target.result;
            previewContainer.style.display = 'block';
        };

        reader.readAsDataURL(file);
    });
</script>
</body>

</html>