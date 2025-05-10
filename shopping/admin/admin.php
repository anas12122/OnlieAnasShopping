<?php
session_start();
include('../include/connected.php');
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <!-- إضافة رابط Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        /* إضافة الخلفية */
        body {
            
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
        }

        /* تنسيق النموذج */
        .form_container {
            background-color: rgba(255, 255, 255, 0.8); /* خلفية نصف شفافة */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            margin: 100px auto;
        }

        .form_container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form_container label {
            color: #333;
        }

        .form_container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form_container button {
            width: 100%;
            padding: 10px;
            background-color: #0f74f5;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .form_container button:hover {
            background-color: #0c5bc0;
        }
    </style>
</head>
<body>
    <main class="form">
        <?php
        if(isset($_POST['add'])) {
            $ADemail = $_POST['email'];
            $ADpassword = $_POST['password'];
            
            if(empty($ADemail) || empty($ADpassword)) {
               echo '<script>alert("الرجاء ادخال كلمة السر والبريد الالكتروني");</script>';
            }
            else {
                $query = "SELECT * FROM admin WHERE EMAIL='$ADemail' AND password='$ADpassword'";
                $result = mysqli_query($conn, $query);
                
                if(mysqli_num_rows($result) == 1) {
                    $_SESSION['EMAIL'] = $ADemail;
                    echo '<script>alert("مرحبا بك ايها المدير سوف يتم تحويلك الى لوحة التحكم");</script>';
                    header("REFRESH:2;URL=adminpanel.php");
                    exit();
                }
                else {
                    echo '<script>alert("مرحبا بك سوف يتم تحويلك الى المتجر الخاص بك");</script>';
                    header("REFRESH:2;URL=../index.php");
                    exit();
                }
            }
        }
        ?>
        <div class="form_container">
            <h1>تسجيل الدخول</h1>
            <form action="admin.php" method="post">
                <label for="em">البريد الالكتروني</label>
                <input type="email" name="email" id="em" required>
                
                <label for="pass">كلمة السر</label>
                <input type="password" name="password" id="pass" required>
                
                <button type="submit" name="add">تسجيل الدخول</button>
            </form>
        </div>
    </main>

    <!-- إضافة رابط إلى JavaScript الخاص بـ Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>