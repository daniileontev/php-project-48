install:
	composer install
validate:
	composer validate
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src tests
test:
	composer exec --verbose phpunit tests
	#composer exec --verbose vendor/bin/phpstan analyse -l 6 src tests
gendiff:
	./bin/gendiff -h