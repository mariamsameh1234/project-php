<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// تضمين الملفات المطلوبة
include_once 'config.php';
include_once 'datebase.php';
include_once 'business_logic.php'; 

// إعداد قاعدة البيانات والكائنات المطلوبة
$dbConfig = new DatabaseConfig();
$db = new Database($dbConfig->getHost(), $dbConfig->getUser(), $dbConfig->getPass(), $dbConfig->getDbName());
$productObj = new Product($db);

// التحقق من وجود معرّف المستخدم في الـ URL
if (isset($_GET['id'])) {
    $productId = $_GET['id']; // تغيير من 'userId' إلى 'productId' إذا كان الكود يتعامل مع المنتجات بدلاً من المستخدمين

    // محاولة حذف المنتج بناءً على المعرّف
    if ($productObj->delete($productId)) {
        header("Location: display_products.php?success=Product deleted successfully");
    } else {
        header("Location: display_products.php?error=Failed to delete product");
    }
    exit;
}
?>
