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
   GET CUSTOMER MESSAGES
========================================================= */

$messages = $conn->query(
    "SELECT id, name, email, subject, message, submission_date
     FROM contacts
     ORDER BY id DESC"
);

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Customer Messages | Nexus Gear</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            background: #070914;

            color: white;

            font-family: Arial, sans-serif;

        }


        .container {

            width: 92%;

            max-width: 1200px;

            margin: 40px auto;

        }


        .top-bar {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 30px;

        }


        h1 {

            margin: 0;

            font-size: 32px;

        }


        .back-button {

            background: #7c2be8;

            color: white;

            text-decoration: none;

            padding: 12px 20px;

            border-radius: 8px;

            font-weight: bold;

        }


        .messages-box {

            background: #111827;

            border: 1px solid #263247;

            border-radius: 14px;

            padding: 25px;

            overflow-x: auto;

        }


        .messages-box h2 {

            margin-top: 0;

            margin-bottom: 25px;

        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 900px;

        }


        th,
        td {

            padding: 15px;

            text-align: left;

            border-bottom: 1px solid #293449;

            vertical-align: top;

        }


        th {

            color: #c084fc;

            font-size: 15px;

        }


        td {

            color: #eeeeee;

        }


        .message-text {

            max-width: 350px;

            line-height: 1.5;

            color: #d1d5db;

        }


        .subject {

            color: #ffffff;

            font-weight: bold;

        }


        .date {

            color: #a7a7b5;

            white-space: nowrap;

        }


        .no-messages {

            text-align: center;

            padding: 40px;

            color: #a7a7b5;

        }


        .message-count {

            display: inline-block;

            margin-left: 8px;

            padding: 5px 10px;

            border-radius: 20px;

            background: #3b1c72;

            color: #c4b5fd;

            font-size: 13px;

        }


        @media (max-width: 700px) {

            .top-bar {

                flex-direction: column;

                align-items: flex-start;

                gap: 15px;

            }

        }

    </style>

</head>


<body>


<div class="container">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="top-bar">


        <h1>

            💬 Customer Messages

        </h1>


        <a
            href="admin-dashboard.php"
            class="back-button"
        >

            ← Admin Dashboard

        </a>


    </div>



    <!-- =====================================================
         MESSAGES
    ====================================================== -->

    <div class="messages-box">


        <h2>

            💬 Support Enquiries


            <span class="message-count">

                <?php

                echo $messages->num_rows;

                ?>

            </span>

        </h2>



        <?php if ($messages->num_rows > 0): ?>


            <table>


                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Email</th>

                        <th>Subject</th>

                        <th>Message</th>

                        <th>Date</th>

                    </tr>

                </thead>


                <tbody>


                <?php while (
                    $row = $messages->fetch_assoc()
                ): ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            <?php

                            echo $row["id"];

                            ?>

                        </td>



                        <!-- NAME -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["name"]
                            );

                            ?>

                        </td>



                        <!-- EMAIL -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["email"]
                            );

                            ?>

                        </td>



                        <!-- SUBJECT -->

                        <td>

                            <span class="subject">

                                <?php

                                echo htmlspecialchars(
                                    $row["subject"]
                                    ?? "No Subject"
                                );

                                ?>

                            </span>

                        </td>



                        <!-- MESSAGE -->

                        <td>

                            <div class="message-text">

                                <?php

                                echo nl2br(
                                    htmlspecialchars(
                                        $row["message"]
                                    )
                                );

                                ?>

                            </div>

                        </td>



                        <!-- DATE -->

                        <td>

                            <span class="date">

                                <?php

                                echo date(
                                    "d M Y, h:i A",
                                    strtotime(
                                        $row["submission_date"]
                                    )
                                );

                                ?>

                            </span>

                        </td>


                    </tr>


                <?php endwhile; ?>


                </tbody>


            </table>


        <?php else: ?>


            <div class="no-messages">

                <h2>

                    💬 No Messages Yet

                </h2>

                <p>

                    Customer support messages
                    will appear here when customers
                    contact Nexus Gear.

                </p>

            </div>


        <?php endif; ?>


    </div>


</div>


</body>

</html>