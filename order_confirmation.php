<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            max-width: 500px;
        }

        h2 {
            color: #2e8b57;
        }

        p {
            font-size: 18px;
            color: #333;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background-color: #2e8b57;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        a:hover {
            background-color: #245f3e;
        }

        .footer {
            font-size: 14px;
            color: #777;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Thank You for Your Order!</h2>
    <p>Your order has been placed successfully.</p>
    <a href="user_products.php">Continue Shopping</a>
    <div class="footer">
        <p>If you have any questions, feel free to contact us.</p>
    </div>
</div>

</body>
</html>

