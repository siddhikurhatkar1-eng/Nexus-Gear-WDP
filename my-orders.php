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
   GET CURRENT USER
========================================================= */

$user_id = $_SESSION["user_id"];


$stmt = $conn->prepare(
    "SELECT id, name, email
     FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $user_id);

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


$userEmail = $user["email"];


/* =========================================================
   GET CUSTOMER ORDERS
========================================================= */

$stmt = $conn->prepare(
    "SELECT
        id,
        customer_name,
        email,
        phone,
        address,
        payment_method,
        total_amount,
        order_date,
        status
     FROM orders
     WHERE email = ?
     ORDER BY order_date DESC"
);

$stmt->bind_param("s", $userEmail);

$stmt->execute();

$ordersResult = $stmt->get_result();

$orders = [];


while ($order = $ordersResult->fetch_assoc()) {

    $orders[] = $order;

}


$stmt->close();


/* =========================================================
   GET PRODUCTS FOR EACH ORDER
========================================================= */

foreach ($orders as &$order) {

    $orderId = $order["id"];


    $stmt = $conn->prepare(
        "SELECT
            oi.product_id,
            oi.quantity,
            oi.price,
            p.name AS product_name
         FROM order_items oi
         LEFT JOIN products p
         ON oi.product_id = p.id
         WHERE oi.order_id = ?
         ORDER BY oi.id ASC"
    );


    $stmt->bind_param(
        "i",
        $orderId
    );


    $stmt->execute();


    $itemsResult = $stmt->get_result();


    $order["items"] = [];


    while ($item = $itemsResult->fetch_assoc()) {

        $order["items"][] = $item;

    }


    $stmt->close();

}


unset($order);

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Orders | Nexus Gear</title>


    <link
        rel="stylesheet"
        href="css/style.css"
    >


    <style>

        /* =================================================
           PAGE
        ================================================= */

        .orders-page {

            min-height: 100vh;

            padding: 40px 20px;

        }


        .orders-container {

            max-width: 1100px;

            margin: 0 auto;

        }


        /* =================================================
           HEADER
        ================================================= */

        .orders-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            margin-bottom: 30px;

        }


        .orders-header h1 {

            margin-bottom: 8px;

        }


        .orders-header p {

            opacity: 0.7;

        }


        .header-buttons {

            display: flex;

            gap: 10px;

            flex-wrap: wrap;

        }


        /* =================================================
           BUTTON
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


        /* =================================================
           ORDER CARD
        ================================================= */

        .order-card {

            background: #111827;

            border: 1px solid #273449;

            border-radius: 16px;

            padding: 25px;

            margin-bottom: 25px;

        }


        /* =================================================
           ORDER HEADER
        ================================================= */

        .order-top {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            padding-bottom: 20px;

            border-bottom: 1px solid #273449;

        }


        .order-number {

            color: #c084fc;

            font-size: 24px;

            font-weight: 800;

        }


        .order-date {

            margin-top: 7px;

            opacity: 0.65;

        }


        /* =================================================
           STATUS
        ================================================= */

        .status {

            padding: 9px 16px;

            border-radius: 20px;

            background: #4c1d95;

            color: #ddd6fe;

            font-weight: 700;

        }


        /* =================================================
           ORDER INFORMATION
        ================================================= */

        .order-info {

            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 15px;

            margin-top: 20px;

        }


        .info-box {

            background: #080c16;

            border-radius: 10px;

            padding: 15px;

        }


        .info-label {

            display: block;

            color: #c084fc;

            font-weight: 700;

            margin-bottom: 5px;

        }


        .info-value {

            color: white;

        }


        /* =================================================
           PRODUCTS
        ================================================= */

        .products-section {

            margin-top: 25px;

            padding-top: 20px;

            border-top: 1px solid #273449;

        }


        .products-title {

            font-size: 20px;

            font-weight: 800;

            margin-bottom: 15px;

        }


        .product-row {

            display: grid;

            grid-template-columns: 2fr 1fr 1fr 1fr;

            gap: 15px;

            align-items: center;

            padding: 14px 0;

            border-bottom: 1px solid #273449;

        }


        .product-row:last-child {

            border-bottom: none;

        }


        .product-heading {

            color: #c084fc;

            font-weight: 700;

            font-size: 14px;

        }


        .product-name {

            font-weight: 600;

        }


        .product-price {

            color: #d1d5db;

        }


        .product-subtotal {

            color: #c084fc;

            font-weight: 700;

        }


        .no-products {

            opacity: 0.7;

            padding: 15px 0;

        }


        /* =================================================
           ORDER TOTAL
        ================================================= */

        .order-total {

            margin-top: 20px;

            padding-top: 20px;

            border-top: 1px solid #273449;

            display: flex;

            justify-content: space-between;

            align-items: center;

            font-size: 20px;

            font-weight: 800;

        }


        .total-price {

            color: #c084fc;

        }


        /* =================================================
           EMPTY ORDERS
        ================================================= */

        .empty-orders {

            background: #111827;

            border: 1px solid #273449;

            border-radius: 16px;

            padding: 50px 20px;

            text-align: center;

        }


        .empty-icon {

            font-size: 55px;

            margin-bottom: 15px;

        }


        /* =================================================
           MOBILE
        ================================================= */

        @media (max-width: 700px) {

            .orders-header {

                flex-direction: column;

                align-items: flex-start;

            }


            .order-top {

                flex-direction: column;

                align-items: flex-start;

            }


            .order-info {

                grid-template-columns: 1fr;

            }


            .product-row {

                grid-template-columns: 1fr;

                gap: 5px;

            }


            .product-heading {

                display: none;

            }

        }

    </style>

</head>


<body>


