<?php

require_once "config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    /* ---------------------------------------------
       VALIDATION
    --------------------------------------------- */

    if ($name === "" || $email === "" || $password === "") {

        $message = "Please fill in all required fields.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $message_type = "error";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "error";

    } else {

        /* ---------------------------------------------
           CHECK IF EMAIL ALREADY EXISTS
        --------------------------------------------- */

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);

        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "An account with this email already exists.";
            $message_type = "error";

        } else {

            /* ---------------------------------------------
               HASH PASSWORD
            --------------------------------------------- */

            $hashed_password =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


            /* ---------------------------------------------
               CREATE CUSTOMER
            --------------------------------------------- */

            $role = "customer";

            $insert = $conn->prepare(
                "INSERT INTO users
                (name, email, password, role)
                VALUES (?, ?, ?, ?)"
            );

            $insert->bind_param(
                "ssss",
                $name,
                $email,
                $hashed_password,
                $role
            );


            if ($insert->execute()) {

                header(
                    "Location: login.php?registered=1"
                );

                exit;

            } else {

                $message =
                    "Something went wrong. Please try again.";

                $message_type = "error";

            }

            $insert->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Create Account | Nexus Gear</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

    <style>

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .auth-box {
            width: 100%;
            max-width: 450px;
            padding: 35px;
            border-radius: 18px;
            background: #111827;
            border: 1px solid #273449;
        }

        .auth-logo {
            text-align: center;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .auth-logo span {
            color: #8b5cf6;
        }

        .auth-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .auth-form-group {
            margin-bottom: 18px;
        }

        .auth-form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
        }

        .auth-form-group input {
            width: 100%;
            padding: 13px;
            border-radius: 8px;
            border: 1px solid #334155;
            background: #0f172a;
            color: white;
            box-sizing: border-box;
        }

        .auth-button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(
                90deg,
                #6d28d9,
                #9333ea
            );
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        .auth-message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
            text-align: center;
        }

        .auth-error {
            background: #451a1a;
            color: #fca5a5;
        }

        .auth-links {
            text-align: center;
            margin-top: 20px;
        }

        .auth-links a {
            color: #a78bfa;
        }

    </style>

</head>

<body>

<div class="auth-page">

    <div class="auth-box">

        <div class="auth-logo">
            NEXUS<span>GEAR</span>
        </div>

        <h2 class="auth-title">
            Create Your Account
        </h2>


        <?php if ($message !== ""): ?>

            <div class="auth-message auth-error">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <div class="auth-form-group">

                <label>
                    Full Name
                </label>

                <input
                    type="text"
                    name="name"
                    required
                    value="<?php
                        echo htmlspecialchars(
                            $_POST["name"] ?? ""
                        );
                    ?>"
                >

            </div>


            <div class="auth-form-group">

                <label>
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    required
                    value="<?php
                        echo htmlspecialchars(
                            $_POST["email"] ?? ""
                        );
                    ?>"
                >

            </div>


            <div class="auth-form-group">

                <label>
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                >

            </div>


            <div class="auth-form-group">

                <label>
                    Confirm Password
                </label>

                <input
                    type="password"
                    name="confirm_password"
                    required
                >

            </div>


            <button
                type="submit"
                class="auth-button"
            >

                CREATE ACCOUNT

            </button>


        </form>


        <div class="auth-links">

            Already have an account?

            <a href="login.php">
                Login
            </a>

        </div>


        <div class="auth-links">

            <a href="index.php">
                ← Back to Nexus Gear
            </a>

        </div>

    </div>

</div>

</body>

</html>