<?php

require_once "config/database.php";

/*
|--------------------------------------------------------------------------
| Get products from database
|--------------------------------------------------------------------------
*/

$sql = "SELECT * FROM products ORDER BY id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Nexus Gear | Gaming Store</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body>


<!-- =========================================================
     TOP NAVIGATION
========================================================= -->

<header class="navbar">

    <div class="logo">
        NEXUS<span>GEAR</span>
    </div>


    <nav>

        <a href="index.php">
            Home
        </a>

        <a href="#products">
            Products
        </a>

        <a href="#categories">
            Categories
        </a>

        <a href="#offers">
            Deals
        </a>

        <a href="#contact">
            Contact
        </a>

    </nav>


    <!-- CART -->

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
     HERO
========================================================= -->

<section class="hero">

    <div class="hero-content">

        <p class="small-title">
            NEXT GENERATION GAMING
        </p>


        <h1>

            LEVEL UP

            <br>

            <span>
                YOUR GEAR.
            </span>

        </h1>


        <p>

            Premium gaming hardware,
            accessories and gear for
            players who never settle.

        </p>


        <a
            href="#products"
            class="hero-button"
        >

            EXPLORE GEAR →

        </a>

    </div>

</section>



<!-- =========================================================
     STORE FEATURES
========================================================= -->

<section class="features">


    <!-- FREE SHIPPING -->

    <div class="feature-box">

        <span class="feature-icon">
            🚚
        </span>

        <div>

            <strong>
                FREE SHIPPING
            </strong>

            <small>
                On orders over ₹1,500
            </small>

        </div>

    </div>



    <!-- EASY RETURNS -->

    <div class="feature-box">

        <span class="feature-icon">
            ↩️
        </span>

        <div>

            <strong>
                EASY RETURNS
            </strong>

            <small>
                7-day return policy
            </small>

        </div>

    </div>



    <!-- SECURE PAYMENTS -->

    <div class="feature-box">

        <span class="feature-icon">
            🔒
        </span>

        <div>

            <strong>
                SECURE PAYMENTS
            </strong>

            <small>
                100% secure checkout
            </small>

        </div>

    </div>



    <!-- GAMER SUPPORT -->

    <div class="feature-box">

        <span class="feature-icon">
            🎧
        </span>

        <div>

            <strong>
                GAMER SUPPORT
            </strong>

            <small>
                Customer support available
            </small>

        </div>

    </div>


</section>



<!-- =========================================================
     CATEGORIES
========================================================= -->

<section
    class="section"
    id="categories"
>


    <div class="section-heading">

        <div>

            <p class="section-label">
                EXPLORE
            </p>

            <h2>
                SHOP BY CATEGORY
            </h2>

        </div>


        <a
            href="category.php?id=0"
            class="view-text"
        >

            View All →

        </a>

    </div>



    <div class="category-grid">


        <!-- ALL -->

        <a
            href="category.php?id=0"
            class="category-card"
        >

            <span class="category-icon">
                🎮
            </span>

            <span>
                All Gaming Gear
            </span>

        </a>



        <!-- KEYBOARDS -->

        <a
            href="category.php?id=3"
            class="category-card"
        >

            <span class="category-icon">
                ⌨️
            </span>

            <span>
                Keyboards
            </span>

        </a>



        <!-- MICE -->

        <a
            href="category.php?id=4"
            class="category-card"
        >

            <span class="category-icon">
                🖱️
            </span>

            <span>
                Gaming Mice
            </span>

        </a>



        <!-- HEADSETS -->

        <a
            href="category.php?id=5"
            class="category-card"
        >

            <span class="category-icon">
                🎧
            </span>

            <span>
                Headsets
            </span>

        </a>



        <!-- MONITORS -->

        <a
            href="category.php?id=8"
            class="category-card"
        >

            <span class="category-icon">
                🖥️
            </span>

            <span>
                Monitors
            </span>

        </a>



        <!-- GAMING CHAIRS -->

        <a
            href="category.php?id=6"
            class="category-card"
        >

            <span class="category-icon">
                💺
            </span>

            <span>
                Gaming Chairs
            </span>

        </a>



        <!-- STREAMING -->

        <a
            href="category.php?id=9"
            class="category-card"
        >

            <span class="category-icon">
                🎙️
            </span>

            <span>
                Streaming Gear
            </span>

        </a>



        <!-- ACCESSORIES -->

        <a
            href="category.php?id=10"
            class="category-card"
        >

            <span class="category-icon">
                ⚡
            </span>

            <span>
                Accessories
            </span>

        </a>


    </div>

</section>



<!-- =========================================================
     FEATURED PRODUCTS
========================================================= -->

<section
    class="section"
    id="products"
