<?php

require_once "config/database.php";

session_start();


/* =========================================================
   CHECK LOGIN
========================================================= */

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;

}


/* =========================================================
   CHECK ADMIN ROLE
========================================================= */

if ($_SESSION["user_role"] !== "admin") {

    header("Location: customer-dashboard.php");

    exit;

}


/* =========================================================
   GET CURRENT USER
========================================================= */

$userName =
    $_SESSION["user_name"];

$userEmail =
    $_SESSION["user_email"];

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard | Nexus Gear</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >


    <style>

        body {

            margin: 0;

            background: #050713;

            color: white;

            font-family: Arial, sans-serif;

        }


        .admin-container {

            max-width: 1200px;

            margin: auto;

            padding: 60px 30px;

        }


        .admin-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            margin-bottom: 45px;

        }


        .admin-welcome {

            display: flex;

            align-items: center;

            gap: 20px;

        }


        .admin-robot {

            font-size: 55px;

        }


        .admin-welcome h1 {

            margin: 0;

            font-size: 38px;

        }


        .admin-welcome p {

            margin-top: 8px;

            color: #a7a7b5;

            font-size: 17px;

        }


        .admin-buttons {

            display: flex;

            gap: 12px;

        }


        .admin-button {

            display: inline-block;

            padding: 14px 22px;

            border-radius: 10px;

            text-decoration: none;

            font-weight: bold;

            color: white;

            background: linear-gradient(
                90deg,
                #6d28d9,
                #9333ea
            );

        }


        .logout-button {

            background: #991b1b;

        }


        .section-title {

            margin-bottom: 20px;

            font-size: 26px;

        }


        .dashboard-grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 25px;

        }


        .dashboard-card {

            background: #111827;

            border: 1px solid #273449;

            border-radius: 18px;

            padding: 28px;

            min-height: 180px;

        }


        .dashboard-card h2 {

            margin-top: 0;

            font-size: 24px;

        }


        .dashboard-card p {

            color: #a7a7b5;

            line-height: 1.6;

        }


        .card-button {

            display: inline-block;

            margin-top: 15px;

            padding: 11px 18px;

            border-radius: 8px;

            background: #6d28d9;

            color: white;

            text-decoration: none;

            font-weight: bold;

        }


        .admin-badge {

            display: inline-block;

            margin-top: 10px;

            padding: 6px 12px;

            border-radius: 20px;

            background: #3b1c72;

            color: #c4b5fd;

            font-size: 13px;

            font-weight: bold;

        }


        @media (max-width: 700px) {

            .admin-header {

                flex-direction: column;

                align-items: flex-start;

            }


            .dashboard-grid {

                grid-template-columns: 1fr;

            }


            .admin-buttons {

                width: 100%;

            }

        }

    </style>

</head>


<body>


<div class="admin-container">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="admin-header">


        <div class="admin-welcome">


            <div class="admin-robot">

                🤖

            </div>


            <div>

                <h1>

                    Welcome, <?php

                    echo htmlspecialchars(
                        $userName
                    );

                    ?>!

                </h1>


                <p>

                    Nexus Gear Admin Dashboard

                </p>


                <span class="admin-badge">

                    👑 ADMIN

                </span>

            </div>


        </div>


        <div class="admin-buttons">


            <a
                href="index.php"
                class="admin-button"
            >

                Continue Shopping

            </a>


            <a
                href="logout.php"
                class="admin-button logout-button"
            >

                Logout

            </a>


        </div>


    </div>



    <!-- =====================================================
         ACCOUNT
    ====================================================== -->

    <h2 class="section-title">

        👤 My Account

    </h2>


    <div class="dashboard-grid">


        <!-- PROFILE -->

        <div class="dashboard-card">


            <h2>

                👤 My Profile

            </h2>


            <p>

                <strong>Name:</strong>

                <?php

                echo htmlspecialchars(
                    $userName
                );

                ?>

            </p>


            <p>

                <strong>Email:</strong>

                <?php

                echo htmlspecialchars(
                    $userEmail
                );

                ?>

            </p>


            <p>

                <strong>Account Type:</strong>

                Administrator

            </p>


            <a
                href="customer-dashboard.php"
                class="card-button"
            >

                View Customer Dashboard

            </a>


        </div>



        <!-- ORDERS -->

        <div class="dashboard-card">


            <h2>

                📦 My Orders

            </h2>


            <p>

                View the orders placed
                using your account.

            </p>


            <a
                href="customer-dashboard.php"
                class="card-button"
            >

                View My Orders

            </a>


        </div>


    </div>



    <!-- =====================================================
         ADMIN
    ====================================================== -->

    <h2
        class="section-title"
        style="margin-top:50px;"
    >

        👑 Administration

    </h2>


    <div class="dashboard-grid">


        <!-- PRODUCTS -->

        <div class="dashboard-card">


            <h2>

                🎮 Manage Products

            </h2>


            <p>

                Add, edit and remove
                gaming products.

            </p>


            <a
                href="manage-products.php"
                class="card-button"
            >

                Manage Products

            </a>


        </div>



        <!-- ORDERS -->

        <div class="dashboard-card">


            <h2>

                📦 Manage Orders

            </h2>


            <p>

                View customer orders
                and update their status.

            </p>


            <a
                href="manage-orders.php"
                class="card-button"
            >

                Manage Orders

            </a>


        </div>



        <!-- USERS -->

        <div class="dashboard-card">


            <h2>

                👥 Manage Users

            </h2>


            <p>

                View registered customers
                and their account roles.

            </p>


            <a
                href="manage-users.php"
                class="card-button"
            >

                Manage Users

            </a>


        </div>



        <!-- CONTACTS -->

        <div class="dashboard-card">


            <h2>

                💬 Customer Messages

            </h2>


            <p>

                View customer support
                messages and enquiries.

            </p>


            <a
                href="manage-messages.php"
                class="card-button"
            >

                View Messages

            </a>


        </div>


    </div>


</div>


</body>

</html>