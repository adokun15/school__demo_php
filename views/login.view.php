<?php

session_start();

require_once __DIR__ . "/../model/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $matric_no = isset($_POST["matric_no"])
        ? trim($_POST["matric_no"])
        : "";

    $password = isset($_POST["password"])
        ? $_POST["password"]
        : "";

    if (empty($matric_no) || empty($password)) {
        $error = "Please enter your matriculation number and password.";

    } else {

        $stmt = $conn->prepare("
            SELECT id, matric_no, full_name, password
            FROM students_info
            WHERE matric_no = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $matric_no);

        $stmt->execute();

        $result = $stmt->get_result();


        if ($result->num_rows === 1) {

            $student = $result->fetch_assoc();

            if (crypt($password, $student["password"]) === $student["password"]) {
                session_regenerate_id(true);

                $_SESSION["student_id"] = $student["id"];

                header("Location: /kwasu_demo/dashboard");
                exit;

            } else {

                $error = "Invalid matriculation number or password.";
            }

        } else {

            $error = "Invalid matriculation number or password.";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Login</title>
<style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #f7f7f7;
        color: #222;
    }

    main {
        width: min(420px, 92%);
        margin: 80px auto;
    }
    header {
        margin-bottom: 25px;
    }
    h1 {
        margin: 0 0 8px;
        font-size: 28px;
    }
    header p {
        margin: 0;
        color: #666;
    }
    form {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 25px;
    }
    fieldset {
        border: 0;
        padding: 0;
        margin: 0;
    }
    legend {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 22px;
    }
    .field {
        display: flex;
        flex-direction: column;
        gap: 7px;
        margin-bottom: 18px;
    }
    label {
        font-size: 14px;
        font-weight: 500;
    }
    input {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font: inherit;
        outline: none;
    }
    input:focus {
        border-color: #222;
    }
    button {
        width: 100%;
        padding: 11px;
        border: 0;
        border-radius: 6px;
        background: green;
        color: white;
        font: inherit;
        cursor: pointer;
    }
    button:hover {
        background: #444;
    }


    
        #cards {
            margin: 45px 0;
            width: 100%;
            display: grid;
            column-gap: 1rem;
            grid-template-columns: 1fr 1fr 1fr;
        }

        #box_2 {
            padding: 20px 1%;
            margin: 0 auto;
            width: 70%;
        }

        .card_box {
            grid-column: auto;
            box-shadow: inset;
            padding: 4px 12px;
        }

        .card_box_header {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .card_box_desc {
            font-size: 16px;
            font-weight: 400;
            margin-bottom: 5px;
        }

        .card_box_button {
            margin-top: 16px;
            border-radius: 3px;
            background-color: green;
            padding: 4px 14px;
            outline: none;
            border: none;
            color: white;
        }

    .message {
        margin-bottom: 20px;
        padding: 10px 12px;
        border-radius: 6px;
        font-size: 14px;
    }

    .error {
        background: #fff0f0;
        color: #b00020;
    }

    .success {
        background: #f0fff4;
        color: #176b36;
    }

    .signup {
        margin-top: 18px;
        text-align: center;
        font-size: 14px;
        color: #666;
    }

    .signup a {
        color: #222;
        font-weight: 500;
    }
</style>
</head>
<body style="max-height: 100vh; display: flex;">    
    <section style="background-color:green; width:30%; height: 100vh;">
        <img />
    </section>
    <section id="box_2">
        <nav style="float: right">
            <button style="padding: 1px 3px; border-radius: 3px;">En</button>
        </nav>
        
        <!-- The logo part and title description -->
        <div style=" margin: 2rem auto; width: fit-content;">
            <img style="margin: 0 auto; " width="150px" height="150px" src="public/KWASU_kwasu-transparent.png" />
            <p
            style="text-align: center; font-weight: 700; font-family: Arial, Helvetica, sans-serif; color: green; font-size: 1.25rem;">
    Kwara State University,
    Malete</p>
        </div>

        
        <main>

<?php if ($error): ?>

    <div class="message error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form method="POST">
    <fieldset>
        <div class="field">
            <label for="matric_no">
                Matriculation Number
            </label>

            <input
                type="text"
                id="matric_no"
                name="matric_no"
                value="<?= isset($_POST["matric_no"]) ? htmlspecialchars($_POST["matric_no"]) : "" ?>"
                autocomplete="username"
                placeholder="kwasu_cs_1"
                required
            >
        </div>

        <div class="field">
            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
            >
        </div>

    </fieldset>

    <button type="submit">
        Log In
    </button>

    <p class="signup">
        Don't have an account?
        <a href="register">Create an account</a>
    </p>

</form>
</main>
    </section>
</body>
</html>