>


    <!-- =====================================================
         PRODUCT SEARCH
    ====================================================== -->

    <div
        class="product-search"
        style="
            width: 100%;
            margin-bottom: 30px;
        "
    >

        <input
            type="text"
            id="product-search"
            placeholder="🔍 Search gaming products..."
            autocomplete="off"
            style="
                width: 100%;
                box-sizing: border-box;
                padding: 16px 20px;
                background: #0b1020;
                border: 1px solid #343b5c;
                border-radius: 12px;
                color: white;
                font-size: 16px;
                outline: none;
            "
        >

    </div>



    <div class="section-heading">

        <div>

            <p class="section-label">
                OUR COLLECTION
            </p>

            <h2>
                FEATURED HARDWARE
            </h2>

        </div>


        <span class="view-text">

            <?php

            if ($result) {

                echo $result->num_rows;

            }

            ?>

            PRODUCTS

        </span>

    </div>



    <!-- =====================================================
         PRODUCT GRID
    ====================================================== -->

    <div class="product-grid">


        <?php if ($result && $result->num_rows > 0): ?>


            <?php while ($product = $result->fetch_assoc()): ?>


                <!-- =================================================
                     PRODUCT CARD
                ================================================= -->

                <div
                    class="product-card"

                    data-category="<?php
                        echo htmlspecialchars(
                            $product['category_id']
                        );
                    ?>"

                    data-id="<?php
                        echo htmlspecialchars(
                            $product['id']
                        );
                    ?>"

                    data-name="<?php
                        echo htmlspecialchars(
                            $product['name']
                        );
                    ?>"

                    data-price="<?php
                        echo htmlspecialchars(
                            $product['price']
                        );
                    ?>"

                    data-image="<?php
                        echo htmlspecialchars(
                            $product['image']
                        );
                    ?>"

                    data-stock="<?php
                        echo htmlspecialchars(
                            $product['stock']
                        );
                    ?>"

                    onclick="window.location.href='product.php?id=<?php
                        echo $product['id'];
                    ?>'"

                    style="cursor: pointer;"
                >


                    <!-- =================================================
                         PRODUCT IMAGE
                    ================================================= -->

                    <div class="product-image">


                        <img

                            src="images/<?php
                                echo htmlspecialchars(
                                    $product['image']
                                );
                            ?>"

                            alt="<?php
                                echo htmlspecialchars(
                                    $product['name']
                                );
                            ?>"

                            class="product-real-image"

                            onerror="
                                this.style.display='none';
                                this.nextElementSibling.style.display='flex';
                            "

                        >


                        <!-- FALLBACK -->

                        <div class="product-placeholder">

                            🎮

                        </div>


                        <!-- DEAL BADGE -->

                        <?php if (!empty($product['old_price'])): ?>

                            <span class="product-badge">

                                DEAL

                            </span>

                        <?php endif; ?>


                    </div>



                    <!-- =================================================
                         PRODUCT INFORMATION
                    ================================================= -->

                    <div class="product-info">


                        <p class="product-category">

                            NEXUS GEAR

                        </p>



                        <h3>

                            <?php

                            echo htmlspecialchars(
                                $product['name']
                            );

                            ?>

                        </h3>



                        <p class="description">

                            <?php

                            echo htmlspecialchars(
                                $product['description']
                            );

                            ?>

                        </p>



                        <!-- RATING -->

                        <div class="rating">

                            ⭐

                            <?php

                            echo htmlspecialchars(
                                $product['rating']
                            );

                            ?>

                        </div>



                        <!-- =================================================
                             STOCK INFORMATION
                        ================================================= -->

                        <?php if ($product['stock'] > 0): ?>

                            <p class="stock-info">

                                In Stock:
                                <?php
                                echo htmlspecialchars(
                                    $product['stock']
                                );
                                ?>

                            </p>

                        <?php else: ?>

                            <p class="stock-info out-of-stock">

                                ❌ Out of Stock

                            </p>

                        <?php endif; ?>



                        <!-- =================================================
                             PRICE
                        ================================================= -->

                        <div class="price-row">


                            <div>


                                <span class="price">

                                    ₹<?php

                                    echo number_format(
                                        $product['price'],
                                        2
                                    );

                                    ?>

                                </span>



                                <?php if (!empty($product['old_price'])): ?>

                                    <span class="old-price">

                                        ₹<?php

                                        echo number_format(
                                            $product['old_price'],
                                            2
                                        );

                                        ?>

                                    </span>

                                <?php endif; ?>


                            </div>



                            <!-- =================================================
                                 ADD TO CART
                            ================================================= -->

                            <?php if ($product['stock'] > 0): ?>


                                <button

                                    class="add-button"

                                    onclick="
                                        event.stopPropagation();
                                        addToCart(
                                            <?php
                                            echo $product['id'];
                                            ?>
                                        );
                                    "

                                >

                                    +

                                </button>


                            <?php else: ?>


                                <button

                                    class="add-button"

                                    disabled

                                    onclick="
                                        event.stopPropagation();
                                    "

                                    title="Out of Stock"

                                    style="
                                        cursor: not-allowed;
                                        opacity: 0.5;
                                    "

                                >

                                    ×

                                </button>


                            <?php endif; ?>


                        </div>


                    </div>


                </div>


            <?php endwhile; ?>


        <?php else: ?>


            <div class="empty-products">

                <h3>
                    No products found.
                </h3>

                <p>
                    Please add products to the database.
                </p>

            </div>


        <?php endif; ?>


    </div>

