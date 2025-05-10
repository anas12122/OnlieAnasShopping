<?php
$host="localhost";
$username="root";
$password="";
$dbname="shopping";
$conn=mysqli_connect($host,$username,$password,$dbname);
if(isset($conn)){
    echo"اتصال بقاعدة البيانات ناجحة";
}
else{
    echo"لم يتم اتصال بقاعدة البيانات بنجاح";

}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>الصفحة الرئيسية</title>
  <link rel="stylesheet" href="../style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    .header-nav-container {
      background-color:#E0F7FA;
    }
    .header-nav-container .social ul li a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 38px;
      height: 38px;
      background: #f1f1f1;
      border-radius: 50%;
      color: #333 !important;
      font-size: 20px;
      transition: background 0.3s, color 0.3s, transform 0.3s;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
      margin: 0 2px;
    }
    .header-nav-container .social ul li a:hover {
      background: #ebc60a;
      color: #fff !important;
      transform: scale(1.1) rotate(-8deg);
      box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    }
  </style>
</head>
<body>
  <div class="header-nav-container d-flex flex-wrap align-items-center justify-content-between py-3 px-3">
    <div class="logo d-flex align-items-center mb-2 mb-lg-0">
      <img src="image/images.jpg" alt="Logo" />
      <h1 class="ms-2">shopping</h1>
    </div>
    <div class="search mb-2 mb-lg-0">
      <form action="" method="get" class="d-flex">
        <input type="text" class="search_input" placeholder="ادخل كلمة البحث" />
        <button class="button_search ms-2" name="btn_search">بحث</button>
      </form>
    </div>
    <div class="section mb-2 mb-lg-0">
      <ul class="d-flex gap-3">
        <li><a href="index.html">الرئيسية</a></li>
        <li><a href="#">عطور</a></li>
        <li><a href="#">مجوهرات</a></li>
        <li><a href="#">الكترونيات</a></li>
        <li><a href="#">ملابس</a></li>
      </ul>
    </div>
    <div class="social mb-2 mb-lg-0">
      <ul class="d-flex gap-2">
        <li><a href="#"><i class="fa-brands fa-facebook"></i></a></li>
        <li><a href="#"><i class="fa-brands fa-square-instagram"></i></a></li>
        <li><a href="#"><i class="fa-brands fa-square-whatsapp"></i></a></li>
        <li><a href="#"><i class="fa-brands fa-linkedin"></i></a></li>
      </ul>
    </div>
    <div class="cart mb-2 mb-lg-0">
      <ul class="d-flex gap-2">
        <li><a href="./include/register.php"><i class="fa-solid fa-user-minus"></i></a></li>
        <li class="cart-icon">
          <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
          <span class="cart-count">1</span>
        </li>
      </ul>
    </div>
  </div>
  <!-- قسم المضاف حديثاً -->
  <div class="last-post">
    <div class="last-post-container">
      <h4>مضاف حديثًا</h4>
      <ul>
        <li><a href="#"><img src="image/shoes.jpg" alt="حذاء" /></a></li>
        <li><a href="#"><img src="image/perfume.jpg" alt="عطر" /></a></li>
        <li><a href="#"><img src="image/wristwatch.jpg" alt="ساعة يد" /></a></li>
      </ul>
    </div>
  </div>