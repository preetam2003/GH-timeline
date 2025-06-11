#!/bin/bash

CRON_JOB="*/5 * * * * /usr/bin/php "`pwd`"/src/cron.php >/dev/null 2>&1"
(crontab -l 2>/dev/null | grep -v "cron.php"; echo "$CRON_JOB") | crontab -
echo "CRON job set up successfully to run every 5 minutes"