# GH-timeline
A PHP-based email verification &amp; subscription system. Users register, verify email, and receive GitHub timeline updates via email every 5 mins using CRON. Includes email-based unsubscribe verification. Fully automated, file-based storage, no external libraries.

Implementation Notes:
File Structure:
All files are placed in the src/ directory
Uses registered_emails.txt for storing verified emails
Uses verification_codes.txt and unsubscribe_codes.txt for temporary code storage

Email Handling:
Uses PHP's mail() function
All emails are sent in HTML format as required
Includes unsubscribe links in GitHub update emails

CRON Job:
The setup_cron.sh script automatically configures the CRON job
Runs cron.php every 5 minutes
cron.php calls sendGitHubUpdatesToSubscribers()

Forms:
All forms are always visible as required
Follows the exact naming conventions for input fields and buttons
Includes both email submission and verification forms on the same page

Security:
Uses filter_var for input sanitization
Stores emails with verification status in text files
Requires verification codes for both subscription and unsubscription
