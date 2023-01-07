<?php
require("../../../connection.php");


session_start();

if (!$_SESSION['ofc_add']) {
    header("Location: ../../../index.php");
}

// รับ id ผู้ให้เช่า
$id = $_GET["idsub"];

// session id admin
$ad_id = $_SESSION['ofc_add'];

// เปลียนสถานะผู้ให้เช่า 1
$sql = "UPDATE lessor SET ls_status_use ='1', 
               ad_id ='$ad_id',
               ls_status_show = '1' 
               WHERE ls_id = $id";
$result = mysqli_query($conn, $sql);

//เรียก ข้อมูลผู้ให้เช่า
$sql_ch = "SELECT * FROM lessor WHERE ls_id= $id ";
$result_ch = mysqli_query($conn, $sql_ch);
$row_ch = mysqli_fetch_assoc($result_ch);

//email ไปหาผู้ให้เช่า
require_once "../../../16_func_email_sms/func_email.php";
$email = $row_ch['ls_email'];
$header = "แจ้งเตือน การอนุมัติเข้าใช้งานระบบ";
$detail = "ผู้ดูแลระบบ อนุมัติการเข้างานระบบบริหารสัญญาเช่า เรียบร้อยแล้ว";
$send_email = sendEmail($email, $header, $detail);

//SMS ไปหาผู้ให้เช่า
require_once "../../../16_func_email_sms/func_sms.php";
$number = $row_ch['ls_tel'];
$send_sms = sendSMS($number, $detail);


if ($result) {
    echo "<script type='text/javascript'>";
    echo "alert('อนุมัติ สำเร็จ');";
    // เสร็จแล้วไปหน้า 08_1_admin_check_tn_register.php
    echo "window.location='08_1_admin_check_tn_register.php';";
    echo "</script>";
} else {
    echo "<script type='text/javascript'>";
    echo "alert('มีความผิดพลาด');";
    echo "window.location='08_1_admin_check_tn_register.php';";
    echo "</script>";
}
