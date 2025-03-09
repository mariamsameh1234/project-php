<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'database.php';
require 'business_logic.php';

$db = new Database();
$pdo = $db->getConnection();
$productModel = new Product($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product = trim($_POST["product"]);
    $price = trim($_POST["price"]);
    $category_id = trim($_POST["category"]);
    $image = isset($_FILES["img"]) && $_FILES["img"]["error"] == 0 ? $_FILES["img"]["name"] : null;

    $errors = [];

    // ✅ التحقق من أن جميع الحقول ممتلئة
    if (empty($product)) {
        $errors[] = "Product name is required.";
    }

    if (empty($price) || !is_numeric($price) || $price <= 0) {
        $errors[] = "Enter a valid price.";
    }

    if (empty($category_id)) {
        $errors[] = "Select a category.";
    }

    // ✅ التحقق من صحة الفئة
    $stmt = $pdo->prepare("SELECT c_id FROM Category WHERE c_id = ?");
    $stmt->execute([$category_id]);
    if (!$stmt->fetchColumn()) {
        $errors[] = "Invalid category selected.";
    }

    // ✅ التحقق من الصورة
    $image_path = null;
    if (!$image) {
        $errors[] = "Please upload an image.";
    } else {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_ext)) {
            $errors[] = "Invalid image format. Only JPG, PNG, and GIF are allowed.";
        } else {
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            $upload_dir = "uploads/";
            $image_path = $upload_dir . basename($image);

            if (!move_uploaded_file($_FILES["img"]["tmp_name"], $image_path)) {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    // ✅ إذا كان هناك أخطاء، إعادة المستخدم مع رسالة خطأ
    if (!empty($errors)) {
        $error_message = urlencode(implode("<br>", $errors));
        header("Location: AddProduct.php?error=$error_message");
        exit();
    }

    // ✅ إدخال البيانات في قاعدة البيانات
    if ($productModel->insert($product, $price, $category_id, $image_path)) {
        header("Location: add_product.php?success=Product added successfully!");
        exit();
    } else {
        header("Location: add_product.php?error=Failed to add product.");
        exit();
    }
}
?>
