<?php
require_once 'includes/header.php';

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: index.php");
    exit;
}

$product_id = $_GET['id'];

// Fetch product details
$sql = "SELECT * FROM products WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) == 1) {
            $product = mysqli_fetch_assoc($result);
        } else {
            header("location: index.php");
            exit;
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit;
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Oops! Something went wrong. Please try again later.";
    exit;
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    <p class="card-text"><strong>Price: $<?php echo number_format($product['price'], 2); ?></strong></p>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="mt-3">
                            <a href="products.php?edit=<?php echo $product['id']; ?>" class="btn btn-primary">Edit Product</a>
                            <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete Product</a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <a href="index.php" class="btn btn-secondary">Back to Products</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 