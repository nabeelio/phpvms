#
# Before you go live, remember to change the APP_ENV to production
# and APP_DEBUG to false
#

APP_ENV=dev
APP_KEY=base64:{!! $app_key !!}
APP_DEBUG=true
APP_LOCALE=en

APP_LOG=daily
APP_LOG_LEVEL=debug
APP_LOG_MAX_FILES=3

APP_URL=http://localhost

DB_CONNECTION={!! $db_conn !!}
DB_HOST={!! $db_host !!}
DB_PORT={!! $db_port !!}
DB_DATABASE={!! $db_name !!}
DB_USERNAME={!! $db_user !!}
DB_PASSWORD={!! $db_pass !!}
DB_PREFIX=

CACHE_DRIVER=array
CACHE_PREFIX=

REDIS_HOST=localhost
REDIS_PASSWORD=
REDIS_PORT=6379
REDIS_DATABASE=1

SESSION_DRIVER=array
QUEUE_DRIVER=sync
