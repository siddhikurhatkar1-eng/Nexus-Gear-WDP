<?php

session_start();
require_once "config/database.php";

/* ---------------------------------
   CHECK LOGIN
--------------------------------- */

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

/* ---------------------------------
   CHECK ADMIN
--------------------------------- */

if ($_SESSION["user_role"] !== "admin") {
    header("Location: customer-dashboard.php");
    exit;
}

$message = "";

/* ---------------------------------
   DELETE PRODUCT
--------------------------------- */

if (isset($_GET["delete"])) {

    $id = intval($_GET["delete"]);

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "Product deleted successfully.";
    } else {
        $message = "Could not delete this product.";
    }

    $stmt->close();
}

/* ---------------------------------
   ADD PRODUCT
--------------------------------- */

if (isset($_POST["add_product"])) {

    $name = trim($_POST["name"]);
    $category_id = intval($_POST["category_id"]);
    $price = floatval($_POST["price"]);
    $old_price = !empty($_POST["old_price"])
        ? floatval($_POST["old_price"])
        : null;

    $description = trim($_POST["description"]);
    $image = trim($_POST["image"]);
    $stock = intval($_POST["stock"]);
    $rating = floatval($_POST["rating"]);

    $stmt = $conn->prepare(
        "INSERT INTO products
        (name, category_id, price, old_price, description, image, stock, rating)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "siddssid",
        $name,
        $category_id,
        $price,
        $old_price,
        $description,
        $image,
        $stock,
        $rating
    );

    if ($stmt->execute()) {
        $message = "Product added successfully.";
    } else {
        $message = "Error adding product.";
    }

    $stmt->close();
}

/* ---------------------------------
   UPDATE PRODUCT
--------------------------------- */

if (isset($_POST["update_product"])) {

    $id = intval($_POST["id"]);

    $name = trim($_POST["name"]);
    $category_id = intval($_POST["category_id"]);
    $price = floatval($_POST["price"]);

    $old_price = !empty($_POST["old_price"])
        ? floatval($_POST["old_price"])
        : null;

    $description = trim($_POST["description"]);
    $image = trim($_POST["image"]);
    $stock = intval($_POST["stock"]);
    $rating = floatval($_POST["rating"]);

    $stmt = $conn->prepare(
        "UPDATE products
         SET name = ?,
             category_id = ?,
             price = ?,
             old_price = ?,
             description = ?,
             image = ?,
             stock = ?,
             rating = ?
         WHERE id = ?"
    );

    $stmt->bind_param(
        "siddssidi",
        $name,
        $category_id,
        $price,
        $old_price,
        $description,
        $image,
        $stock,
        $rating,
        $id
    );

    if ($stmt->execute()) {
        $message = "Product updated successfully.";
    } else {
        $message = "Error updating product.";
    }

    $stmt->close();
}

/* ---------------------------------
   GET PRODUCT FOR EDITING
--------------------------------- */

$edit_product = null;

if (isset($_GET["edit"])) {

    $id = intval($_GET["edit"]);

    $stmt = $conn->prepare(
        "SELECT * FROM products WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $edit_product = $result->fetch_assoc();
    }

    $stmt->close();
}

/* ---------------------------------
   GET ALL PRODUCTS
--------------------------------- */

