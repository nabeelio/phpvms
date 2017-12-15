#
# Before you go live, remember to change the APP_ENV to production
# and APP_DEBUG to false
#

APP_ENV={!! $APP_ENV !!}
APP_URL="http://localhost"
APP_SKIN=default
VACENTRAL_API_KEY=""
APP_KEY="base64:{!! $APP_KEY !!}"
APP_DEBUG=true
APP_LOCALE=en

APP_LOG=daily
APP_LOG_LEVEL=debug
APP_LOG_MAX_FILES=3

DB_CONNECTION="{!! $DB_CONN !!}"
DB_HOST="{!! $DB_HOST !!}"
DB_PORT="{!! $DB_PORT !!}"
DB_DATABASE="{!! $DB_NAME !!}"
DB_USERNAME="{!! $DB_USER !!}"
DB_PASSWORD="{!! $DB_PASS !!}"
DB_PREFIX=""

MAIL_DRIVER=smtp
MAIL_FROM_ADDRESS="no-reply@phpvms.net"
MAIL_FROM_NAME="phpVMS Admin"
MAIL_HOST="smtp.mailgun.org"
MAIL_PORT=587
MAIL_ENCRYPTION="tls"
MAIL_USERNAME=""
MAIL_PASSWORD=""

CACHE_DRIVER="{!! $CACHE_DRIVER !!}"
CACHE_PREFIX="phpvms"

REDIS_HOST="localhost"
REDIS_PASSWORD=""
REDIS_PORT=6379
REDIS_DATABASE=1

SESSION_DRIVER=array
QUEUE_DRIVER={!! $QUEUE_DRIVER !!}
