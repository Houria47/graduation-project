<?php

//Include required PHPMailer files
	require $libs . 'PHPMailer.php';
	require $libs . 'SMTP.php';
	require $libs . 'Exception.php';
//Define name spaces
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;



  function createMailer(){
    //Create instance of PHPMailer
    $mail = new PHPMailer(true);
    // Activate debug
    // $mail->SMTPDebug = 2
    //Set mailer to use smtp
    $mail->isSMTP();
    //Define smtp host
    // $mail->Host = "used host";
    //Enable smtp authentication
    $mail->SMTPAuth = true;
    //Set smtp encryption type (ssl/tls)
    // $mail->SMTPSecure = "tls";
    //Port to connect smtp
    // $mail->Port = "used post";
    //Set gmail username
    // $mail->Username = "used email";
    //Set gmail password
    // $mail->Password = "used password";
    //Set sender email
    // $mail->setFrom('used email', 'Resto Platform');

    return $mail;
  }

  function sendVerifyToken($destination, $token, $do){
    $res = false;
    try {
      $mail = createMailer();

    //Email subject
    $mail->Subject = "Confirm your account";
    //Enable HTML
    $mail->isHTML(true);
    //Attachment
    // $mail->addAttachment('img/attachment.png');
    //Email body
    $msg_template = "
    <div style='
      text-align: center;
      font-size: 20px;
      color: #2c2c54;
      border-radius: 10px;
      border: 1px solid #ccc;
      margin: 10px;
      padding: 0 10px 20px;
    '>
      <h2>Resto لقد قمت بتسجيل حساب بمنصة</h2>
      <h5>.لتأكيد الحساب استخدم الرابط أدناه</h5>
      <a style='color:#fc8621' href='http://localhost/5thProject/admin/verifyAccount.php?do=$do&token=$token'>تأكيد الحساب</a>
    </div> 
    ";
    $mail->Body = $msg_template;
    //Add recipient
    $mail->addAddress($destination);
    //Finally send email
    $res = $mail->send()?false:$mail->ErrorInfo;
    } catch (Exception $e) {
      $res = $e->getMessage() . "<br>PHPMailer Message: " . $mail->ErrorInfo;
    }
    //Closing smtp connection
    $mail->smtpClose();
    // return result
    return $res;
  }

  function sendRestuarantHasBeenVerified($destination, $restName){
    $res = false;

    try {
      $mail = createMailer();

      //Email subject
      $mail->Subject = "Your Restaurant has been Approved";
      //Enable HTML
      $mail->isHTML(true);
      //Attachment
      // $mail->addAttachment('img/attachment.png');
      //Email body
      $msg_template = "
      <div style='
        text-align: center;
        font-size: 20px;
        color: #2c2c54;
        border-radius: 10px;
        border: 1px solid #ccc;
        margin: 10px;
        padding: 0 10px 20px;
      '>
        <h2>Resto لقد تم قبول مطعمك بمنصة</h2>
        <h5>. بعد مراجعة البيانات والملفات التي أرفقتها مع حساب المطعم <em style='color:#fc8621'>$restName</em> الذي قمت بتسجيله في منصتنا تمت الموافقة عليه وتفعيله
        </h5>
        <h5>.لتستفيد من الخدمات التي توفرها المنصة يمكنك الآن تسجيل الدخول لحسابك عبر الرابط أدناه
        </h5>
        <a style='color:#fc8621' href='http://localhost/5thProject/admin/restaurant_account/restLogin.php'>
        تسجيل الدخول 
        </a>
      </div> 
      ";
      $mail->Body = $msg_template;
      //Add recipient
      $mail->addAddress($destination);
      //Finally send email
      $res = $mail->send()?false:$mail->ErrorInfo;

  } catch (Exception $e) {
    $res = $e->getMessage() . "<br>PHPMailer Message: " . $mail->ErrorInfo;
  }
    //Closing smtp connection
    $mail->smtpClose();
    // return result
    return $res;
  }