<?php
require_once("FormMailer.php");
$mailer = FormMailer::create("youremail@example.com");
//$mailer->setCaptcha(new Recaptcha("PUBLICKEYHERE", "PRIVATEKEYHERE"));
if($mailer->validateandsend()){
	echo "Message was sent successfully";
}else{
	echo $mailer->getError();
}
?>