<?php

session_start();

require_once __DIR__ . "/../model/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = isset($_POST["full_name"])
        ? trim($_POST["full_name"])
        : "";

    $username = isset($_POST["username"])
        ? trim($_POST["username"])
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

        //Check if student is older than 16;
    $DobConverted = DateTime::createFromFormat(
    'Y-m-d',
    $date_of_birth
);
    
    $today = new DateTime();
    $age = $today->diff($DobConverted)->y;
        
    if (
        empty($full_name) ||
        empty($date_of_birth) ||
        empty($gender) ||
        empty($phone) ||
        empty($username) ||
        empty($password) ||
        empty($confirm_password)
    ) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif($age < 16) {
       $error = 'You must be at least 16 years old.';
    } elseif(!preg_match('/^[0-9]{11}$/', $phone)) {
        $error = 'You inserted an Invalid phone number!';
    }
    elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        /*Create password hash.*/
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

        //Db response variable
        $stmt = $conn->prepare("
            INSERT INTO students_info
            (
                full_name,
                username,
                date_of_birth,
                gender,
                phone,
                password
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        /*Make sure prepare() worked.*/
        if (!$stmt) {
            $error = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param(
                "ssssss",
                $full_name,
                $username,
                $date_of_birth,
                $gender,
                $phone,
                $hashed_password
            );

            /*Execute INSERT.*/
            if ($stmt->execute()) {
                $student_id = $conn->insert_id;
                $matric_no = "kwasu_cs_" . $student_id;
                $update = $conn->prepare("
                UPDATE students_info
                SET matric_no = ?
                WHERE id= ?
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
                                $update->close();
                                $stmt->close();
                                
                                //$redirect = "Location: /kwasu_demo/login?registered=" . $matric_no;
                                $redirect = "Location: /kwasu_demo/login?registered=1";
                                header($redirect);
                                exit;
                                } else {
                                    $error = "Could not save matriculation number: ". $update->error;
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
        <title>Join Kwasu today!</title>
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
        margin: 40px auto;
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
<body style="height: fit-content; display: flex;">    
    <section style="background-color:green; width:30%; ">
        <img />
    </section>
    <section id="box_2">
        <nav style="float: right">
            <button style="padding: 1px 3px; border-radius: 3px;">En</button>
        </nav>
        
        <!-- The logo part and title description -->
        <div style=" margin: 0.2rem auto; width: fit-content;">
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



<form  method="POST">
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
                    <label for="username">
                        Student Id
                    </label>
                    
                    <input
                    type="text"
                    placeholder="Enter a unique username!"
                    id="username"
                    name="username"
                    value="<?= isset($_POST["username"]) ? htmlspecialchars($_POST["username"]) : "" ?>"
            
                    autocomplete="username"
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
        </section>
</body>
</html>

