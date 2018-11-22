Periodic backups of MySQL database "trubadur_db" are performed every night at 03:00:00, when there's least traffic.

Modify using "crontab -e" command.
Current command:
0 3 * * * /var/www/trubadur/backups/backup-database.sh
