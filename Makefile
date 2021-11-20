migrate:
	./bin/migrate

refresh:
	./bin/migrate down
	./bin/migrate up
	./bin/seed up

seed:
	./bin/seed

test:
	composer exec --verbose phpunit tests -- --coverage-text

run:
	php -S localhost:8000
