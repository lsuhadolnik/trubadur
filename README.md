# Trubadur
Trubadur - quiz-like music learning web application.


# How to deploy a local test environment
- You can use Laradock (a docker-compose project that sets up everithing you need in a few lines)
- Set up MySQL
	- Create a new user trubadur with password Trubadur!123
	- Create a new database named trubadur and grant all privileges to trubadur user
- Set up NginX
- Set up PHP

- Clone Git repo

- run `composer install` and wait...
- run `artisan key:generate` and copy the string that starts with base64: and ends with =
- create the .env file
```text
DB_HOST=mysql
REDIS_HOST=redis
QUEUE_HOST=beanstalkd

DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=trubadur
DB_USERNAME=trubadur
DB_PASSWORD=Trubadur!123

SOUNDFONT="/usr/share/sounds/sf2/Fluid.sf2"

TELEGRAM_API_KEY=
TELEGRAM_CHAT_ID=

APP_DEBUG=true
APP_KEY=

```
- paste the value (so it looks like APP_KEY=base64:.......=)

- then `npm install`
 
- Run `apt install ffmpeg fluidsynth`

- `mkdir storage/midi`
- `chmod -R 777 storage`
