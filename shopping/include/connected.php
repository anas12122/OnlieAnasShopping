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
