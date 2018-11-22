#!/bin/bash

timestamp=$(date '+%Y-%m-%d_%H:%M:%S')
mysqldump -u root trubadur_db > "/var/www/trubadur/backups/$timestamp.sql"
