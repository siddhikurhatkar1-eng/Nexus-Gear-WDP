<?php

require_once "config/database.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

/* User must be logged in */
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userName = $_SESSION["user_name"] ?? "";
$userEmail = $_SESSION["user_email"] ?? "";

$error = "";
$success = "";


/* =========================
   PLACE ORDER
   ========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $customerName = trim($_POST["customer_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $paymentMethod = trim($_POST["payment_method"] ?? "");
    $cartData = $_POST["cart_data"] ?? "";

    /* Basic validation */

    if ($customerName === "" || $email === "" || $phone === "" || $address === "") {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($paymentMethod === "") {
        $error = "Please select a payment method.";
    } elseif ($cartData === "") {
        $error = "Your cart is empty.";
    } else {

        $cart = json_decode($cartData, true);

        if (!is_array($cart) || count($cart) === 0) {

            $error = "Your cart is empty.";

        } else {

            try {

                /*
                 * Calculate the total using prices from the DATABASE.
                 * This prevents users from changing product prices
                 * using browser/localStorage.
                 */

                $totalAmount = 0;
                $products = [];

                foreach ($cart as $item) {

                    $productId = intval($item["id"] ?? 0);
                    $quantity = intval($item["quantity"] ?? 0);

                    if ($productId <= 0 || $quantity <= 0) {
                        throw new Exception("Invalid product information in cart.");
                    }

                    /*
                     * Get product information
                     */
                    $stmt = $conn->prepare(
                        "SELECT id, name, price, stock
                         FROM products
                         WHERE id = ?"
                    );

                    $stmt->bind_param("i", $productId);
                    $stmt->execute();

                    $result = $stmt->get_result();

                    if ($result->num_rows === 0) {
                        throw new Exception("One of the products in your cart no longer exists.");
                    }

                    $product = $result->fetch_assoc();

                    $stmt->close();

                    /*
                     * Check stock
                     */
                    if ($quantity > intval($product["stock"])) {
                        throw new Exception(
                            "Not enough stock for " . $product["name"] .
                            ". Available stock: " . $product["stock"]
                        );
                    }

                    /*
                     * Calculate price from database
                     */
                    $itemTotal = floatval($product["price"]) * $quantity;

                    $totalAmount += $itemTotal;

                    $products[] = [
                        "id" => $productId,
                        "quantity" => $quantity,
                        "price" => floatval($product["price"])
                    ];
                }


                /*
                 * Start database transaction
                 */
                $conn->begin_transaction();


                /* =========================
                   INSERT INTO ORDERS
                   ========================= */

                $stmt = $conn->prepare(
                    "INSERT INTO orders
                    (
                        customer_name,
                        email,
                        phone,
                        address,
                        payment_method,
                        total_amount
                    )
                    VALUES (?, ?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "sssssd",
                    $customerName,
                    $email,
                    $phone,
                    $address,
                    $paymentMethod,
                    $totalAmount
                );

                $stmt->execute();

                $orderId = $conn->insert_id;

                $stmt->close();


                /* =========================
                   INSERT ORDER ITEMS
                   ========================= */

                foreach ($products as $product) {

                    $productId = $product["id"];
                    $quantity = $product["quantity"];
                    $price = $product["price"];

                    $stmt = $conn->prepare(
                        "INSERT INTO order_items
                        (
                            order_id,
                            product_id,
                            quantity,
                            price
                        )
                        VALUES (?, ?, ?, ?)"
                    );

                    $stmt->bind_param(
                        "iiid",
                        $orderId,
                        $productId,
                        $quantity,
                        $price
                    );

                    $stmt->execute();

                    $stmt->close();


                    /* =========================
                       REDUCE PRODUCT STOCK
                       ========================= */

                    $stmt = $conn->prepare(
                        "UPDATE products
                         SET stock = stock - ?
                         WHERE id = ?
                         AND stock >= ?"
                    );

                    $stmt->bind_param(
                        "iii",
                        $quantity,
                        $productId,
                        $quantity
                    );

                    $stmt->execute();

                    if ($stmt->affected_rows === 0) {
                        throw new Exception(
                            "Unable to update stock for product ID " . $productId
                        );
                    }

                    $stmt->close();
                }


                /*
                 * Everything worked.
                 * Save changes permanently.
                 */
                $conn->commit();

                $_SESSION["last_order_id"] = $orderId;

                /*
                 * Go to order success page
                 */
                header("Location: order-success.php");
                exit;


            } catch (Exception $e) {

                /*
                 * Undo database changes if something failed.
                 */
                if ($conn->thread_id) {
                    try {
                        $conn->rollback();
                    } catch (Exception $rollbackError) {
                        // Ignore rollback error
                    }
                }

                /*
                 * Show the actual error so we can fix it.
                 */
                $error = "Order could not be placed: " . $e->getMessage();
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checkout - Nexus Gear</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0b0b14;
            color: white;
        }

        .header {
            background: #11111f;
            padding: 20px 50px;
            border-bottom: 1px solid #292945;
        }

        .logo {
            font-size: 26px;
            font-weight: bold;
            color: #9d6cff;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
        }

        h1 {
            margin-bottom: 30px;
            color: #ffffff;
        }

        .checkout-box {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }

        .form-box,
        .summary-box {
            background: #151526;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #292945;
        }

        h2 {
            margin-top: 0;
            color: #b78cff;
        }

        label {
            display: block;
            margin-top: 18px;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border-radius: 7px;
            border: 1px solid #393955;
            background: #0d0d19;
            color: white;
            font-size: 15px;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #9d6cff;
        }

        .place-order {
            width: 100%;
            margin-top: 25px;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #8f5cff;
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
        }

        .place-order:hover {
            background: #7440df;
        }

        .error {
            background: #3a1515;
            border: 1px solid #ff5555;
            color: #ff8d8d;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #292945;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 21px;
            font-weight: bold;
            color: #b78cff;
        }

        .back-cart {
            display: inline-block;
            margin-top: 20px;
            color: #b78cff;
            text-decoration: none;
        }

        @media (max-width: 800px) {

            .checkout-box {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

    <div class="header">
        <div class="logo">🤖 Nexus Gear</div>
    </div>


    <div class="container">

        <h1>Checkout</h1>


        <?php if ($error !== ""): ?>

            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <div class="checkout-box">


            <!-- =========================
                 CUSTOMER INFORMATION
                 ========================= -->

            <div class="form-box">

                <h2>Customer Information</h2>

                <form method="POST" action="checkout.php" id="checkoutForm">

                    <label for="customer_name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="customer_name"
                        name="customer_name"
                        value="<?php echo htmlspecialchars($userName); ?>"
                        required
                    >


                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($userEmail); ?>"
                        required
                    >


                    <label for="phone">
                        Phone Number
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        placeholder="Enter your phone number"
                        required
                    >


                    <label for="address">
                        Delivery Address
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        placeholder="Enter your complete delivery address"
                        required
                    ></textarea>


                    <label for="payment_method">
                        Payment Method
                    </label>

                    <select
                        id="payment_method"
                        name="payment_method"
                        required
                    >

                        <option value="">Select Payment Method</option>

                        <option value="Cash on Delivery">
                            Cash on Delivery
                        </option>

                        <option value="UPI">
                            UPI
                        </option>

                        <option value="Card">
                            Credit / Debit Card
                        </option>

                    </select>


                    <!-- Cart data will be inserted by JavaScript -->
                    <input
                        type="hidden"
                        name="cart_data"
                        id="cart_data"
                    >


                    <button
                        type="submit"
                        class="place-order"
                        id="placeOrderButton"
                    >
                        Place Order
                    </button>

                </form>


                <a href="cart.php" class="back-cart">
                    ← Back to Cart
                </a>

            </div>



            <!-- =========================
                 ORDER SUMMARY
                 ========================= -->

            <div class="summary-box">

                <h2>Order Summary</h2>

                <div id="orderSummary">
                    Loading cart...
                </div>

                <div class="summary-total">

                    <span>Total</span>

                    <span id="totalAmount">
                        ₹0.00
                    </span>

                </div>

            </div>

        </div>

    </div>



    <script>

        /*
         * Get cart from localStorage
         */
        let cart = JSON.parse(localStorage.getItem("nexusCart")) || [];


        const summary = document.getElementById("orderSummary");
        const totalAmount = document.getElementById("totalAmount");
        const cartData = document.getElementById("cart_data");
        const checkoutForm = document.getElementById("checkoutForm");


        /*
         * If cart is empty
         */
        if (cart.length === 0) {

            summary.innerHTML = "<p>Your cart is empty.</p>";

            document.getElementById("placeOrderButton").disabled = true;

        } else {

            let total = 0;
            let html = "";


            cart.forEach(function(item) {

                let quantity = Number(item.quantity) || 1;
                let price = Number(item.price) || 0;

                let itemTotal = price * quantity;

                total += itemTotal;


                html += `
                    <div class="summary-item">

                        <span>
                            ${item.name} × ${quantity}
                        </span>

                        <span>
                            ₹${itemTotal.toFixed(2)}
                        </span>

                    </div>
                `;

            });


            summary.innerHTML = html;

            totalAmount.textContent = "₹" + total.toFixed(2);

        }


        /*
         * Put cart into hidden form field before submitting.
         */
        checkoutForm.addEventListener("submit", function(event) {

            if (cart.length === 0) {

                event.preventDefault();

                alert("Your cart is empty.");

                return;

            }


            cartData.value = JSON.stringify(cart);

        });

    </script>

</body>

</html>