<?php

require_once "config/database.php";


/* =========================================================
   GET PRODUCT ID
========================================================= */

$product_id = isset($_GET["id"])
    ? intval($_GET["id"])
    : 0;


/* =========================================================
   CHECK PRODUCT ID
========================================================= */

if ($product_id <= 0) {

    header("Location: index.php");
    exit;

}


/* =========================================================
   GET PRODUCT FROM DATABASE
========================================================= */

$stmt = $conn->prepare(
    "SELECT
        p.id,
        p.name,
        p.category_id,
        p.price,
        p.old_price,
        p.description,
        p.image,
        p.stock,
        p.rating,
        c.name AS category_name
     FROM products p
     LEFT JOIN categories c
     ON p.category_id = c.id
     WHERE p.id = ?"
);

$stmt->bind_param("i", $product_id);

$stmt->execute();

$result = $stmt->get_result();

$product = $result->fetch_assoc();

$stmt->close();


/* =========================================================
   PRODUCT NOT FOUND
========================================================= */

if (!$product) {

    header("Location: index.php");
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

    <title>

        <?php

        echo htmlspecialchars(
            $product["name"]
        );

        ?>

        | Nexus Gear

    </title>


    <link
        rel="stylesheet"
        href="css/style.css"
    >


    <style>

        /* =================================================
           PRODUCT PAGE
        ================================================= */

        .product-page {

            min-height: 100vh;

            padding: 50px 20px;

        }


        .product-container {

            max-width: 1100px;

            margin: 0 auto;

        }


        /* =================================================
           BACK BUTTON
        ================================================= */

        .back-link {

            display: inline-block;

            margin-bottom: 25px;

            color: #c084fc;

            text-decoration: none;

            font-weight: 700;

        }


        .back-link:hover {

            color: #a855f7;

        }


        /* =================================================
           PRODUCT DETAILS
        ================================================= */

        .product-details {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 45px;

            background: #111827;

            border: 1px solid #273449;

            border-radius: 18px;

            padding: 35px;

        }


        /* =================================================
           IMAGE
        ================================================= */

        .product-detail-image {

            min-height: 450px;

            background: #080c16;

            border-radius: 15px;

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 30px;

            position: relative;

        }


        .product-detail-image img {

            width: 100%;

            max-width: 450px;

            max-height: 430px;

            object-fit: contain;

        }


        .image-placeholder {

            display: none;

            font-size: 100px;

        }


        /* =================================================
           INFORMATION
        ================================================= */

        .product-detail-info {

            display: flex;

            flex-direction: column;

            justify-content: center;

        }


        .product-category-label {

            color: #c084fc;

            font-size: 14px;

            font-weight: 800;

            text-transform: uppercase;

            letter-spacing: 1px;

            margin-bottom: 10px;

        }


        .product-detail-info h1 {

            font-size: 38px;

            line-height: 1.2;

            margin-bottom: 15px;

        }


        /* =================================================
           RATING
        ================================================= */

        .product-rating {

            margin-bottom: 20px;

            font-size: 18px;

        }


        /* =================================================
           DESCRIPTION
        ================================================= */

        .product-description {

            color: #d1d5db;

            line-height: 1.7;

            font-size: 16px;

            margin-bottom: 25px;

        }


        /* =================================================
           PRICE
        ================================================= */

        .product-price {

            display: flex;

            align-items: center;

            gap: 15px;

            margin-bottom: 20px;

        }


        .current-price {

            color: #c084fc;

            font-size: 32px;

            font-weight: 800;

        }


        .old-price-detail {

            color: #737b8c;

            font-size: 18px;

            text-decoration: line-through;

        }


        /* =================================================
           STOCK
        ================================================= */

        .stock-status {

            margin-bottom: 25px;

            font-weight: 700;

        }


        .stock-available {

            color: #4ade80;

        }


        .stock-low {

            color: #facc15;

        }


        .stock-out {

            color: #f87171;

        }


        /* =================================================
           ADD TO CART
        ================================================= */

        .product-add-button {

            width: 100%;

            padding: 16px;

            border: none;

            border-radius: 10px;

            background: #8f5cff;

            color: white;

            font-size: 17px;

            font-weight: 800;

            cursor: pointer;

            transition: 0.2s;

        }


        .product-add-button:hover {

            background: #7440df;

            transform: translateY(-2px);

        }


        .product-add-button:disabled {

            background: #3b3b4f;

            color: #888;

            cursor: not-allowed;

            transform: none;

        }


        /* =================================================
           CART OVERLAY
        ================================================= */

        .cart-overlay {

            display: none;

        }


        /* =================================================
           MOBILE
        ================================================= */

        @media (max-width: 800px) {

            .product-details {

                grid-template-columns: 1fr;

                padding: 20px;

            }


            .product-detail-image {

                min-height: 300px;

            }


            .product-detail-info h1 {

                font-size: 30px;

            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     NAVIGATION
========================================================= -->

<header class="navbar">


    <div class="logo">

        NEXUS<span>GEAR</span>

    </div>


    <nav>

        <a href="index.php">
            Home
        </a>

        <a href="index.php#products">
            Products
        </a>

        <a href="index.php#categories">
            Categories
        </a>

        <a href="index.php#offers">
            Deals
        </a>

        <a href="index.php#contact">
            Contact
        </a>

    </nav>


    <button
        class="cart-button"
        onclick="openCart()"
    >

        🛒 Cart

        <span id="cart-count">
            0
        </span>

    </button>


</header>



<!-- =========================================================
     PRODUCT PAGE
========================================================= -->

<section class="product-page">


    <div class="product-container">


        <!-- BACK -->

        <a
            href="javascript:history.back()"
            class="back-link"
        >

            ← Back

        </a>



        <div class="product-details">


            <!-- =================================================
                 PRODUCT IMAGE
            ================================================== -->

            <div class="product-detail-image">


                <img

                    src="images/<?php

                        echo htmlspecialchars(
                            $product["image"]
                        );

                    ?>"

                    alt="<?php

                        echo htmlspecialchars(
                            $product["name"]
                        );

                    ?>"

                    onerror="
                        this.style.display='none';
                        this.nextElementSibling.style.display='block';
                    "

                >


                <div class="image-placeholder">

                    🎮

                </div>


            </div>



            <!-- =================================================
                 PRODUCT INFORMATION
            ================================================== -->

            <div class="product-detail-info">


                <div class="product-category-label">

                    <?php

                    echo htmlspecialchars(
                        $product["category_name"]
                            ?? "Gaming Gear"
                    );

                    ?>

                </div>



                <h1>

                    <?php

                    echo htmlspecialchars(
                        $product["name"]
                    );

                    ?>

                </h1>



                <!-- RATING -->

                <div class="product-rating">

                    ⭐

                    <?php

                    echo htmlspecialchars(
                        $product["rating"]
                    );

                    ?>

                    / 5

                </div>



                <!-- DESCRIPTION -->

                <p class="product-description">

                    <?php

                    echo htmlspecialchars(
                        $product["description"]
                    );

                    ?>

                </p>



                <!-- PRICE -->

                <div class="product-price">


                    <span class="current-price">

                        ₹<?php

                        echo number_format(
                            $product["price"],
                            2
                        );

                        ?>

                    </span>



                    <?php if (!empty($product["old_price"])): ?>

                        <span class="old-price-detail">

                            ₹<?php

                            echo number_format(
                                $product["old_price"],
                                2
                            );

                            ?>

                        </span>

                    <?php endif; ?>


                </div>



                <!-- STOCK -->

                <div class="stock-status">


                    <?php if ($product["stock"] <= 0): ?>


                        <span class="stock-out">

                            ❌ Out of Stock

                        </span>


                    <?php elseif ($product["stock"] <= 5): ?>


                        <span class="stock-low">

                            ⚠️ Only

                            <?php

                            echo intval(
                                $product["stock"]
                            );

                            ?>

                            left in stock

                        </span>


                    <?php else: ?>


                        <span class="stock-available">

                            ✅ In Stock

                        </span>


                    <?php endif; ?>


                </div>



                <!-- ADD TO CART -->

                <?php if ($product["stock"] > 0): ?>


                    <button

                        class="product-add-button"

                        onclick="
                            addToCart(
                                <?php
                                echo intval(
                                    $product["id"]
                                );
                                ?>
                            )
                        "

                    >

                        🛒 Add to Cart

                    </button>


                <?php else: ?>


                    <button
                        class="product-add-button"
                        disabled
                    >

                        Out of Stock

                    </button>


                <?php endif; ?>


            </div>


        </div>


    </div>


</section>



<!-- =========================================================
     CART OVERLAY
========================================================= -->

<div
    class="cart-overlay"
    id="cart-overlay"
    onclick="closeCart()"
></div>



<!-- =========================================================
     CART PANEL
========================================================= -->

<div
    class="cart-panel"
    id="cart-panel"
>


    <div class="cart-header">


        <h2>
            Your Cart
        </h2>


        <button
            onclick="closeCart()"
            class="close-cart"
        >

            ×

        </button>


    </div>



    <div id="cart-items">


        <p class="empty-cart">

            Your cart is empty.

        </p>


    </div>



    <div class="cart-footer">


        <div class="cart-total">

            <span>
                Total
            </span>

            <strong id="cart-total">
                ₹0
            </strong>

        </div>


        <button
            class="checkout-button"
            onclick="checkout()"
        >

            PROCEED TO CHECKOUT

        </button>


    </div>


</div>



<!-- =========================================================
     NEXI
========================================================= -->

<div
    class="robot-container"
    id="robot"
>


    <div
        class="robot-message"
        id="robot-message"
    >

        Welcome to Nexus Gear! 👋

    </div>


    <div class="robot">

        🤖

    </div>


</div>



<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script src="js/script.js"></script>


</body>

</html>