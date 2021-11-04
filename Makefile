migrate:
	@php -r 'include "database/migrations/users_migration.php";up();'
	@php -r 'include "database/migrations/tasks_migration.php";up();'

refresh:
	@php -r 'include "database/migrations/tasks_migration.php";down();'
	@php -r 'include "database/migrations/users_migration.php";down();'

seed:
	@php -r 'include "database/seeders/user_seeder.php";seed();'

test:
	composer exec --verbose phpunit tests -- --coverage-text

run:
	./bin/firstTestTask
