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


$message = "";


/* =========================================================
   UPDATE ORDER STATUS
========================================================= */

if (isset($_POST["update_status"])) {

    $order_id = intval($_POST["order_id"]);
    $status = $_POST["status"];

    $allowed_statuses = [
        "Pending",
        "Processing",
        "Shipped",
        "Delivered",
        "Cancelled"
    ];

    if (in_array($status, $allowed_statuses)) {

        $stmt = $conn->prepare(
            "UPDATE orders SET status = ? WHERE id = ?"
        );

        $stmt->bind_param(
            "si",
            $status,
            $order_id
        );

        if ($stmt->execute()) {

            $message = "Order status updated successfully.";

        } else {

            $message = "Could not update order status.";

        }

        $stmt->close();

    }

}


/* =========================================================
   GET ALL ORDERS
========================================================= */

$orders = $conn->query(
    "SELECT *
     FROM orders
     ORDER BY order_date DESC"
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

    <title>Manage Orders | Nexus Gear</title>


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


        .message {

            background: #18251c;

            border: 1px solid #3b8c4a;

            padding: 15px;

            border-radius: 8px;

            margin-bottom: 25px;

        }


        .order-card {

            background: #111827;

            border: 1px solid #263247;

            border-radius: 14px;

            padding: 25px;

            margin-bottom: 25px;

        }


        .order-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            border-bottom: 1px solid #293449;

            padding-bottom: 18px;

            margin-bottom: 20px;

        }


        .order-header h2 {

            margin: 0 0 8px 0;

            color: #c084fc;

        }


        .order-date {

            color: #a7a7b5;

        }


        .customer-info {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 10px;

            margin-bottom: 20px;

        }


        .info-box {

            background: #080c17;

            border-radius: 8px;

            padding: 14px;

        }


        .info-box strong {

            color: #c084fc;

        }


        .products-title {

            margin-bottom: 10px;

        }


        table {

            width: 100%;

            border-collapse: collapse;

            margin-bottom: 20px;

        }


        th,
        td {

            padding: 12px;

            text-align: left;

            border-bottom: 1px solid #293449;

        }


        th {

            color: #c084fc;

        }


        .total {

            font-size: 20px;

            font-weight: bold;

            color: #ffffff;

            margin: 15px 0;

        }


        .status-form {

            display: flex;

            align-items: center;

            gap: 10px;

            flex-wrap: wrap;

        }


        .status-form label {

            font-weight: bold;

        }


        select {

            padding: 10px;

            border-radius: 7px;

            border: 1px solid #374151;

            background: #080c17;

            color: white;

        }


        .update-button {

            padding: 10px 18px;

            border: none;

            border-radius: 7px;

            background: #7c2be8;

            color: white;

            font-weight: bold;

            cursor: pointer;

        }


        .status {

            display: inline-block;

            padding: 6px 12px;

            border-radius: 20px;

            background: #3b1c72;

            color: #c4b5fd;

            font-weight: bold;

        }


        .no-orders {

            background: #111827;

            border: 1px solid #263247;

            padding: 30px;

            border-radius: 12px;

            text-align: center;

            color: #a7a7b5;

        }


        @media (max-width: 700px) {

            .top-bar {

                flex-direction: column;

                align-items: flex-start;

                gap: 15px;

            }


            .customer-info {

                grid-template-columns: 1fr;

            }


            .order-header {

                flex-direction: column;

                align-items: flex-start;

            }


            table {

                min-width: 600px;

            }


            .order-card {

                overflow-x: auto;

            }

        }

    </style>

</head>


<body>


