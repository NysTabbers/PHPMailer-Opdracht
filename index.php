<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$env = parse_ini_file(".env");

$email = $env["MAIL_EMAIL"];
$password = $env["MAIL_PASSWORD"];

//Load Composer's autoloader
require 'vendor/autoload.php';

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('mailer');
$log->pushHandler(new StreamHandler('info.log', Level::Info));

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth = true;                                   //Enable SMTP authentication
    $mail->Username = $email;                     //SMTP username
    $mail->Password = $password;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    $mail->Debugoutput = function ($str, $level) use ($log) {
        $log->info($str);
    };

    $mail->smtpConnect();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = $_POST['email'];

        $titel = $_POST['subject'];

        $inhoud = $_POST['inhoud'];

        //Recipients
        $mail->setFrom('nystabbers7@gmail.com', 'ROC Tilburg');
        $mail->addAddress($email);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $titel;
        $mail->Body = $inhoud;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        echo 'Message has been sent';

        header("Location: .");
    } else {
        echo 'Welcome, dingus';
    }

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="" method="post">
        <input type="email" name="email" placeholder="Verstuur naar emailadress">
        <input type="text" name="subject" placeholder="Titel">
        <input type="text" name="inhoud" placeholder="Inhoud">
        <input type="submit" value="do the thing">
    </form>
</body>

</html>