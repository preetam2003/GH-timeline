<?php require_once 'functions.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe from GitHub Updates</title>
</head>
<body>
    <h1>Unsubscribe from GitHub Updates</h1>
    
    <form method="POST">
        <input type="email" name="unsubscribe_email" required>
        <button id="submit-unsubscribe">Unsubscribe</button>
    </form>
    
    <form method="POST">
        <input type="hidden" name="unsubscribe_email" value="<?= isset($_POST['unsubscribe_email']) ? htmlspecialchars($_POST['unsubscribe_email']) : '' ?>">
        <input type="text" name="unsubscribe_verification_code" maxlength="6" required>
        <button id="verify-unsubscribe">Verify</button>
    </form>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['unsubscribe_email']) && !isset($_POST['unsubscribe_verification_code'])) {
            $email = filter_var($_POST['unsubscribe_email'], FILTER_SANITIZE_EMAIL);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $code = generateVerificationCode();
                $subject = 'Confirm Unsubscription';
                $message = "<p>To confirm unsubscription, use this code: <strong>$code</strong></p>";
                $headers = "From: no-reply@example.com\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                
                mail($email, $subject, $message, $headers);
                file_put_contents(__DIR__ . '/unsubscribe_codes.txt', "$email|$code\n", FILE_APPEND);
                echo "<p>Confirmation code sent to $email</p>";
            } else {
                echo "<p>Invalid email address</p>";
            }
        } elseif (isset($_POST['unsubscribe_email'], $_POST['unsubscribe_verification_code'])) {
            $email = filter_var($_POST['unsubscribe_email'], FILTER_SANITIZE_EMAIL);
            $code = filter_var($_POST['unsubscribe_verification_code'], FILTER_SANITIZE_STRING);
            
            $codes = file(__DIR__ . '/unsubscribe_codes.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $valid = false;
            
            foreach ($codes as $line) {
                list($storedEmail, $storedCode) = explode('|', $line);
                if ($storedEmail === $email && $storedCode === $code) {
                    $valid = true;
                    break;
                }
            }
            
            if ($valid) {
                unsubscribeEmail($email);
                echo "<p>You have been unsubscribed from GitHub updates.</p>";
            } else {
                echo "<p>Invalid verification code</p>";
            }
        }
    }
    ?>
</body>
</html>