<div class="container">


    <!-- =====================================================
         PAGE HEADER
    ====================================================== -->

    <div class="top-bar">

        <h1>📦 Manage Orders</h1>


        <a
            href="admin-dashboard.php"
            class="back-button"
        >

            ← Admin Dashboard

        </a>

    </div>



    <!-- =====================================================
         MESSAGE
    ====================================================== -->

    <?php if ($message != ""): ?>

        <div class="message">

            <?php

            echo htmlspecialchars($message);

            ?>

        </div>

    <?php endif; ?>



    <!-- =====================================================
         ORDERS
    ====================================================== -->

    <?php if ($orders->num_rows > 0): ?>


        <?php while ($order = $orders->fetch_assoc()): ?>


            <div class="order-card">


                <!-- ORDER HEADER -->

                <div class="order-header">

                    <div>

                        <h2>

                            Order #<?php

                            echo $order["id"];

                            ?>

                        </h2>


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



                <!-- CUSTOMER INFORMATION -->

                <div class="customer-info">


                    <div class="info-box">

                        <strong>Customer:</strong><br>

                        <?php

                        echo htmlspecialchars(
                            $order["customer_name"]
                        );

                        ?>

                    </div>


                    <div class="info-box">

                        <strong>Email:</strong><br>

                        <?php

                        echo htmlspecialchars(
                            $order["email"]
                        );

                        ?>

                    </div>


                    <div class="info-box">

                        <strong>Phone:</strong><br>

                        <?php

                        echo htmlspecialchars(
                            $order["phone"]
                        );

                        ?>

                    </div>


                    <div class="info-box">

                        <strong>Payment Method:</strong><br>

                        <?php

                        echo htmlspecialchars(
                            $order["payment_method"]
                        );

                        ?>

                    </div>


                    <div class="info-box">

                        <strong>Address:</strong><br>

                        <?php

                        echo nl2br(
                            htmlspecialchars(
                                $order["address"]
                            )
                        );

                        ?>

                    </div>


                </div>



                <!-- PRODUCTS -->

                <h3 class="products-title">

                    🛒 Products in this Order

                </h3>


                <table>

                    <thead>

                        <tr>

                            <th>Product</th>

                            <th>Quantity</th>

                            <th>Price</th>

                            <th>Subtotal</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    $order_id = $order["id"];


                    $items = $conn->prepare(

                        "SELECT
                            order_items.quantity,
                            order_items.price,
                            products.name

                         FROM order_items

                         INNER JOIN products
                         ON order_items.product_id = products.id

                         WHERE order_items.order_id = ?"

                    );


                    $items->bind_param(
                        "i",
                        $order_id
                    );


                    $items->execute();


                    $items_result =
                        $items->get_result();


                    ?>


                    <?php while (
                        $item =
                        $items_result->fetch_assoc()
                    ): ?>


                        <tr>

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $item["name"]
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                echo $item["quantity"];

                                ?>

                            </td>


                            <td>

                                ₹<?php

                                echo number_format(
                                    $item["price"],
                                    2
                                );

                                ?>

                            </td>


                            <td>

                                ₹<?php

                                $subtotal =
                                    $item["price"]
                                    *
                                    $item["quantity"];

                                echo number_format(
                                    $subtotal,
                                    2
                                );

                                ?>

                            </td>

                        </tr>


                    <?php endwhile; ?>


                    </tbody>

                </table>


                <?php

                $items->close();

                ?>



                <!-- TOTAL -->

                <div class="total">

                    Total Amount:

                    ₹<?php

                    echo number_format(
                        $order["total_amount"],
                        2
                    );

                    ?>

                </div>



                <!-- STATUS UPDATE -->

                <form
                    method="POST"
                    class="status-form"
                >

                    <input
                        type="hidden"
                        name="order_id"
                        value="<?php

                        echo $order["id"];

                        ?>"
                    >


                    <label>

                        Update Status:

                    </label>


                    <select name="status">


                        <option
                            value="Pending"
                            <?php

                            if (
                                $order["status"]
                                === "Pending"
                            ) {
                                echo "selected";
                            }

                            ?>
                        >

                            Pending

                        </option>


                        <option
                            value="Processing"
                            <?php

                            if (
                                $order["status"]
                                === "Processing"
                            ) {
                                echo "selected";
                            }

                            ?>
                        >

                            Processing

                        </option>


                        <option
                            value="Shipped"
                            <?php

                            if (
                                $order["status"]
                                === "Shipped"
                            ) {
                                echo "selected";
                            }

                            ?>
                        >

                            Shipped

                        </option>


                        <option
                            value="Delivered"
                            <?php

                            if (
                                $order["status"]
                                === "Delivered"
                            ) {
                                echo "selected";
                            }

                            ?>
                        >

                            Delivered

                        </option>


                        <option
                            value="Cancelled"
                            <?php

                            if (
                                $order["status"]
                                === "Cancelled"
                            ) {
                                echo "selected";
                            }

                            ?>
                        >

                            Cancelled

                        </option>


                    </select>


                    <button
                        type="submit"
                        name="update_status"
                        class="update-button"
                    >

                        Update Status

                    </button>

                </form>


            </div>


        <?php endwhile; ?>


    <?php else: ?>


        <div class="no-orders">

            <h2>📦 No Orders Yet</h2>

            <p>

                Customer orders will appear here
                when they are placed.

            </p>

        </div>


    <?php endif; ?>


</div>


</body>

</html>