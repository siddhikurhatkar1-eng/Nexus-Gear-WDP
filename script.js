/* =========================================================
   NEXUS GEAR - CART SYSTEM
   Persistent Shopping Cart + Stock Protection
========================================================= */


/* ---------------------------------------------------------
   GET CART FROM BROWSER STORAGE
--------------------------------------------------------- */

let cart = JSON.parse(
    localStorage.getItem("nexusCart")
) || [];


/* ---------------------------------------------------------
   SAVE CART
--------------------------------------------------------- */

function saveCart() {

    localStorage.setItem(
        "nexusCart",
        JSON.stringify(cart)
    );

}


/* ---------------------------------------------------------
   ROBOT
--------------------------------------------------------- */

const robot =
    document.getElementById("robot");

const robotMessage =
    document.getElementById("robot-message");


function robotReact(message) {

    if (!robot || !robotMessage) {
        return;
    }

    robotMessage.textContent = message;

    const robotFace =
        robot.querySelector(".robot");

    if (robotFace) {

        robotFace.style.transform =
            "scale(1.2) rotate(5deg)";

        setTimeout(function () {

            robotFace.style.transform =
                "scale(1)";

        }, 400);

    }

}


/* ---------------------------------------------------------
   UPDATE CART COUNT
--------------------------------------------------------- */

function updateCartCount() {

    const cartCount =
        document.getElementById("cart-count");

    if (!cartCount) {
        return;
    }

    let totalItems = 0;

    cart.forEach(function (item) {

        totalItems += item.quantity;

    });

    cartCount.textContent =
        totalItems;

}


/* ---------------------------------------------------------
   ADD PRODUCT TO CART
--------------------------------------------------------- */

function addToCart(productId) {

    /*
     * Find the product card on the
     * current page.
     */

    const productCard =
        document.querySelector(
            '.product-card[data-id="' +
            productId +
            '"]'
        );


    if (!productCard) {

        console.log(
            "Product not found on this page."
        );

        return;

    }


    /*
     * Get product stock from
     * the HTML data attribute.
     */

    const stock =
        Number(productCard.dataset.stock);


    /*
     * If stock is zero or less,
     * do not allow the product
     * to enter the cart.
     */

    if (stock <= 0) {

        robotReact(
            "Aww... this item is out of stock! 🥺"
        );

        return;

    }


    /*
     * Get product information
     * from the HTML data attributes.
     */

    const product = {

        id:
            Number(productId),

        name:
            productCard.dataset.name,

        price:
            Number(productCard.dataset.price),

        image:
            productCard.dataset.image,

        stock:
            stock,

        quantity:
            1

    };


    /*
     * Check whether this product
     * is already in the cart.
     */

    const existingProduct =
        cart.find(function (item) {

            return item.id === product.id;

        });


    if (existingProduct) {

        /*
         * Make sure the saved stock
         * value is updated.
         */

        existingProduct.stock =
            stock;


        /*
         * Do not allow quantity to
         * become greater than stock.
         */

        if (
            existingProduct.quantity >=
            stock
        ) {

            robotReact(
                "That's all we have in stock! 😅"
            );

            return;

        }


        /*
         * Product already exists,
         * so increase quantity.
         */

        existingProduct.quantity++;


        robotReact(
            "Yayyy! One more! 🎉"
        );

    } else {

        /*
         * New product.
         */

        cart.push(product);


        robotReact(
            "Yayyy! Great choice! 🎉"
        );

    }


    /*
     * Save cart permanently.
     */

    saveCart();


    /*
     * Update number shown beside Cart.
     */

    updateCartCount();


    /*
     * Refresh cart contents if
     * the cart panel exists.
     */

    displayCart();


    console.log(
        "Cart:",
        cart
    );

}


/* ---------------------------------------------------------
   REMOVE PRODUCT
--------------------------------------------------------- */

