<?php

require_once('C:/xampp/htdocs/TCPDF/tcpdf_import.php');
require_once('C:/xampp/htdocs/TCPDF/tcpdf_barcodes_2d.php');

$name = $_GET["name"];
$englishname = $_GET["englishname"];
$phone = $_GET["phone"];
$ID = $_GET["ID"];
$address = $_GET["address"];
$email = $_GET["email"];
$num1 = $_GET["num1"];
$num2 = $_GET["num2"];
$num3 = $_GET["num3"];
$num4 = $_GET["num4"];
$num5 = $_GET["num5"];
$num6 = $_GET["num6"];
$num7 = $_GET["num7"];
$num8 = $_GET["num8"];
$num9 = $_GET["num9"];
$num10 = $_GET["num10"];
$num11 = $_GET["num11"];
$num12 = $_GET["num12"];
$sum = 1280*($num1+$num2+$num3+$num4+$num5+$num6+$num7+$num8+$num9+$num10+$num11+$num12);

if($num1+$num2+$num3+$num4+$num5+$num6+$num7+$num8+$num9+$num10+$num11+$num12 == 0)
{
echo '請至少選擇一件商品';
exit();
}

$html = <<<EOF
    <h4>訂單已完成</h4>
    <br></br>
    <br></br>
    <table border = "1">
    <tr>
        <td>姓名:</td>
        <td>$name</td>
        <td>English Name:</td>
        <td>$englishname</td>
    </tr>
 	  <tr>
        <td>電話:</td>
        <td>$phone</td>
        <td>身分證:</td>
        <td>$ID</td>
    </tr>
    <tr>
        <td>地址:</td>
        <td rowspan="1" colspan="3" color = "red">$address</td>
    </tr>
    <tr>
        <td>E-mail:</td>
        <td rowspan="1" colspan="3">$email</td>
    </tr>
    <tr>
        <td>旅行小包 (黑)</td>
        <td>$num1</td>
        <td>旅行小包 (藍)</td>
        <td>$num2</td>
    </tr>
   	<tr>
        <td>旅行小包 (卡其)</td>
        <td>$num3</td>
        <td>旅行小包 (土)</td>
        <td>$num4</td>
    </tr>
	  <tr>
        <td>旅行小包 (紫)</td>
        <td>$num5</td>
        <td>旅行小包 (橘)</td>
        <td>$num6</td>
    </tr>
    <tr>
        <td>旅行小包 (紅)</td>
        <td>$num7</td>
        <td>旅行小包 (藍綠)</td>
        <td>$num8</td>
    </tr>
    <tr>
        <td>旅行小包 (海藍)</td>
        <td>$num9</td>
        <td>旅行小包 (可可)</td>
        <td>$num10</td>
    </tr>
    <tr>
        <td>旅行小包 (軍綠)</td>
        <td>$num11</td>
        <td>旅行小包 (芥黃)</td>
        <td>$num12</td>
    </tr>
    <tr>
        <td rowspan= "1" colspan="2">總金額</td>
        <td rowspan= "1" colspan="2">$sum</td>
    </tr>	
	
    </table>
EOF;

$style = array(
    'border' => 2,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
);
$barcodeobj = new TCPDF2DBarcode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'QRCODE,M');
$file_name = "order.png";
// file_put_contents($file_png, $barcodeobj->getBarcodePngData());

/*---------------- Sent Mail Start -----------------*/
$to = $email;
$from = "kaiwei@porn.yzu";
$subject = "購物確認信";
$subject = "=?UTF-8?B?".base64_encode($subject)."?=";
$attach_filename = date("Y-m-d") . ".html";
$content = $barcodeobj->getBarcodePngData();
$encoded_content = chunk_split(base64_encode($content));
$body = $html;

$attachment = $encoded_content;

$boundary = uniqid("");

$headers = "From: $from
To: $to
Content-type: multipart/mixed; boundary=\"$boundary\"";

$body =  "--$boundary
Content-type: text/html; name=$attach_filename
Content-disposition: inline; filename=$attach_filename
Content-transfer-encoding: 8bit

$body

--$boundary
Content-Type: image/png; name=$file_name
Content-Disposition: attachment; filename=$file_name
Content-Transfer-Encoding: base64\r\n

$attachment

--$boundary--";

mail($to, $subject, $body, $headers);

/*---------------- Sent Mail End -------------------*/

/*---------------- Print PDF Start -----------------*/
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetFont('cid0jp','', 18); 
$pdf->AddPage();
/*---------------- Print PDF End -------------------*/
$pdf->writeHTML($html);
$pdf->lastPage();
$pdf->Output('order.pdf', 'I');

/*---------------- DATEBASE START -------------------*/
$host = 'localhost';
//改成你登入phpmyadmin帳號
$user = 'root';
//改成你登入phpmyadmin密碼
$passwd = '1109';
//資料庫名稱
$database = 'shopwebsite';
//實例化mysqli(資料庫路徑, 登入帳號, 登入密碼, 資料庫)
$connect = new mysqli($host, $user, $passwd, $database);
 
if ($connect->connect_error) {
    die("連線失敗: " . $connect->connect_error);
}
echo "連線成功";
 
//設定連線編碼，防止中文字亂碼
$connect->query("SET NAMES 'utf8'");
 
$insertSql = "INSERT INTO 
member (name, englishname, phone, ID, address, email, num1, num2, num3, num4, num5, num6, num7, num8, num9, num10, num11, num12, sum) 
VALUES ('$name', '$englishname', '$phone', '$ID', '$address', '$email', '$num1', '$num2', '$num3', '$num4', '$num5', '$num6', '$num7', '$num8', '$num9', '$num10', '$num11', '$num12', '$sum')";
//呼叫query方法(SQL語法)
$status = $connect->query($insertSql);
 
if ($status) {
    echo '新增成功';
} else {
    echo "錯誤: " . $insertSql . "<br>" . $connect->error;
}