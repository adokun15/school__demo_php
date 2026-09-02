<?php

session_start();

require_once __DIR__ . "/../model/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = isset($_POST["full_name"])
        ? trim($_POST["full_name"])
        : "";

    $date_of_birth = isset($_POST["date_of_birth"])
        ? $_POST["date_of_birth"]
        : "";

    $gender = isset($_POST["gender"])
        ? $_POST["gender"]
        : "";

    $phone = isset($_POST["phone"])
        ? trim($_POST["phone"])
        : "";

    $password = isset($_POST["password"])
        ? $_POST["password"]
        : "";

    $confirm_password = isset($_POST["confirm_password"])
        ? $_POST["confirm_password"]
        : "";


    /*
     * Validate form
     */

    if (
        empty($full_name) ||
        empty($date_of_birth) ||
        empty($gender) ||
        empty($phone) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $error = "Please fill in all fields.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } elseif (strlen($password) < 8) {

        $error = "Password must be at least 8 characters.";

    } else {


        /*
         * Create password hash.
         *
         * password_hash() does not exist in PHP 5.4,
         * so we use crypt() with bcrypt.
         */

        $salt = '$2y$10$' .
            substr(
                str_replace(
                    '+',
                    '.',
                    base64_encode(
                        pack(
                            'N4',
                            mt_rand(),
                            mt_rand(),
                            mt_rand(),
                            mt_rand()
                        )
                    )
                ),
                0,
                22
            );

        $hashed_password = crypt($password, $salt);


        /*
         * Insert student.
         *
         * MySQL automatically creates the ID.
         */

        $stmt = $conn->prepare("
            INSERT INTO students_info
            (
                full_name,
                date_of_birth,
                gender,
                phone,
                password
            )
            VALUES (?, ?, ?, ?, ?)
        ");


        /*
         * Make sure prepare() worked.
         */

        if (!$stmt) {

            $error = "Database error: " . $conn->error;

        } else {


            /*
             * Bind form values.
             */

            $stmt->bind_param(
                "sssss",
                $full_name,
                $date_of_birth,
                $gender,
                $phone,
                $hashed_password
            );


            /*
             * Execute INSERT.
             */

            if ($stmt->execute()) {


                /*
                 * Get the AUTO_INCREMENT ID.
                 */

                $student_id = $conn->insert_id;


                /*
                 * Generate matric number.
                 */

                $matric_no = "kwasu_cs_" . $student_id;


                /*
                 * Update the student's matric number.
                 */

                $update = $conn->prepare("
                    UPDATE students_info
                    SET matric_no = ?
                    WHERE id = ?
                ");


                if (!$update) {

                    $error = "Could not generate matric number: " . $conn->error;

                } else {

                    $update->bind_param(
                        "si",
                        $matric_no,
                        $student_id
                    );


                    if ($update->execute()) {

                        /*
                         * Registration successful.
                         */

                        $update->close();
                        $stmt->close();

                        header("Location: /kwasu_demo/login");
                        exit;

                    } else {

                        $error = "Could not save matriculation number: "
                            . $update->error;

                        $update->close();
                    }
                }


            } else {

                $error = "Registration failed: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

?>


<!DOCTYPE html>

<html>
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Student Account</title>
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
        width: min(650px, 92%);
        margin: 50px auto;
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
        background: #fff;
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
    
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }
    
    .field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }
    
    .full {
        grid-column: 1 / -1;
    }
    
    label {
        font-size: 14px;
        font-weight: 500;
    }

    input,
    select {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font: inherit;
        background: #fff;
        color: #222;
        outline: none;
    }
    
    input:focus,
    select:focus {
        border-color: #222;
    }
    
    button {
        width: 100%;
        margin-top: 25px;
        padding: 11px;
        border: 0;
        border-radius: 6px;
        background: #222;
        color: white;
        font: inherit;
        cursor: pointer;
    }
    
    button:hover {
        background: #444;
    }
    
    .error {
        margin-bottom: 20px;
        padding: 10px 12px;
        border-radius: 6px;
        background: #fff0f0;
        color: #b00020;
        font-size: 14px;
    }
    
    .login {
        margin-top: 18px;
        text-align: center;
        font-size: 14px;
        color: #666;
    }
    
    .login a {
        color: #222;
        font-weight: 500;
    }
    
    @media (max-width: 600px) {
        main {
            margin: 30px auto;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
        }

        .full {
            grid-column: auto;
        }
    }
</style>
</head>
<body>
    <main>
        <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <?php 
            echo PHP_VERSION;
             ?>

<form method="POST">
    <fieldset>
        <legend>Student Information</legend>

        <div class="form-grid">

            <div class="field full">
                <label for="full_name">Full Name</label>

                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    value="<?= isset($_POST["full_name"]) ? htmlspecialchars($_POST["full_name"]) : "" ?>"
                    autocomplete="name"
                    required
                >
            </div>

            <div class="field">
                <label for="date_of_birth">
                    Date of Birth
                </label>

                <input
                    type="date"
                    id="date_of_birth"
                    name="date_of_birth"
                    value="<?= htmlspecialchars($_POST["date_of_birth"]) ?>"
                    required
                >
            </div>

            <div class="field">
                <label for="gender">Gender</label>

                <select id="gender" name="gender" required>
                    <option value="">Select gender</option>
 <option value="male"
        <?= (isset($_POST["gender"]) && $_POST["gender"] === "male") ? "selected" : "" ?>>
        Male
    </option>

    <option value="female"
        <?= (isset($_POST["gender"]) && $_POST["gender"] === "female") ? "selected" : "" ?>>
        Female
    </option>

                </select>
            </div>

            <div class="field full">
                <label for="phone">Phone Number</label>

                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="<?=  isset($_POST["phone"]) ? htmlspecialchars($_POST["phone"]) : "" ?>"
                    autocomplete="tel"
                    required
                >
            </div>

            <div class="field">
                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    required
                >
            </div>

            <div class="field">
                <label for="confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    autocomplete="new-password"
                    required
                >
            </div>

        </div>

    </fieldset>

    <button type="submit">
        Create Account
    </button>

    <p class="login">
        Already have an account?
        <a href="login">Log in</a>
    </p>

</form>


</main>

</body>
</html>
