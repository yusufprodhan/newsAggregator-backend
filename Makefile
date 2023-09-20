setup:
	@make build
	@make up
	@make composer-update
build:
	docker-compose build --no-cache --force-rm
stop:
	docker-compose stop
up:
	docker-compose up -d
composer-update:
	docker exec bash -c "composer update"
data:
	docker exec bash -c "php artisan migrate"
	docker exec -c "php artisan schedule:run"
