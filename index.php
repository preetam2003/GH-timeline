<?php require_once 'functions.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>GitHub Updates Subscription</title>
</head>
<body>
    <h1>Subscribe to GitHub Updates</h1>
    
    <form method="POST">
        <input type="email" name="email" required>
        <button id="submit-email">Submit</button>
    </form>
    
    <form method="POST">
        <input type="hidden" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        <input type="text" name="verification_code" maxlength="6" required>
        <button id="submit-verification">Verify</button>
    </form>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['email']) && !isset($_POST['verification_code'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                registerEmail($email);
                echo "<p>Verification code sent to $email</p>";
            } else {
                echo "<p>Invalid email address</p>";
            }
        } elseif (isset($_POST['email'], $_POST['verification_code'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $code = filter_var($_POST['verification_code'], FILTER_SANITIZE_STRING);
            
            $codes = file(__DIR__ . '/verification_codes.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $valid = false;
            
            foreach ($codes as $line) {
                list($storedEmail, $storedCode) = explode('|', $line);
                if ($storedEmail === $email && $storedCode === $code) {
                    $valid = true;
                    break;
                }
            }
            
            if ($valid) {
                file_put_contents(__DIR__ . '/registered_emails.txt', "$email|1\n", FILE_APPEND);
                echo "<p>Email verified! You'll now receive GitHub updates.</p>";
            } else {
                echo "<p>Invalid verification code</p>";
            }
        }
    }
    ?>
</body>
</html>