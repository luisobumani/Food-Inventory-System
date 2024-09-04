<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "inventory";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function clearTable($conn) {
    $sql = "TRUNCATE TABLE inventoryform";
    
    if ($conn->query($sql) === TRUE) {
        echo '<script>("Table cleared successfully.")</script>';
    } else {
        echo "Error clearing table: " . $conn->error;
    }
}

if (isset($_POST['clear_table'])) {
    clearTable($conn);
}

function computeStatus($conn, $productID, $inventoryQuantity) {
    $sql = "SELECT `$productID` FROM supply";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (isset($row[$productID])) {
            $supplyQuantity = $row[$productID];

            if ($inventoryQuantity > $supplyQuantity) {
                return "Unavailable. Please Restock.";
            } else if ($inventoryQuantity > ($supplyQuantity * 0.75)) {
                return "Critical";
            } else {
                return "Sufficient";
            }
        } else {
            return "N/A";
        }
    } else {
        return "N/A";
    }
}
function printInventoryTable($conn) {
    $sql = "SELECT * FROM inventoryform";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table>
                <tr>
                    <th>Date</th>
                    <th>Product ID</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>';

        while ($row = $result->fetch_assoc()) {
            $status = computeStatus($conn, $row['product_id'], $row['quantity']);
            echo '<tr>
                    <td>' . $row['date'] . '</td>
                    <td>' . $row['product_id'] . '</td>
                    <td>' . $row['product_name'] . '</td>
                    <td>' . $row['quantity'] . '</td>
                    <td>' . $status . '</td>
                </tr>';
        }

        echo '</table>';
    } else {
        echo "No inventory data found.";
    }
}

?>


<!DOCTYPE html>
<html>
<head>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            font-family: verdana;
            background-image: url("https://i.pinimg.com/originals/d3/6d/46/d36d462db827833805497d9ea78a1343.jpg");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }
        .header {
            margin-top: 0px;
            position: absolute;
            align-items: center;
            display: flex;
            background-color: #FEE135;
            width: 100%;
            outline: auto;
        }
        .heading {
            font-size: 60px;
            color: #362204;
            margin-left: 50px;
        }
        .logo {
            margin-top: 10px;
            margin-left: 10px;
        }
        .backForm {
            margin-top: 60px;
            margin-right: 30px;
            position: absolute;
            right: 10px;
            top: 10px;
        }
        .summaryChart {
            margin: 300px auto;
            color: #362204;
            padding: 60px;
            border-radius: 12px;
            width: 1000px;
            margin-left: 400px;
            justify-content: center;
        }
        .summaryChart table {
            width: 100%;
            background-color: rgba(254, 225, 53, 0.7);
            padding: 60px;
            border-radius: 12px;
            color: #362204;
        }
        .summaryChart th {
            background-color: #FEE135;
            padding: 10px;
        }
        .summaryChart td {
            padding: 5px;
            text-align: center;
        }
        .clearForm {
            margin-top: auto; 
            margin-right: 50px; 
            margin-bottom: 10px; 
            justify-content: flex-end; 
            flex-direction: column;
            text-align: right;
            position: absolute;
            bottom: 0; 
            right: 0;
        }
        .addMoreForm {
            margin-top: auto; 
            margin-right: 10px; 
            margin-bottom: 10px; 
            justify-content: flex-end; 
            flex-direction: column;
            text-align: right;
            position: absolute;
            bottom: 0; 
            right: 220px;
        }
        .container{
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #362204;
        }
        .backButton {
            margin-top: 10px;
            padding: 10px;
            width: 200px;
            border-radius: 6px;
            cursor: pointer;
            background-color: #FEE135;
            font-family: verdana;
            font-weight: bold;
            outline: none;
            font-size: 15px;
            transition: all 0.3s ease-in-out;
        }
        .backButton:hover {
            background-color: #362204;
            color: #ffffff;
        }

        .addMoreButton {
            padding: 15px;
            background-color: #FEE135;
            border: #372204;
            border-radius: 6px;
            cursor: pointer;
            font-family: verdana;
            font-weight: bold;
            outline: auto;
            font-size: 15px;
            color: #362204;
            transition: all 0.3s ease-in-out;
        }
        .addMoreButton:hover {
            background-color: #362204;
            color: #ffffff;
        }

        .clearButton {
            padding: 15px;
            background-color: #FEE135;
            border: #372204;
            border-radius: 6px;
            cursor: pointer;
            font-family: verdana;
            font-weight: bold;
            outline: auto;
            font-size: 15px;
            color: #362204;
            transition: all 0.3s ease-in-out;
        }
        .clearButton:hover {
            background-color: #362204;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="logo.png" style="width:300px;height:200px;">
        </div>
        <b class="heading">Inventory Form</b>
        <form class="backForm" action="adminwelcome.php">
            <button class="backButton" type="submit">Back to Main Menu</button>
        </form>
    </div>
    <div class="summaryChart">
        <?php printInventoryTable($conn); ?>
    </div>
    <form class="clearForm" method="POST" action="">
        <button class="clearButton" type="submit" name="clear_table">Clear Table</button>
    </form>
    <form class="addMoreForm">
        <a href="admininventory.php"><button class="addMoreButton" type="button">Add More to Inventory</button></a>
    </form>
</body>
</html>

<?php
$conn->close();
?>
