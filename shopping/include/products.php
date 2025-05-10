<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /shopping/include/login.php");
    exit;
}

$product_name = $price = $description = "";
$product_name_err = $price_err = $description_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate product name
    if (empty(trim($_POST["product_name"]))) {
        $product_name_err = "Please enter a product name.";
    } else {
        $product_name = trim($_POST["product_name"]);
    }

    // Validate price
    if (empty(trim($_POST["price"]))) {
        $price_err = "Please enter a price.";
    } elseif (!is_numeric($_POST["price"]) || $_POST["price"] < 0) {
        $price_err = "Please enter a valid price.";
    } else {
        $price = trim($_POST["price"]);
    }

    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter a description.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Check input errors before inserting in database
    if (empty($product_name_err) && empty($price_err) && empty($description_err)) {
        if (isset($_POST['edit_id'])) {
            // Update existing product
            $sql = "UPDATE products SET product_name = ?, price = ?, description = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "sdsi", $product_name, $price, $description, $_POST['edit_id']);
                if (mysqli_stmt_execute($stmt)) {
                    header("location: products.php");
                    exit;
                } else {
                    echo "Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            // Insert new product
            $sql = "INSERT INTO products (product_name, price, description) VALUES (?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "sds", $product_name, $price, $description);
                if (mysqli_stmt_execute($stmt)) {
                    header("location: products.php");
                    exit;
                } else {
                    echo "Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $sql = "DELETE FROM products WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_GET['delete']);
        if (mysqli_stmt_execute($stmt)) {
            header("location: products.php");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    $sql = "SELECT * FROM products WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_GET['edit']);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $product_name = $row['product_name'];
                $price = $row['price'];
                $description = $row['description'];
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all products
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container">
    <h1 class="mb-4">Manage Products</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3><?php echo isset($_GET['edit']) ? 'Edit Product' : 'Add New Product'; ?></h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <?php if(isset($_GET['edit'])): ?>
                            <input type="hidden" name="edit_id" value="<?php echo $_GET['edit']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control <?php echo (!empty($product_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $product_name; ?>">
                            <span class="invalid-feedback"><?php echo $product_name_err; ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $price; ?>">
                            <span class="invalid-feedback"><?php echo $price_err; ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>" rows="4"><?php echo $description; ?></textarea>
                            <span class="invalid-feedback"><?php echo $description_err; ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <input type="submit" class="btn btn-primary" value="<?php echo isset($_GET['edit']) ? 'Update Product' : 'Add Product'; ?>">
                            <?php if(isset($_GET['edit'])): ?>
                                <a href="products.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Product List</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                                        echo "<td>$" . number_format($row['price'], 2) . "</td>";
                                        echo "<td>" . htmlspecialchars(substr($row['description'], 0, 50)) . "...</td>";
                                        echo "<td>
                                                <a href='products.php?edit=" . $row['id'] . "' class='btn btn-sm btn-primary'>Edit</a>
                                                <a href='products.php?delete=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No products found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 