function removeFromCart(productId) {

    productId =
        Number(productId);


    cart =
        cart.filter(function (item) {

            return item.id !== productId;

        });


    saveCart();

    updateCartCount();

    displayCart();


    robotReact(
        "Aww... bye bye! 🥺"
    );

}


/* ---------------------------------------------------------
   CHANGE QUANTITY
--------------------------------------------------------- */

function changeQuantity(
    productId,
    change
) {

    productId =
        Number(productId);


    const product =
        cart.find(function (item) {

            return item.id === productId;

        });


    if (!product) {
        return;
    }


    /*
     * If the product is being increased,
     * check its available stock.
     */

    if (change > 0) {

        /*
         * First try to get the latest
         * stock from the product card
         * currently displayed on the page.
         */

        const productCard =
            document.querySelector(
                '.product-card[data-id="' +
                productId +
                '"]'
            );


        let availableStock =
            Number(product.stock);


        /*
         * If the product card exists,
         * use its current stock value.
         */

        if (productCard) {

            availableStock =
                Number(
                    productCard.dataset.stock
                );

            product.stock =
                availableStock;

        }


        /*
         * Stop the quantity from
         * going above available stock.
         */

        if (
            product.quantity >=
            availableStock
        ) {

            robotReact(
                "That's all we have in stock! 😅"
            );

            return;

        }

    }


    /*
     * Change quantity.
     */

    product.quantity +=
        change;


    /*
     * If quantity becomes zero,
     * remove the product.
     */

    if (product.quantity <= 0) {

        removeFromCart(productId);

        return;

    }


    saveCart();

    updateCartCount();

    displayCart();

}


/* ---------------------------------------------------------
   CALCULATE CART TOTAL
--------------------------------------------------------- */

function getCartTotal() {

    let total = 0;


    cart.forEach(function (item) {

        total +=
            item.price *
            item.quantity;

    });


    return total;

}


/* ---------------------------------------------------------
   DISPLAY CART
--------------------------------------------------------- */

function displayCart() {

    const cartItems =
        document.getElementById(
            "cart-items"
        );


    const cartTotal =
        document.getElementById(
            "cart-total"
        );


    /*
     * If this page doesn't contain
     * the cart panel, stop here.
     */

    if (!cartItems) {
        return;
    }


    /*
     * Empty cart
     */

    if (cart.length === 0) {

        cartItems.innerHTML = `

            <div class="empty-cart">

                Your cart is empty.

            </div>

        `;


        if (cartTotal) {

            cartTotal.textContent =
                "₹0";

        }

updateFreeShipping();

        return;

    }


    /*
     * Create HTML for every product.
     */

    cartItems.innerHTML = "";


    cart.forEach(function (item) {

        const itemTotal =
            item.price *
            item.quantity;


        const cartItem =
            document.createElement(
                "div"
            );


        cartItem.className =
            "cart-item";


        cartItem.innerHTML = `

            <div class="cart-item-image">

                <img
                    src="images/${item.image}"
                    alt="${item.name}"
                    onerror="
                        this.style.display='none';
                    "
                >

            </div>


            <div class="cart-item-details">

                <h4>
                    ${item.name}
                </h4>


                <p>
                    ₹${item.price.toFixed(2)}
                </p>


                <div class="quantity-controls">


                    <button
                        onclick="
                            changeQuantity(
                                ${item.id},
                                -1
                            )
                        "
                    >

                        −

                    </button>


                    <span>

                        ${item.quantity}

                    </span>


                    <button
                        onclick="
                            changeQuantity(
                                ${item.id},
                                1
                            )
                        "
                    >

                        +

                    </button>


                </div>


                <button
                    class="remove-cart-item"
                    onclick="
                        removeFromCart(
                            ${item.id}
                        )
                    "
                >

                    Remove

                </button>


            </div>


            <strong class="cart-item-total">

                ₹${itemTotal.toFixed(2)}

            </strong>

        `;


        cartItems.appendChild(
            cartItem
        );

    });


    /*
     * Show total.
     */

    if (cartTotal) {

        cartTotal.textContent =
            "₹" +
            getCartTotal().toFixed(2);

    }

    updateFreeShipping();

}