$result = $conn->query(
    "SELECT * FROM products ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Products | Nexus Gear</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #070914;
            color: white;
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

        .form-box {
            background: #111827;
            border: 1px solid #263247;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 35px;
        }

        .form-box h2 {
            margin-top: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        textarea {
            padding: 12px;
            border-radius: 7px;
            border: 1px solid #374151;
            background: #080c17;
            color: white;
            font-size: 15px;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .save-button {
            margin-top: 20px;
            background: #7c2be8;
            color: white;
            border: none;
            padding: 13px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .cancel-button {
            display: inline-block;
            margin-left: 10px;
            background: #374151;
            color: white;
            text-decoration: none;
            padding: 13px 25px;
            border-radius: 8px;
            font-weight: bold;
        }

        .table-box {
            background: #111827;
            border: 1px solid #263247;
            border-radius: 12px;
            padding: 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th,
        td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #293449;
        }

        th {
            color: #c084fc;
        }

        .edit-button {
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 8px 13px;
            border-radius: 6px;
        }

        .delete-button {
            background: #dc2626;
            color: white;
            text-decoration: none;
            padding: 8px 13px;
            border-radius: 6px;
            margin-left: 5px;
        }

        @media (max-width: 700px) {

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <div class="top-bar">

        <h1>🎮 Manage Products</h1>

        <a href="admin-dashboard.php"
           class="back-button">
            ← Admin Dashboard
        </a>

    </div>


    <?php if ($message != ""): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <!-- PRODUCT FORM -->

    <div class="form-box">

        <?php if ($edit_product): ?>

            <h2>✏️ Edit Product</h2>

            <form method="POST">

                <input type="hidden"
                       name="id"
                       value="<?php echo $edit_product["id"]; ?>">

                <div class="form-grid">

                    <div class="form-group">

                        <label>Product Name</label>

                        <input
                            type="text"
                            name="name"
                            required
                            value="<?php echo htmlspecialchars($edit_product["name"]); ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label>Category ID</label>

                        <input
                            type="number"
                            name="category_id"
                            required
                            value="<?php echo $edit_product["category_id"]; ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label>Price</label>

                        <input
                            type="number"
                            step="0.01"
                            name="price"
                            required
                            value="<?php echo $edit_product["price"]; ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label>Old Price</label>

                        <input
                            type="number"
                            step="0.01"
                            name="old_price"
                            value="<?php echo $edit_product["old_price"]; ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label>Stock</label>

                        <input
                            type="number"
                            name="stock"
                            value="<?php echo $edit_product["stock"]; ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label>Rating</label>

                        <input
                            type="number"
                            step="0.1"
                            min="0"
                            max="5"
                            name="rating"
                            value="<?php echo $edit_product["rating"]; ?>"
                        >

                    </div>


                    <div class="form-group full">

                        <label>Image Filename</label>

                        <input
                            type="text"
                            name="image"
                            value="<?php echo htmlspecialchars($edit_product["image"] ?? ""); ?>"
                            placeholder="example: mouse.jpg"
                        >

                    </div>


                    <div class="form-group full">

                        <label>Description</label>

                        <textarea name="description"><?php
                            echo htmlspecialchars($edit_product["description"] ?? "");
                        ?></textarea>

                    </div>

                </div>


                <button
                    type="submit"
                    name="update_product"
                    class="save-button">
                    Update Product
                </button>


                <a href="manage-products.php"
                   class="cancel-button">
                    Cancel
                </a>

            </form>


        <?php else: ?>

            <h2>➕ Add New Product</h2>

            <form method="POST">

                <div class="form-grid">

                    <div class="form-group">

                        <label>Product Name</label>

                        <input
                            type="text"
                            name="name"
                            required
                            placeholder="Example: Gaming Mouse"
                        >

                    </div>


                    <div class="form-group">

                        <label>Category ID</label>

                        <input
                            type="number"
                            name="category_id"
                            required
                            placeholder="Example: 1"
                        >

                    </div>


                    <div class="form-group">

                        <label>Price</label>

                        <input
                            type="number"
                            step="0.01"
                            name="price"
                            required
                            placeholder="Example: 1499"
                        >

                    </div>


                    <div class="form-group">

                        <label>Old Price</label>

                        <input
                            type="number"
                            step="0.01"
                            name="old_price"
                            placeholder="Example: 1999"
                        >

                    </div>


                    <div class="form-group">

                        <label>Stock</label>

                        <input
                            type="number"
                            name="stock"
                            value="0"
                        >

                    </div>


                    <div class="form-group">

                        <label>Rating</label>

                        <input
                            type="number"
                            step="0.1"
                            min="0"
                            max="5"
                            name="rating"
                            value="0"
                        >

                    </div>


                    <div class="form-group full">

                        <label>Image Filename</label>

                        <input
                            type="text"
                            name="image"
                            placeholder="example: gaming-mouse.jpg"
                        >

                    </div>


                    <div class="form-group full">

                        <label>Description</label>

                        <textarea
                            name="description"
                            placeholder="Enter product description..."
                        ></textarea>

                    </div>

                </div>


                <button
                    type="submit"
                    name="add_product"
                    class="save-button">
                    Add Product
                </button>

            </form>

        <?php endif; ?>

    </div>


    <!-- PRODUCTS TABLE -->

    <div class="table-box">

        <h2>📦 Existing Products</h2>

        <table>

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Old Price</th>
                    <th>Stock</th>
                    <th>Rating</th>
                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

            <?php while ($product = $result->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php echo $product["id"]; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($product["name"]); ?>
                    </td>

                    <td>
                        <?php echo $product["category_id"]; ?>
                    </td>

                    <td>
                        ₹<?php echo number_format($product["price"], 2); ?>
                    </td>

                    <td>

                        <?php

                        if ($product["old_price"] !== null) {
                            echo "₹" .
                                 number_format(
                                     $product["old_price"],
                                     2
                                 );
                        } else {
                            echo "-";
                        }

                        ?>

                    </td>

                    <td>
                        <?php echo $product["stock"]; ?>
                    </td>

                    <td>
                        <?php echo $product["rating"]; ?>/5
                    </td>

                    <td>

                        <a
                            href="manage-products.php?edit=<?php echo $product["id"]; ?>"
                            class="edit-button">
                            Edit
                        </a>

                        <a
                            href="manage-products.php?delete=<?php echo $product["id"]; ?>"
                            class="delete-button"
                            onclick="return confirm('Are you sure you want to delete this product?');">
                            Delete
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>