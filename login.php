<?php

require_once "config/database.php";

session_start();

$message = "";
$message_type = "";


/* =========================================================
   SHOW REGISTRATION MESSAGE
========================================================= */

if (isset($_GET["registered"]) && $_GET["registered"] == "1") {

    $message = "Account created successfully! Please login.";
    $message_type = "success";

}


/* =========================================================
   LOGIN
========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    /* -----------------------------------------------------
       BASIC VALIDATION
    ----------------------------------------------------- */

    if ($email === "" || $password === "") {

        $message = "Please enter your email and password.";
        $message_type = "error";

    } else {

        /* -------------------------------------------------
           FIND USER
        ------------------------------------------------- */

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param(
            "s",
            $email
        );

        $stmt->execute();

        $result = $stmt->get_result();


        /* -------------------------------------------------
           CHECK USER
        ------------------------------------------------- */

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();


            /* ---------------------------------------------
               CHECK PASSWORD
            --------------------------------------------- */

            if (
                password_verify(
                    $password,
                    $user["password"]
                )
            ) {

                /* -----------------------------------------
                   CREATE LOGIN SESSION
                ----------------------------------------- */

                $_SESSION["user_id"] =
                    $user["id"];

                $_SESSION["user_name"] =
                    $user["name"];

                $_SESSION["user_email"] =
                    $user["email"];

                $_SESSION["user_role"] =
                    $user["role"];


                /* -----------------------------------------
                   ROLE BASED REDIRECT
                ----------------------------------------- */

                if ($user["role"] === "admin") {

                    header(
                        "Location: admin-dashboard.php"
                    );

                    exit;

                } else {

                    header(
                        "Location: customer-dashboard.php"
                    );

                    exit;

                }

            } else {

                $message =
                    "Incorrect email or password.";

                $message_type = "error";

            }

        } else {

            $message =
                "Incorrect email or password.";

            $message_type = "error";

        }


        $stmt->close();

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

    <title>Login | Nexus Gear</title>

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


        .auth-success {

            background: #143c2a;

            color: #86efac;

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

            Welcome Back

        </h2>


        <?php if ($message !== ""): ?>

            <div
                class="auth-message
                <?php
                    echo $message_type === "success"
                        ? "auth-success"
                        : "auth-error";
                ?>"
            >

                <?php

                echo htmlspecialchars(
                    $message
                );

                ?>

            </div>

        <?php endif; ?>


        <form method="POST">


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


            <button
                type="submit"
                class="auth-button"
            >

                LOGIN

            </button>


        </form>


        <div class="auth-links">

            Don't have an account?

            <a href="register.php">
                Create Account
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
