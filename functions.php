<?php

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $code = generateVerificationCode();
    sendVerificationEmail($email, $code);
    file_put_contents(__DIR__ . '/verification_codes.txt', "$email|$code\n", FILE_APPEND);
    return $code;
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $updated = [];
    
    foreach ($emails as $line) {
        list($storedEmail, $verified) = explode('|', $line);
        if ($storedEmail !== $email) {
            $updated[] = $line;
        }
    }
    
    file_put_contents($file, implode("\n", $updated));
}

function sendVerificationEmail($email, $code) {
    $subject = 'Your Verification Code';
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    mail($email, $subject, $message, $headers);
}

function fetchGitHubTimeline() {
    return file_get_contents('https://www.github.com/timeline');
}

function formatGitHubData($data) {
    // Simple parsing example - in real implementation you'd parse the actual timeline
    $html = '<h2>GitHub Timeline Updates</h2><table border="1"><tr><th>Event</th><th>User</th></tr>';
    $html .= '<tr><td>Push</td><td>testuser</td></tr>'; // Example data
    $html .= '</table>';
    return $html;
}

function sendGitHubUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    $data = fetchGitHubTimeline();
    $formatted = formatGitHubData($data);
    
    foreach ($emails as $line) {
        list($email, $verified) = explode('|', $line);
        if ($verified === '1') {
            $subject = 'Latest GitHub Updates';
            $unsubscribeUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/unsubscribe.php?email=" . urlencode($email);
            $message = $formatted . '<p><a href="' . $unsubscribeUrl . '" id="unsubscribe-button">Unsubscribe</a></p>';
            $headers = "From: no-reply@example.com\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            mail($email, $subject, $message, $headers);
        }
    }
}

?>