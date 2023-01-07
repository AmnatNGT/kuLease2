<?php

session_start();
require('../../../connection.php');

if (!$_SESSION['ofc_add']) {
	header("Location: ../../../index.php");
}

//ดึงวันที่ เวลา ปัจจุบัน
date_default_timezone_set('Asia/Bangkok');
$date = date("Y-m-d");
$time = date('H:i:s');

// รับ id สัญญที่เป็น session
$le_id = $_SESSION['le_id'];
// ชื่อรูป
$name = $_POST['name'];

// รูปลายเซน
$file = $_FILES['file_1'];
$fileName = $_FILES['file_1']['name'];
$fileTmpName = $_FILES['file_1']['tmp_name'];
$fileSize = $_FILES['file_1']['size'];
$fileError = $_FILES['file_1']['error'];
$fileType = $_FILES['file_1']['type'];

$fileEXT = explode('.', $fileName);
$fileActualExt = strtolower(end($fileEXT));

$allowed = array('jpeg', 'png', 'jpg');

if (in_array($fileActualExt, $allowed)) {
	if ($fileError === 0) {
		// ตั้งชื่อรูป 64003002192_tn_sign.png
		$fileNameNew = $name . '.' . $fileActualExt;
		$fileDestination = '../../../file_uploads/doc_signs/' . $fileNameNew;
		move_uploaded_file($fileTmpName, $fileDestination);

		// นำชื่อรูปเก็บไว้ที่ session
		$_SESSION['fullname2'] = $fileNameNew;

		//Set otp
		date_default_timezone_set('Asia/Bangkok');
		$date_otp = date("Y-m-d");
		$time_otp = date('H:i:s');

		//time otp end
		$a = strtotime("+5 minutes", strtotime($time_otp));
		$otp_time =  date('H:i:s', $a);

		//random otp
		$otp = mt_rand(10000, 99999);

		//update status_lease
		// ใส่ otp วันเวลาที่ otp หมดอายุ ของพยานผู้เช่าลงนาม
		$q7 = "UPDATE status_lease SET otp_t2='$otp', otp_date_tn2='$date_otp', otp_time_tn2='$otp_time' WHERE le_id = $le_id ";
		$result_q7 = mysqli_query($conn, $q7);


		//ดึงข้อมูล
		$sql = "SELECT * FROM status_lease stl, lease l, witness wn
        		        WHERE stl.le_id = $le_id
                		AND stl.le_id = l.le_id
                		AND l.le_sign_wn = wn.wn_id";
		$result = mysqli_query($conn, $sql);
		$row = mysqli_fetch_assoc($result);

		// email และ sms พยานผู้เช่า
		$tn_email = $row['wn_email'];
		$tn_tel = $row['wn_phone'];

		//send otp
		//eamil to พยานผู้เช่า
		require_once "../../../16_func_email_sms/func_email.php";
		$header = "แจ้งเตือน OTP";
		$send_email = sendEmail($tn_email, $header, $otp);

		//SMS to พยานผู้เช่า
		require_once "../../../16_func_email_sms/func_sms.php";
		$send_sms = sendSMS($tn_tel, "OTP >> $otp");

		echo "<script type='text/javascript'>";
		echo "alert('กรุณาตรวจสอบที่ SMS โทรศัพท์ หรือ Email พยานของผู้เช่า เพื่อยืนยัน OTP');";
		echo "window.location='06_confirm_otp.php';";
		echo "</script>";
	} else {
		echo "<script type='text/javascript'>";
		echo "alert('มีความผิดพลาด');";
		//echo "window.location='';";
		echo "</script>";
	}
} else {
	echo "You cannot upload files of this type!";
}
