<?php
include('db.php'); // Include the database connection

$sql = "SELECT id, FirstName, LastName, username, email, password FROM users"; // Fetch all columns
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            text-align: center;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 0px 10px gray;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background: #28a745;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
    </style>
</head>
<body>

    <h2>Users List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Password (Hashed)</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row["id"] . "</td>
                        <td>" . $row["FirstName"] . "</td>
                        <td>" . $row["LastName"] . "</td>
                        <td>" . $row["username"] . "</td>
                        <td>" . $row["email"] . "</td>
                        <td>" . $row["password"] . "</td> <!-- Password is stored as a hash -->
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No users found</td></tr>";
        }
        $conn->close();
        ?>
    </table>

</body>
</html>
