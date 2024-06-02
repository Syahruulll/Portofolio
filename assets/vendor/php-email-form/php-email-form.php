<?php
require 'path/to/PHPMailer/src/PHPMailer.php';
require 'path/to/PHPMailer/src/SMTP.php';
require 'path/to/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHP_Email_Form {
  public $to = '';
  public $from_name = '';
  public $from_email = '';
  public $subject = '';
  public $smtp = [];
  public $messages = [];
  public $ajax = false;

  public function add_message($message, $label, $priority = 0) {
    $this->messages[] = ['message' => $message, 'label' => $label, 'priority' => $priority];
  }

  public function send() {
    if(!empty($this->smtp)) {
      return $this->send_smtp();
    } else {
      return $this->send_mail();
    }
  }

  private function send_mail() {
    $headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
    $headers .= "Reply-To: " . $this->from_email . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $message = $this->build_message();

    return mail($this->to, $this->subject, $message, $headers);
  }

  private function send_smtp() {
    $host = $this->smtp['host'];
    $username = $this->smtp['username'];
    $password = $this->smtp['password'];
    $port = $this->smtp['port'];

    // Using PHPMailer for SMTP
    require 'path/to/PHPMailer/PHPMailerAutoload.php'; // Make sure PHPMailer is included

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = 'tls';
    $mail->Port = $port;

    $mail->setFrom($this->from_email, $this->from_name);
    $mail->addAddress($this->to);
    $mail->Subject = $this->subject;
    $mail->isHTML(true);
    $mail->Body = $this->build_message();

    if(!$mail->send()) {
      return 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
      return 'Message has been sent';
    }
  }

  private function build_message() {
    $message = '';
    foreach($this->messages as $msg) {
      $message .= '<p><strong>' . $msg['label'] . ':</strong> ' . $msg['message'] . '</p>';
    }
    return $message;
  }
}
?>
