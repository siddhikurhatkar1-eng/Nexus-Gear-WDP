<?php

session_start();

require_once "config/database.php";


/* =========================================================
   CHECK WHETHER AN ORDER WAS ACTUALLY CREATED
========================================================= */

if (!isset($_SESSION["last_order_id"])) {

    header("Location: index.php");
    exit;

}


$orderId = $_SESSION["last_order_id"];


/* =========================================================
   GET THE ORDER INFORMATION
========================================================= */

$stmt = $conn->prepare(
    "SELECT email
     FROM orders
     WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $orderId
);

$stmt->execute();

$result = $stmt->get_result();

$order = $result->fetch_assoc();

$stmt->close();


/* =========================================================
   SAFETY CHECK
========================================================= */

if (!$order) {

    unset($_SESSION["last_order_id"]);

    header("Location: index.php");
    exit;

}


$orderEmail = $order["email"];


/* =========================================================
   CALCULATE CUSTOMER'S PERSONAL ORDER NUMBER
========================================================= */

/*
 * The database ID is still the real internal order ID.
 *
 * We count how many orders this customer has placed
 * up to this order ID.
 *
 * Example:
 *
 * Database IDs:
 * 7  = customer's first order
 * 12 = customer's second order
 * 18 = customer's third order
 *
 * Customer sees:
 *
 * My Order #1
 * My Order #2
 * My Order #3
 */

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS customer_order_number
     FROM orders
     WHERE email = ?
     AND id <= ?"
);

$stmt->bind_param(
    "si",
    $orderEmail,
    $orderId
);

$stmt->execute();

$result = $stmt->get_result();

$orderNumberData = $result->fetch_assoc();

$stmt->close();


$myOrderNumber =
    intval(
        $orderNumberData["customer_order_number"]
    );


/*
 * Remove the order ID from the session after reading it.
 * This prevents the success page from being reused accidentally.
 */
unset($_SESSION["last_order_id"]);

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Order Successful - Nexus Gear</title>


    <style>

        * {

            box-sizing: border-box;

        }


        body {

            margin: 0;

            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            font-family: Arial, sans-serif;

            background: #0b0b14;

            color: white;

        }


        .success-box {

            width: 90%;

            max-width: 550px;

            text-align: center;

            background: #151526;

            border: 1px solid #292945;

            border-radius: 15px;

            padding: 45px 30px;

            box-shadow:
                0 0 30px
                rgba(140, 80, 255, 0.15);

        }


        .robot {

            font-size: 75px;

            margin-bottom: 15px;

        }


        h1 {

            color: #b78cff;

            margin-bottom: 15px;

        }


        .message {

            color: #ccccd8;

            font-size: 17px;

            line-height: 1.6;

        }


        .order-id {

            display: inline-block;

            margin: 20px 0;

            padding: 12px 20px;

            background: #0d0d19;

            border: 1px solid #393955;

            border-radius: 8px;

            color: #b78cff;

            font-size: 20px;

            font-weight: bold;

        }


        .buttons {

            margin-top: 25px;

        }


        .button {

            display: inline-block;

            padding: 13px 22px;

            margin: 5px;

            border-radius: 8px;

            text-decoration: none;

            font-weight: bold;

        }


        .shop-button {

            background: #8f5cff;

            color: white;

        }


        .shop-button:hover {

            background: #7440df;

        }


        .orders-button {

            background: #25253b;

            color: white;

            border: 1px solid #393955;

        }


        .orders-button:hover {

            background: #30304a;

        }


    </style>

</head>


<body>


    <div class="success-box">


        <div class="robot">

            🤖

        </div>


        <h1>

            Order Placed Successfully!

        </h1>


        <p class="message">

            Thank you for shopping with Nexus Gear! 🎉

        </p>


        <p class="message">

            Your order has been successfully placed.

        </p>


        <div class="order-id">

            My Order #

            <?php

            echo $myOrderNumber;

            ?>

        </div>


        <p class="message">

            Your order is currently being processed.

        </p>


        <div class="buttons">


            <a
                href="index.php"
                class="button shop-button"
            >

                Continue Shopping

            </a>


            <a
                href="customer-dashboard.php"
                class="button orders-button"
            >

                My Dashboard

            </a>


        </div>


    </div>



    <script>

        /*
         * Order has been successfully created.
         * Clear the shopping cart.
         */

        localStorage.removeItem("nexusCart");

    </script>


</body>

</html>