</section>



<!-- =========================================================
     DEALS / OFFERS
========================================================= -->

<section
    class="offers"
    id="offers"
>


    <div class="offer-content">


        <p class="section-label">
            LIMITED TIME
        </p>


        <h2>
            GEAR UP & SAVE
        </h2>


        <p>

            Grab selected gaming hardware
            before the deals disappear.

        </p>


        <a
            href="#products"
            class="offer-button"
        >

            VIEW DEALS →

        </a>


    </div>


    <div class="offer-visual">

    </div>


</section>



<!-- =========================================================
     CONTACT / CUSTOMER SUPPORT
========================================================= -->

<section
    class="section contact-section"
    id="contact"
>


    <div class="section-heading">

        <div>

            <p class="section-label">
                CUSTOMER SUPPORT
            </p>

            <h2>
                HOW CAN WE HELP?
            </h2>

        </div>

    </div>



    <div class="contact-grid">


        <!-- EMAIL -->

        <div class="contact-card">

            <div class="contact-icon">
                📧
            </div>

            <h3>
                Email Support
            </h3>

            <p>
                Send us your questions
                anytime.
            </p>

            <a
                href="mailto:support@nexusgear.com"
            >

                support@nexusgear.com

            </a>

        </div>



        <!-- PHONE -->

        <div class="contact-card">

            <div class="contact-icon">
                📞
            </div>

            <h3>
                Customer Care
            </h3>

            <p>
                Need quick assistance?
                Call our support team.
            </p>

            <a
                href="tel:+911800123456"
            >

                +91 1800-123-4567

            </a>

            <small>
                Demo project support number
            </small>

        </div>



        <!-- INSTAGRAM -->

        <div class="contact-card">

            <div class="contact-icon">
                📸
            </div>

            <h3>
                Instagram
            </h3>

            <p>
                Follow Nexus Gear
                for gaming updates.
            </p>

            <a
                href="https://www.instagram.com/"
                target="_blank"
                rel="noopener noreferrer"
            >

                @NexusGear

            </a>

        </div>



        <!-- FACEBOOK -->

        <div class="contact-card">

            <div class="contact-icon">
                📘
            </div>

            <h3>
                Facebook
            </h3>

            <p>
                Join our gaming
                community.
            </p>

            <a
                href="https://www.facebook.com/"
                target="_blank"
                rel="noopener noreferrer"
            >

                Nexus Gear

            </a>

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


    <!-- CART HEADER -->

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



    <!-- CART PRODUCTS -->

    <div id="cart-items">

        <p class="empty-cart">

            Your cart is empty.

        </p>

    </div>



    <!-- =====================================================
         CART FOOTER
    ====================================================== -->

    <div class="cart-footer">


        <!-- FREE SHIPPING MESSAGE -->

        <div
            id="shipping-message"
            class="shipping-message"
        >

            🚚 Add ₹1500.00 more to unlock FREE SHIPPING

        </div>



        <!-- CART TOTAL -->

        <div class="cart-total">


            <span>
                Total
            </span>


            <strong id="cart-total">

                ₹0

            </strong>


        </div>



        <!-- CHECKOUT BUTTON -->

        <button
            class="checkout-button"
            onclick="checkout()"
        >

            PROCEED TO CHECKOUT

        </button>


    </div>


</div>



<!-- =========================================================
     NEXI ROBOT
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
     FOOTER
========================================================= -->

<footer>


    <div class="footer-content">


        <!-- BRAND -->

        <div>


            <div class="logo">

                NEXUS<span>GEAR</span>

            </div>


            <p>

                Gaming hardware for
                the next generation.

            </p>


        </div>



        <!-- QUICK LINKS -->

        <div>

            <h3>
                Quick Links
            </h3>

            <a href="#products">
                Products
            </a>

            <a href="#categories">
                Categories
            </a>

            <a href="#offers">
                Deals
            </a>

        </div>



        <!-- SUPPORT -->

        <div>

            <h3>
                Support
            </h3>

            <a href="#contact">
                Contact Us
            </a>

            <a href="mailto:support@nexusgear.com">
                Email Support
            </a>

            <a href="tel:+911800123456">
                Customer Care
            </a>

        </div>


    </div>



    <div class="footer-bottom">

        <p>

            © 2026 Nexus Gear.
            Built for gamers.

        </p>

    </div>


</footer>



<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script src="js/script.js"></script>


</body>

</html>