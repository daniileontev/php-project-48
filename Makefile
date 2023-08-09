install:
	composer install
validate:
	composer validate
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src tests
test:
	composer exec --verbose phpunit tests
	#composer exec --verbose vendor/bin/phpstan analyse -l 6 src tests
test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml
gendiff:
	./bin/gendiff -h	