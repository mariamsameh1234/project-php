<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
include 'database.php';
include 'business_logic.php';

$dbConfig = new DatabaseConfig();
$db = new Database($dbConfig);
$productObj  = new Product($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product = trim($_POST['product']);
    $price = trim($_POST['price']);
    $category = trim($_POST['category']);
    $image_path = null;
    $errors = [];

   
    if (empty($product) || empty($price) || empty($category)) {
        $errors[] = "All fields are required!";
    }

   
    if (!is_numeric($price) || $price <= 0) {
        $errors[] = "Invalid price! It must be a positive number.";
    }

    
    if (isset($_FILES['img']) && $_FILES['img']['error'] === 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_extensions)) {
            $errors[] = "Invalid file type! Only JPG, PNG, and GIF are allowed.";
        }

        if ($_FILES['img']['size'] > 2 * 1024 * 1024) {
            $errors[] = "File size must be less than 2MB!";
        }
    } else {
        $errors[] = "Product image is required!";
    }


    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        exit;
    }

  
    $upload_dir = './images/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_name = time() . "_" . uniqid() . "." . $file_ext;
    $target_file = $upload_dir . $file_name;
    if (move_uploaded_file($_FILES['img']['tmp_name'], $target_file)) {
        $image_path = $target_file;
    } else {
        echo "<p style='color:red;'>Failed to upload image!</p>";
        exit;
    }

    
    try {
        $inserted = $productObj->insert($product, $price, $category, $image_path);
        
        if ($inserted) {
            header("Location: AddProduct.php?success=" . urlencode("Product added successfully!"));
        } else {
            header("Location: AddProduct.php?error=" . urlencode("No rows were inserted!"));
        }
    } catch (PDOException $e) {
        header("Location: AddProduct.php?error=" . urlencode("Error inserting data: " . $e->getMessage()));
    }
    
    exit();
}
?>
