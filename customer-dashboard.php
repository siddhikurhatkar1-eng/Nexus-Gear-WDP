<?php

session_start();

require_once "config/database.php";


/* =========================================================
   CHECK LOGIN
========================================================= */

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;

}


/* =========================================================
   CHECK CUSTOMER ROLE
========================================================= */

if ($_SESSION["user_role"] !== "customer") {

    header("Location: admin-dashboard.php");

    exit;

}


/* =========================================================
   GET CURRENT CUSTOMER
========================================================= */

$user_id = $_SESSION["user_id"];


$stmt = $conn->prepare(
    "SELECT id, name, email, role, created_at
     FROM users
     WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();


/* =========================================================
   SAFETY CHECK
========================================================= */

if (!$user) {

    session_destroy();

    header("Location: login.php");

    exit;

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

    <title>My Account | Nexus Gear</title>


    <link
        rel="stylesheet"
        href="css/style.css"
    >


    <style>

        /* =================================================
           DASHBOARD PAGE
        ================================================= */

        .dashboard-page {

            min-height: 100vh;

            padding: 40px 20px;

        }


        .dashboard-container {

            max-width: 1100px;

            margin: 0 auto;

        }


        /* =================================================
           HEADER
        ================================================= */

        .dashboard-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            margin-bottom: 35px;

        }


        .dashboard-header h1 {

            margin-bottom: 8px;

        }


        .dashboard-header p {

            opacity: 0.7;

        }


        .dashboard-actions {

            display: flex;

            gap: 10px;

        }


        /* =================================================
           BUTTONS
        ================================================= */

        .dashboard-button {

            display: inline-block;

            padding: 12px 18px;

            border-radius: 8px;

            text-decoration: none;

            font-weight: 700;

            background: #6d28d9;

            color: white;

            transition: 0.2s;

        }


        .dashboard-button:hover {

            background: #7c3aed;

            transform: translateY(-1px);

        }


        .logout-button {

            background: #991b1b;

        }


        .logout-button:hover {

            background: #b91c1c;

        }


        /* =================================================
           DASHBOARD GRID
        ================================================= */

        .dashboard-grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 20px;

        }


        /* =================================================
           DASHBOARD CARDS
        ================================================= */

        .dashboard-card {

            background: #111827;

            border: 1px solid #273449;

            border-radius: 16px;

            padding: 25px;

        }


        .dashboard-card h2 {

            margin-top: 0;

            margin-bottom: 20px;

        }


        /* =================================================
           PROFILE
        ================================================= */

        .profile-row {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            padding: 14px 0;

            border-bottom:
                1px solid #273449;

        }


        .profile-row:last-child {

            border-bottom: none;

        }


        .profile-label {

            opacity: 0.65;

        }


        .profile-value {

            font-weight: 600;

            text-align: right;

        }


        /* =================================================
           WELCOME ICON
        ================================================= */

        .welcome-icon {

            font-size: 42px;

            margin-bottom: 10px;

        }


        /* =================================================
           MY ORDERS DESCRIPTION
        ================================================= */

        .orders-description {

            opacity: 0.7;

            line-height: 1.6;

            margin-bottom: 20px;

        }


        /* =================================================
           MOBILE
        ================================================= */

        @media (max-width: 700px) {

            .dashboard-header {

                flex-direction: column;

                align-items: flex-start;

            }


            .dashboard-actions {

                flex-wrap: wrap;

            }


            .dashboard-grid {

                grid-template-columns: 1fr;

            }


            .profile-row {

                align-items: flex-start;

                flex-direction: column;

                gap: 5px;

            }


            .profile-value {

                text-align: left;

            }

        }

    </style>

</head>


<body>


<div class="dashboard-page">


    <div class="dashboard-container">


        <!-- =================================================
             DASHBOARD HEADER
        ================================================== -->

        <div class="dashboard-header">


            <div>


                <div class="welcome-icon">

                    🤖

                </div>


                <h1>

                    Welcome,

                    <?php

                    echo htmlspecialchars(
                        $user["name"]
                    );

                    ?>!

                </h1>


                <p>

                    Your Nexus Gear account

                </p>


            </div>


            <div class="dashboard-actions">


                <a
                    href="index.php"
                    class="dashboard-button"
                >

                    Continue Shopping

                </a>


                <a
                    href="logout.php"
                    class="dashboard-button logout-button"
                >

                    Logout

                </a>


            </div>


        </div>



        <!-- =================================================
             DASHBOARD CARDS
        ================================================== -->

        <div class="dashboard-grid">


            <!-- =================================================
                 MY PROFILE
            ================================================== -->

            <div class="dashboard-card">


                <h2>

                    👤 My Profile

                </h2>


                <!-- NAME -->

                <div class="profile-row">


                    <span class="profile-label">

                        Name

                    </span>


                    <span class="profile-value">

                        <?php

                        echo htmlspecialchars(
                            $user["name"]
                        );

                        ?>

                    </span>


                </div>



                <!-- EMAIL -->

                <div class="profile-row">


                    <span class="profile-label">

                        Email

                    </span>


                    <span class="profile-value">

                        <?php

                        echo htmlspecialchars(
                            $user["email"]
                        );

                        ?>

                    </span>


                </div>



                <!-- ACCOUNT TYPE -->

                <div class="profile-row">


                    <span class="profile-label">

                        Account Type

                    </span>


                    <span class="profile-value">

                        Customer

                    </span>


                </div>



                <!-- MEMBER SINCE -->

                <div class="profile-row">


                    <span class="profile-label">

                        Member Since

                    </span>


                    <span class="profile-value">

                        <?php

                        echo date(
                            "d M Y",
                            strtotime(
                                $user["created_at"]
                            )
                        );

                        ?>

                    </span>


                </div>


            </div>



            <!-- =================================================
                 MY ORDERS
            ================================================== -->

            <div class="dashboard-card">


                <h2>

                    📦 My Orders

                </h2>


                <p class="orders-description">

                    View your previous orders,
                    payment details, delivery address
                    and order status.

                </p>


                <a
                    href="my-orders.php"
                    class="dashboard-button"
                >

                    View My Orders

                </a>


            </div>


        </div>


    </div>


</div>


</body>

</html>