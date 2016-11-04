
api:
	docker-compose up -d api

apiaxle: redis api proxy axlesetup

axlesetup:
	docker-compose up -d axlesetup

bounce:
	docker-compose up -d web

clean:
	docker-compose kill
	docker-compose rm -f

composer:
	docker-compose run --rm composer

composerupdate:
	docker-compose run --rm composerupdate

phpunit:
	docker-compose run --rm phpunit

proxy:
	docker-compose up -d proxy

ps:
	docker-compose ps

redis:
	docker-compose up -d redis

rmapiaxle:
	docker-compose kill redis api proxy axlesetup
	docker-compose rm -f redis api proxy axlesetup

rmdb:
	docker-compose kill db
	docker-compose rm -f db

rmtestdb:
	docker-compose kill testdb
	docker-compose rm -f testdb

start: web

test: testunit

testunit: composer rmtestdb uptestdb yiimigratetestdb rmapiaxle apiaxle web phpunit

db:
	docker-compose up -d db

testdb:
	docker-compose up -d testdb

web: apiaxle updb composer yiimigrate
	docker-compose up -d web

yiimigrate:
	docker-compose run --rm yiimigrate

yiimigratetestdb:
	docker-compose run --rm yiimigratetestdb
