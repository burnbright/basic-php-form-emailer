<?php
/**
 * Easily submit form data as email
 * @author Jeremy Shipman
 */
class FormMailer{
	
	protected $fields = array(
		"Name" => array(
			'required' => true
		),
		"Email" => array(
			'required' => true	
		),
		"Phone",
		"Message" => array(
			'required' => true	
		)
	);
	
	protected $data = array(), $to = null, $error = null, $captcha = null;
	protected $subject = "Website message";
	
	function __construct($data = null, $fields = null){
		$this->data = $data ? $data : $_REQUEST;
	}
	
	static function create($email, $data = null, $fields = null){
		$mailer = new FormMailer($data, $fields);
		$mailer->setEmailTo($email);
		return $mailer;
	}
	
	function setFields($fields){
		$this->fields = $fields;
		return $this;
	}
	
	function setData($data){
		$this->data = $data;
		return $this;
	}
	
	function setEmailTo($email){
		$this->to = $email;
		return $this;
	}
	
	function setCaptcha(Captcha $c){
		$this->captcha = $c;
	}
	
	function getError(){
		return $this->error;
	}
	
	/*
	 * Validate and send data
	 */
	function validateandsend(){
		if($this->validate()){
			return $this->emailsubmission();
		}
		return false;
	}
	
	/*
	 * Check if all the data is entered correctly
	 */
	function validate(){
		foreach($this->fields as $name => $config){
			if(is_array($config)){
				if(isset($config['required']) && $config['required'] && (!isset($this->data[$name]) || empty($this->data[$name]))){
					$this->error("The '{$name}' field is required, please fill it out.");
					return false;
				}
			}
		}
		if($this->captcha){
			if($this->captcha->validate()){
				return true;
			}
			$this->error("The spam captcha failed: ".$this->captcha->getError());
			return false;
		}
		return true;
	}
	
	function emailsubmission(){
		$message = $this->apply_template('emailtemplate.php', $this->data);
		$headers = 'X-Mailer: PHP/' . phpversion();
		if(isset($this->data['Email'])){
			$headers = 'From: '.$this->data['Email'] . "\r\n" . $headers;
		}
		return mail($this->to, $this->subject, $message,$headers);
	}
	
	/*
	 * Set an error message
	 */
	protected function error($error){
		$this->error = $error;
	}
	
	/**
	 * Execute a PHP template file and return the result as a string.
	 */
	protected function apply_template($tpl_file, $vars = array(), $include_globals = false){
	  extract($vars);
	  if ($include_globals) extract($GLOBALS, EXTR_SKIP);
	  ob_start();
	  require($tpl_file);
	  $applied_template = ob_get_contents();
	  ob_end_clean();
	  return $applied_template;
	}
	
}

abstract class Captcha{
	
	protected $error;
	
	abstract function validate();
	
	protected function error($message){
		$this->error = $message;
	}
	
	function getError(){
		return $this->error;
	}
	
}

class Recaptcha extends Captcha{
	
	protected $privatekey, $publickey;
	
	function __construct($public, $private){
		$this->publickey = $public;
		$this->privatekey = $private;
	}
	
	function validate(){
		
		require_once('recaptchalib.php');
		$resp = null;
		$error = null;
		# was there a reCAPTCHA response?
		if ($_REQUEST["recaptcha_response_field"]) {
			$resp = recaptcha_check_answer ($this->privatekey, $_SERVER["REMOTE_ADDR"], $_REQUEST["recaptcha_challenge_field"], $_REQUEST["recaptcha_response_field"]);
			if ($resp->is_valid) {
				return true;
			}
			$this->error($resp->error);
			return false;
		}
		$this->error("No catcha string was entered");
		return false;
	}
	
}