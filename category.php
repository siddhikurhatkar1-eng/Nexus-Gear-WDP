<?php

require_once "config/database.php";

/*
|--------------------------------------------------------------------------
| Get category ID from URL
|--------------------------------------------------------------------------
*/

$category_id = isset($_GET['id'])
    ? intval($_GET['id'])
    : 0;


/*
|--------------------------------------------------------------------------
| Category names
|--------------------------------------------------------------------------
*/

$category_names = [

    0 => "All Gaming Gear",

    3 => "Gaming Keyboards",

    4 => "Gaming Mice",

    5 => "Gaming Headsets",

    6 => "Gaming Chairs",

    8 => "Gaming Monitors",

    9 => "Streaming Gear",

    10 => "Gaming Accessories"

];


/*
|--------------------------------------------------------------------------
| Check category
|--------------------------------------------------------------------------
*/

if (!array_key_exists($category_id, $category_names)) {

    $category_id = 0;

}


$category_title =
    $category_names[$category_id];


/*
|--------------------------------------------------------------------------
| Get products
|--------------------------------------------------------------------------
*/

if ($category_id == 0) {

    $sql =
        "SELECT * FROM products ORDER BY id DESC";

} else {

    $sql =
        "SELECT * FROM products
         WHERE category_id = $category_id
         ORDER BY id DESC";

}


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

    <title>
        <?php echo htmlspecialchars($category_title); ?>
        | Nexus Gear
    </title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

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
     CATEGORY HEADER
========================================================= -->

<section class="category-header">

    <p class="section-label">
        NEXUS GEAR STORE
    </p>


    <h1>

        <?php

        echo htmlspecialchars(
            $category_title
        );

        ?>

    </h1>


    <p>

        Explore our collection of
        <?php

        echo strtolower(
            htmlspecialchars(
                $category_title
            )
        );

        ?>.

    </p>

</section>



<!-- =========================================================
     PRODUCTS
========================================================= -->

<section class="section">


    <div class="section-heading">

        <div>

            <p class="section-label">
                AVAILABLE PRODUCTS
            </p>

            <h2>

                <?php

                echo htmlspecialchars(
                    $category_title
                );

                ?>

            </h2>

        </div>


        <a
            href="index.php"
            class="view-text"
        >

            ← Back to Home

        </a>

    </div>



    <div class="product-grid">


        <?php if ($result && $result->num_rows > 0): ?>


            <?php while ($product = $result->fetch_assoc()): ?>


                <div
                    class="product-card"

                    data-id="<?php
                        echo $product['id'];
                    ?>"

                    data-name="<?php
                        echo htmlspecialchars(
                            $product['name']
                        );
                    ?>"

                    data-price="<?php
                        echo $product['price'];
                    ?>"

                    data-image="<?php
                        echo htmlspecialchars(
                            $product['image']
                        );
                    ?>"
                >


                    <!-- IMAGE -->

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


                        <div class="product-placeholder">

                            🎮

                        </div>


                        <?php if (!empty($product['old_price'])): ?>

                            <span class="product-badge">

                                DEAL

                            </span>

                        <?php endif; ?>


                    </div>



                    <!-- INFORMATION -->

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



                        <div class="rating">

                            ⭐

                            <?php

                            echo $product['rating'];

                            ?>

                        </div>



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



                            <button

                                class="add-button"

                                onclick="
                                    addToCart(
                                        <?php
                                        echo $product['id'];
                                        ?>
                                    )
                                "

                            >

                                +

                            </button>


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
                    There are currently no products
                    in this category.
                </p>

            </div>


        <?php endif; ?>


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
     CART
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
     ROBOT
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

    <div class="logo">

        NEXUS<span>GEAR</span>

    </div>


    <p>

        © 2026 Nexus Gear.
        Built for gamers.

    </p>

</footer>



<script src="js/script.js"></script>

</body>

</html>