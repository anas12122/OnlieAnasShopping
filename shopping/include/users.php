<?php
require_once 'includes/header.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /shopping/index.php");
    exit;
}

$username = $email = $password = $role = "";
$username_err = $email_err = $password_err = $role_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT uid FROM users WHERE username = ? AND uid != ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $param_username, $param_id);
            $param_username = trim($_POST["username"]);
            $param_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : 0;
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $sql = "SELECT uid FROM users WHERE email = ? AND uid != ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $param_email, $param_id);
            $param_email = trim($_POST["email"]);
            $param_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : 0;
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "This email is already registered.";
                } else {
                    $email = trim($_POST["email"]);
                }
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password for new users
    if (!isset($_POST['edit_id'])) {
        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter a password.";
        } elseif (strlen(trim($_POST["password"])) < 6) {
            $password_err = "Password must have at least 6 characters.";
        } else {
            $password = trim($_POST["password"]);
        }
    }

    // Validate role
    if (empty(trim($_POST["role"]))) {
        $role_err = "Please select a role.";
    } else {
        $role = trim($_POST["role"]);
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($role_err)) {
        if (isset($_POST['edit_id'])) {
            // Update existing user
            if (!empty($password)) {
                $sql = "UPDATE users SET username = ?, email = ?, password = ?, role = ? WHERE uid = ?";
                $param_password = password_hash($password, PASSWORD_DEFAULT);
            } else {
                $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE uid = ?";
            }
            
            if ($stmt = mysqli_prepare($conn, $sql)) {
                if (!empty($password)) {
                    mysqli_stmt_bind_param($stmt, "ssssi", $username, $email, $param_password, $role, $_POST['edit_id']);
                } else {
                    mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $role, $_POST['edit_id']);
                }
                
                if (mysqli_stmt_execute($stmt)) {
                    header("location: users.php");
                    exit;
                } else {
                    echo "Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            // Insert new user
            $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                $param_password = password_hash($password, PASSWORD_DEFAULT);
                mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $param_password, $role);
                if (mysqli_stmt_execute($stmt)) {
                    header("location: users.php");
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
    // Prevent deleting self
    if ($_GET['delete'] != $_SESSION['user_id']) {
        $sql = "DELETE FROM users WHERE uid = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $_GET['delete']);
            if (mysqli_stmt_execute($stmt)) {
                header("location: users.php");
                exit;
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    $sql = "SELECT * FROM users WHERE uid = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_GET['edit']);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $username = $row['username'];
                $email = $row['email'];
                $role = $row['role'];
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all users
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container">
    <h1 class="mb-4">Manage Users</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3><?php echo isset($_GET['edit']) ? 'Edit User' : 'Add New User'; ?></h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <?php if(isset($_GET['edit'])): ?>
                            <input type="hidden" name="edit_id" value="<?php echo $_GET['edit']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                            <span class="invalid-feedback"><?php echo $username_err; ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                            <span class="invalid-feedback"><?php echo $email_err; ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password <?php echo isset($_GET['edit']) ? '(leave blank to keep current)' : ''; ?></label>
                            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                            <span class="invalid-feedback"><?php echo $password_err; ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-control <?php echo (!empty($role_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Select Role</option>
                                <option value="user" <?php echo ($role === 'user') ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo ($role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <span class="invalid-feedback"><?php echo $role_err; ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <input type="submit" class="btn btn-primary" value="<?php echo isset($_GET['edit']) ? 'Update User' : 'Add User'; ?>">
                            <?php if(isset($_GET['edit'])): ?>
                                <a href="users.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>User List</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                                        echo "<td>";
                                        if ($row['uid'] != $_SESSION['user_id']) {
                                            echo "<a href='users.php?edit=" . $row['uid'] . "' class='btn btn-sm btn-primary'>Edit</a> ";
                                            echo "<a href='users.php?delete=" . $row['uid'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>";
                                        } else {
                                            echo "<span class='text-muted'>Current User</span>";
                                        }
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No users found</td></tr>";
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