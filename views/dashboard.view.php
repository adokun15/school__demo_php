<?php
session_start();
require_once __DIR__ . "/../model/db.php";

// User must be logged in
if (!isset($_SESSION["student_id"])) {
    header("Location: /kwasu_demo/login");
    exit;
}

$student_id = $_SESSION["student_id"];

$stmt = $conn->prepare("
    SELECT
        matric_no,
        full_name,
        date_of_birth,
        gender,
        phone
    FROM students_info
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $student_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    // Student no longer exists
    session_destroy();
    header("Location: /kwasu_demo/login");
    exit;
}

$student = $result->fetch_assoc();

$stmt->close();
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

    article {
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
</style>
</head>

<body>
<section>
    <h1>Welcome to your Dashboard, 
          <?= htmlspecialchars($student["full_name"]) ?>
    </h1>
    <p>Your journey starts here gee </p>

<article>

    <div>
        <h2>Full name</h2>
        <?= htmlspecialchars($student["full_name"]) ?>
        
    </div>
    
    <div>
        <h2>Matric no</h2>
        <?= htmlspecialchars($student["matric_no"]) ?>
        
    </div>
    
    <div>
        <h2>Date of Birth</h2>
        <?= htmlspecialchars($student["date_of_birth"]) ?>
        
    </div>

    <div>
        <h2>Gender</h2>
    <?= htmlspecialchars($student["gender"]) ?>
</div>

<div>
    <h2>Phone number</h2>
    <?= htmlspecialchars($student["phone"]) ?>
    </div>
    
</article>

<div style="margin: 12px 0px; display: flex; gap: 15px;">  
    <div class="actions">
        <a class="logout" href="logout">
            Log out
        </a>
    </div>
    
    <div class="actions" type="button">
        <a href="dashboard/edit">    
            Upload profile
        </a>    
    </div>
</div>
</section>
</body>
</html>