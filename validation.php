<?php
// ✅ عرض جميع الأخطاء للمساعدة في التصحيح
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
include 'datebase.php';

// ✅ الاتصال بالداتا بيز
$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$pdo = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product = trim($_POST['product']);
    $price = trim($_POST['price']);
    $category = trim($_POST['category']);
    $image_path = "";

    $errors = [];

    // ✅ التحقق من الحقول الفارغة
    if (empty($product) || empty($price) || empty($category)) {
        $errors[] = "All fields are required!";
    }

    // ✅ التحقق من صحة السعر
    if (!is_numeric($price) || $price <= 0) {
        $errors[] = "Invalid price! It must be a positive number.";
    }

    // ✅ التحقق من رفع الصورة
    if (isset($_FILES['img']) && $_FILES['img']['error'] === 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));

        // ✅ التحقق من نوع الملف
        if (!in_array($file_ext, $allowed_extensions)) {
            $errors[] = "Invalid file type! Only JPG, PNG, and GIF are allowed.";
        }

        // ✅ التحقق من حجم الملف
        if ($_FILES['img']['size'] > 2 * 1024 * 1024) {
            $errors[] = "File size must be less than 2MB!";
        }
    } else {
        $errors[] = "Product image is required!";
    }

    // ✅ عرض الأخطاء إن وجدت
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        exit;
    }

    // ✅ رفع الصورة وتحديد المسار
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_name = time() . "_" . uniqid() . "." . $file_ext;
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
        $image_path = $file_name;
    } else {
        echo "<p style='color:red;'>Failed to upload image!</p>";
        exit;
    }

    // ✅ إدخال البيانات في الداتا بيز
    $stmt = $pdo->prepare("INSERT INTO Products (name, price, c_id, image_path, available) VALUES (?, ?, ?, ?, ?)");
    $available = 'available';

    try {
        if ($stmt->execute([$product, $price, $category, $image_path, $available])) {
            if ($stmt->rowCount() > 0) {
                header("Location: AddProduct.php?success=" . urlencode("Product added successfully!"));
            } else {
                header("Location: AddProduct.php?error=" . urlencode("No rows were inserted!"));
            }
        } else {
            header("Location: AddProduct.php?error=" . urlencode("Error inserting data!"));
        }
    } catch (PDOException $e) {
        header("Location: AddProduct.php?error=" . urlencode("Error inserting data: " . $e->getMessage()));
    }

    exit();
}
?>
