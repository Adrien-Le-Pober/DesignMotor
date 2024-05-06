.PHONY: tests
tests:
	symfony php bin/console d:d:d --force --if-exists --env=test
	symfony php bin/console d:d:c --env=test
	symfony php bin/console d:m:m -n --env=test
	symfony php bin/console d:f:l -n --env=test
	symfony php bin/phpunit --testdox tests/