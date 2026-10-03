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
   UPDATE USER ROLE
========================================================= */

if (isset($_POST["update_role"])) {

    $user_id = intval($_POST["user_id"]);
    $role = $_POST["role"];

    /* Only these two roles are allowed */

    if ($role === "customer" || $role === "admin") {

        /*
           Prevent the currently logged-in admin
           from accidentally removing their own admin access.
        */

        if ($user_id == $_SESSION["user_id"]) {

            $message =
                "You cannot change your own admin role.";

        } else {

            $stmt = $conn->prepare(
                "UPDATE users
                 SET role = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "si",
                $role,
                $user_id
            );

            if ($stmt->execute()) {

                $message =
                    "User role updated successfully.";

            } else {

                $message =
                    "Could not update user role.";

            }

            $stmt->close();

        }

    }

}


/* =========================================================
   GET ALL USERS
========================================================= */

$users = $conn->query(
    "SELECT id, name, email, role, created_at
     FROM users
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

    <title>Manage Users | Nexus Gear</title>


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


        .table-box {

            background: #111827;

            border: 1px solid #263247;

            border-radius: 14px;

            padding: 25px;

            overflow-x: auto;

        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 800px;

        }


        th,
        td {

            padding: 15px;

            text-align: left;

            border-bottom: 1px solid #293449;

        }


        th {

            color: #c084fc;

            font-size: 15px;

        }


        td {

            color: #eeeeee;

        }


        .role {

            display: inline-block;

            padding: 6px 12px;

            border-radius: 20px;

            font-weight: bold;

            font-size: 13px;

        }


        .admin-role {

            background: #3b1c72;

            color: #c4b5fd;

        }


        .customer-role {

            background: #123047;

            color: #93c5fd;

        }


        .role-form {

            display: flex;

            gap: 8px;

            align-items: center;

        }


        select {

            padding: 9px;

            border-radius: 7px;

            border: 1px solid #374151;

            background: #080c17;

            color: white;

        }


        .update-button {

            padding: 9px 14px;

            border: none;

            border-radius: 7px;

            background: #7c2be8;

            color: white;

            font-weight: bold;

            cursor: pointer;

        }


        .current-user {

            color: #c084fc;

            font-weight: bold;

        }


        .no-users {

            text-align: center;

            color: #a7a7b5;

            padding: 30px;

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

        <h1>👥 Manage Users</h1>


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
         USERS TABLE
    ====================================================== -->

    <div class="table-box">


        <h2>

            👥 Registered Users

        </h2>


        <?php if ($users->num_rows > 0): ?>


            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Email</th>

                        <th>Role</th>

                        <th>Created At</th>

                        <th>Change Role</th>

                    </tr>

                </thead>


                <tbody>


                <?php while ($user = $users->fetch_assoc()): ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            <?php

                            echo $user["id"];

                            ?>

                        </td>


                        <!-- NAME -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $user["name"]
                            );

                            ?>


                            <?php

                            if (
                                $user["id"]
                                ==
                                $_SESSION["user_id"]
                            ) {

                                echo '<br>
                                <span class="current-user">
                                (You)
                                </span>';

                            }

                            ?>

                        </td>


                        <!-- EMAIL -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $user["email"]
                            );

                            ?>

                        </td>


                        <!-- CURRENT ROLE -->

                        <td>


                            <?php if (
                                $user["role"]
                                === "admin"
                            ): ?>

                                <span
                                    class="role admin-role"
                                >

                                    👑 Admin

                                </span>

                            <?php else: ?>

                                <span
                                    class="role customer-role"
                                >

                                    👤 Customer

                                </span>

                            <?php endif; ?>


                        </td>


                        <!-- CREATED DATE -->

                        <td>

                            <?php

                            echo date(
                                "d M Y",
                                strtotime(
                                    $user["created_at"]
                                )
                            );

                            ?>

                        </td>


                        <!-- CHANGE ROLE -->

                        <td>


                            <?php

                            if (
                                $user["id"]
                                ==
                                $_SESSION["user_id"]
                            ):

                            ?>

                                <span
                                    class="current-user"
                                >

                                    Current Admin

                                </span>

                            <?php else: ?>


                                <form
                                    method="POST"
                                    class="role-form"
                                >


                                    <input
                                        type="hidden"
                                        name="user_id"
                                        value="<?php

                                        echo $user["id"];

                                        ?>"
                                    >


                                    <select
                                        name="role"
                                    >

                                        <option
                                            value="customer"
                                            <?php

                                            if (
                                                $user["role"]
                                                === "customer"
                                            ) {

                                                echo "selected";

                                            }

                                            ?>
                                        >

                                            Customer

                                        </option>


                                        <option
                                            value="admin"
                                            <?php

                                            if (
                                                $user["role"]
                                                === "admin"
                                            ) {

                                                echo "selected";

                                            }

                                            ?>
                                        >

                                            Admin

                                        </option>

                                    </select>


                                    <button
                                        type="submit"
                                        name="update_role"
                                        class="update-button"
                                    >

                                        Update

                                    </button>


                                </form>


                            <?php endif; ?>


                        </td>


                    </tr>


                <?php endwhile; ?>


                </tbody>

            </table>


        <?php else: ?>


            <div class="no-users">

                <h2>No Users Found</h2>

                <p>

                    Registered users will appear here.

                </p>

            </div>


        <?php endif; ?>


    </div>


</div>


</body>

</html>