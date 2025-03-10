<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'header2.php';  
include_once 'config.php';
include_once 'datebase.php'; 
include_once 'business_logic.php';  

$dbConfig = new DatabaseConfig();
$db = new Database($dbConfig->getHost(), $dbConfig->getUser(), $dbConfig->getPass(), $dbConfig->getDbName());
$productObj = new Product($db);
$products = $productObj->getAll();
?>

<h1 class="head">All Products</h1>

<div class="addProduct">
    <a style="float:right; margin-right:30px; font-size:20px; color:blue; text-decoration:underline;" href="AddProduct.php">
        Add Product
    </a>
</div>

<div class="containerr">
    <div class="table">
        <div class="table-header" style="background-color: #000; color: #fff;">
            <div class="header__item">Product ID</div>
            <div class="header__item">Product Name</div>
            <div class="header__item">Product Price</div>
            <div class="header__item">Product Image</div>
            <div class="header__item">Actions</div>
        </div>
        <div class="table-content">
            <?php foreach ($products as $product): ?>
                <div class='table-row'>
                    <div class='table-data'>
                        <?= isset($product['P_id']) ? htmlspecialchars($product['P_id']) : 'N/A' ?>
                    </div>
                    <div class='table-data'><?= htmlspecialchars($product['name']) ?></div>
                    <div class='table-data'><?= htmlspecialchars($product['price']) ?> EGP</div>
                    <div class='table-data'>
                        <?php if (!empty($product['image_path'])): ?>
                            <img src="uploads/<?= htmlspecialchars($product['image_path']) ?>" alt="Product Image" width="50">
                        <?php else: ?>
                            <img src="uploads/default.png" alt="Default" width="50">
                        <?php endif; ?>
                    </div>
                    <div class='table-data' style='display: flex; gap: 5px; justify-content: center;'>
                        <form method='GET' action='update_product.php' style='display:inline;'>
                            <input type='hidden' name='P_id' value='<?= isset($product['P_id']) ? htmlspecialchars($product['P_id']) : '' ?>'>
                            <input type='submit' value='Update' style='border-radius: 20px; padding: 5px 10px; font-size: 14px;'>
                        </form>
                        <form method='POST' action='delete_product.php' style='display:inline;'>
                            <input type='hidden' name='P_id' value='<?= isset($product['P_id']) ? htmlspecialchars($product['P_id']) : '' ?>'>
                            <input type='submit' name='delete' value='Delete' style='border-radius: 20px; padding: 5px 10px; font-size: 14px;'>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
