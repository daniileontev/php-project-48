install:
	composer install
validate:
	composer validate
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src tests
autoload:
	composer dump-autoload
test:
	composer exec --verbose phpunit tests
test-coverage:
	composer exec XDEBUG_MODE=coverage --verbose phpunit tests -- --coverage-clover build/logs/clover.xml
gendiff:
	bin/gendiff -h