/* ---------------------------------------------------------
   OPEN CART
--------------------------------------------------------- */

function openCart() {

    const cartPanel =
        document.getElementById(
            "cart-panel"
        );


    const cartOverlay =
        document.getElementById(
            "cart-overlay"
        );


    if (cartPanel) {

        cartPanel.classList.add(
            "active"
        );

    }


    if (cartOverlay) {

        cartOverlay.classList.add(
            "active"
        );

    }


    displayCart();

}


/* ---------------------------------------------------------
   CLOSE CART
--------------------------------------------------------- */

function closeCart() {

    const cartPanel =
        document.getElementById(
            "cart-panel"
        );


    const cartOverlay =
        document.getElementById(
            "cart-overlay"
        );


    if (cartPanel) {

        cartPanel.classList.remove(
            "active"
        );

    }


    if (cartOverlay) {

        cartOverlay.classList.remove(
            "active"
        );

    }

}


/* ---------------------------------------------------------
   CHECKOUT
--------------------------------------------------------- */

function checkout() {

    if (cart.length === 0) {

        robotReact(
            "Your cart is empty! 🥺"
        );

        return;

    }


    /*
     * Go to the real checkout page.
     */

    window.location.href =
        "checkout.php";

}


/* ---------------------------------------------------------
   ROBOT CLICK
--------------------------------------------------------- */

if (robot) {

    robot.addEventListener(
        "click",
        function () {

            robotReact(
                "Need some gaming gear? 🤖🎮"
            );

        }
    );

}


/* ---------------------------------------------------------
   LOAD CART WHEN PAGE OPENS
--------------------------------------------------------- */

document.addEventListener(
    "DOMContentLoaded",
    function () {

        updateCartCount();

        displayCart();

    }
);

/* ---------------------------------------------------------
   FREE SHIPPING
   Free shipping on orders of ₹1500 or more
--------------------------------------------------------- */

function updateFreeShipping() {

    const shippingMessage =
        document.getElementById("shipping-message");

    if (!shippingMessage) {
        return;
    }

    const total = getCartTotal();

    const freeShippingLimit = 1500;

    if (total >= freeShippingLimit) {

        shippingMessage.innerHTML =
            "🚚 FREE SHIPPING UNLOCKED! 🎉";

        shippingMessage.classList.add(
            "free-shipping-unlocked"
        );

    } else {

        const remaining =
            freeShippingLimit - total;

        shippingMessage.innerHTML =
            "🚚 Add ₹" +
            remaining.toFixed(2) +
            " more to unlock FREE SHIPPING";

        shippingMessage.classList.remove(
            "free-shipping-unlocked"
        );

    }

}

/* ---------------------------------------------------------
   PRODUCT SEARCH
--------------------------------------------------------- */

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const searchInput =
            document.getElementById(
                "product-search"
            );


        if (!searchInput) {
            return;
        }


        searchInput.addEventListener(
            "input",
            function () {

                const searchText =
                    searchInput.value
                        .toLowerCase()
                        .trim();


                const productCards =
                    document.querySelectorAll(
                        ".product-card"
                    );


                productCards.forEach(
                    function (card) {

                        const name =
                            (
                                card.dataset.name
                                || ""
                            ).toLowerCase();


                        const description =
                            (
                                card.querySelector(
                                    ".description"
                                )?.textContent
                                || ""
                            ).toLowerCase();


                        const category =
                            (
                                card.dataset.category
                                || ""
                            ).toLowerCase();


                        const matches =
                            name.includes(searchText)
                            ||
                            description.includes(searchText)
                            ||
                            category.includes(searchText);


                        if (matches) {

                            card.style.display =
                                "";

                        } else {

                            card.style.display =
                                "none";

                        }

                    }
                );

            }
        );

    }
);