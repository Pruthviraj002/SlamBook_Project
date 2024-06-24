<?php

session_start();
include "partials/dbcon.php";

if (!isset($_SESSION['uloggedin']) || !isset($_SESSION['userimg_id'])) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View</title>
    <link rel="stylesheet" href="/index.css">
    <script src="tailwind.js"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="_favicon_/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="_favicon_/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="_favicon_/favicon-16x16.png">
    <link rel="manifest" href="_favicon_/site.webmanifest">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            min-height: 100vh;
        }

        .alb {
            width: 200px;
            height: 200px;
            padding: 5px;
        }

        .alb img {
            width: 100%;
            height: 100%;

        }

        a {
            text-decoration: none;
            color: black;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        form input {
            margin-bottom: 10px;
        }
    </style>
</head>

<body class="bg-gradient-to-r from-indigo-500 to-orange-400">

    <div>
        <div class="flex flex-row">
            <?php
            $userId = $_SESSION['userimg_id'];
            $sql = "SELECT * FROM images WHERE u_id = $userId ORDER BY id DESC";
            $res = mysqli_query($conn, $sql);

            if (mysqli_num_rows($res) > 0) {
                while ($images = mysqli_fetch_assoc($res)) {
                    ?>
                    <div class="alb">
                        <img src="uploads/<?= $images['image_url'] ?>" class="rounded-lg border-2 border-white">
                    </div>
                    <?php
                }
            }
            ?>
        </div>


        <?php


        if (isset($_GET['error'])) {
            echo '<p>' . $_GET['error'] . '</p>';
        }
        echo '
<div class="flex flex-row justify-center">
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="my_image">
        <div class="flex flex-col">
        <input type="submit" name="submit" value="Upload" class="bg-white p-1 rounded-lg font-bold border-2 border-black">
    </form>
    <form action="thanks.php" method="post">
    
        <input type="submit" name="submit" value="Submit" class="bg-green-500 p-1 rounded-lg font-bold border-2 hover:text-white border-black">
        </div>
    </form>
    </div>
';
        ?>
    </div>
    <?php include 'partials/footer.php' ?>

</body>

</html>