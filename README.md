# Basic PHP form emailer

Author: Jeremy Shipman <jeremy@burnbright.net>

This code allows you to define a group of fields to validate, and send in an email submission.
Support for the recapcha field is included.

## Files

 * `index.html` - contains the html form to submit.
 * `submitemail.php` - receives the form submission, and sends to the designated email address.
 * `emailtemplate.php` - contains the template used for the actual email that is sent.
 * `recaptchalib.php` & `FormMailer.php` - helper code for getting the above done.

## Setup

You can rename `index.html` to anything, such as contact.html, or even contact.php.

This is the code found in submitemail.php. Be sure to replace `youremail@example.com`
with the email address you want emails to be sent to.

```php
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
```

### Recapcha

First visit recaptca.com to get an account, plus a public & private key for the domain you want to use it on.
Next, add the public and private keys to the designated places in the code above.
Lastly, uncomment the entire line by removing the "//" at the start of the line.