<div class="orders-page">


    <div class="orders-container">


        <!-- =================================================
             HEADER
        ================================================== -->

        <div class="orders-header">


            <div>

                <h1>

                    📦 My Orders

                </h1>


                <p>

                    Welcome,

                    <?php

                    echo htmlspecialchars(
                        $user["name"]
                    );

                    ?>!

                    Here you can view your orders.

                </p>

            </div>


            <div class="header-buttons">


                <a
                    href="customer-dashboard.php"
                    class="dashboard-button"
                >

                    ← My Dashboard

                </a>


                <a
                    href="index.php"
                    class="dashboard-button"
                >

                    Continue Shopping

                </a>

            </div>


        </div>



        <!-- =================================================
             DISPLAY ORDERS
        ================================================== -->

        <?php if (count($orders) > 0): ?>

            <?php
              $totalOrders = count($orders);
            ?> 

            <?php foreach ($orders as $index => $order): ?>

                <?php

                /*
                 * Orders are displayed newest first.
                 * The oldest order is the customer's #1 order.
                 */

                $myOrderNumber =
                    count($orders) - $index;

                ?>


                <div class="order-card">


                    <!-- =====================================
                         ORDER HEADER
                    ====================================== -->

                    <div class="order-top">


                        <div>


                            <div class="order-number">

                                My Order #

                                <?php

                                echo $myOrderNumber;

                                ?>

                            </div>


                            <div class="order-date">

                                <?php

                                echo date(
                                    "d M Y, h:i A",
                                    strtotime(
                                        $order["order_date"]
                                    )
                                );

                                ?>

                            </div>

                        </div>


                        <div class="status">

                            <?php

                            echo htmlspecialchars(
                                $order["status"]
                            );

                            ?>

                        </div>


                    </div>



                    <!-- =====================================
                         ORDER INFORMATION
                    ====================================== -->

                    <div class="order-info">


                        <!-- PAYMENT -->

                        <div class="info-box">

                            <span class="info-label">

                                Payment Method

                            </span>


                            <span class="info-value">

                                <?php

                                echo htmlspecialchars(
                                    $order["payment_method"]
                                );

                                ?>

                            </span>

                        </div>



                        <!-- PHONE -->

                        <div class="info-box">

                            <span class="info-label">

                                Phone

                            </span>


                            <span class="info-value">

                                <?php

                                echo htmlspecialchars(
                                    $order["phone"]
                                );

                                ?>

                            </span>

                        </div>



                        <!-- ADDRESS -->

                        <div class="info-box">

                            <span class="info-label">

                                Delivery Address

                            </span>


                            <span class="info-value">

                                <?php

                                echo nl2br(
                                    htmlspecialchars(
                                        $order["address"]
                                    )
                                );

                                ?>

                            </span>

                        </div>



                        <!-- CUSTOMER -->

                        <div class="info-box">

                            <span class="info-label">

                                Customer

                            </span>


                            <span class="info-value">

                                <?php

                                echo htmlspecialchars(
                                    $order["customer_name"]
                                );

                                ?>

                            </span>

                        </div>


                    </div>



                    <!-- =====================================
                         PRODUCTS
                    ====================================== -->

                    <div class="products-section">


                        <div class="products-title">

                            🛒 Products in this Order

                        </div>


                        <?php if (count($order["items"]) > 0): ?>


                            <!-- PRODUCT HEADINGS -->

                            <div class="product-row">


                                <div class="product-heading">

                                    Product

                                </div>


                                <div class="product-heading">

                                    Quantity

                                </div>


                                <div class="product-heading">

                                    Price

                                </div>


                                <div class="product-heading">

                                    Subtotal

                                </div>


                            </div>



                            <!-- PRODUCT ITEMS -->

                            <?php foreach ($order["items"] as $item): ?>


                                <?php

                                $quantity =
                                    intval(
                                        $item["quantity"]
                                    );

                                $price =
                                    floatval(
                                        $item["price"]
                                    );

                                $subtotal =
                                    $quantity * $price;

                                ?>


                                <div class="product-row">


                                    <div class="product-name">

                                        <?php

                                        echo htmlspecialchars(
                                            $item["product_name"]
                                                ?? "Product unavailable"
                                        );

                                        ?>

                                    </div>


                                    <div>

                                        <?php

                                        echo $quantity;

                                        ?>

                                    </div>


                                    <div class="product-price">

                                        ₹<?php

                                        echo number_format(
                                            $price,
                                            2
                                        );

                                        ?>

                                    </div>


                                    <div class="product-subtotal">

                                        ₹<?php

                                        echo number_format(
                                            $subtotal,
                                            2
                                        );

                                        ?>

                                    </div>


                                </div>


                            <?php endforeach; ?>


                        <?php else: ?>


                            <div class="no-products">

                                Product details are not available
                                for this order.

                            </div>


                        <?php endif; ?>


                    </div>



                    <!-- =====================================
                         ORDER TOTAL
                    ====================================== -->

                    <div class="order-total">


                        <span>

                            Order Total

                        </span>


                        <span class="total-price">

                            ₹<?php

                            echo number_format(
                                $order["total_amount"],
                                2
                            );

                            ?>

                        </span>


                    </div>


                </div>


            <?php endforeach; ?>


        <?php else: ?>


            <!-- =========================================
                 NO ORDERS
            ========================================== -->

            <div class="empty-orders">


                <div class="empty-icon">

                    🛒

                </div>


                <h2>

                    No Orders Yet

                </h2>


                <p>

                    You haven't placed any orders yet.

                </p>


                <br>


                <a
                    href="index.php#products"
                    class="dashboard-button"
                >

                    Start Shopping

                </a>


            </div>


        <?php endif; ?>


    </div>


</div>


</body>

</html>