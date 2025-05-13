
To install dependencies using composer, run

`composer install`

To run basic service, navigate to the public folder and execute

`php -S localhost:8000`

To run test coverage from the commandline

`vendor/bin/phpunit -c phpunit.xml`

To run static analysis from the commandline

`vendor/phpstan/phpstan/phpstan analyze ./src/ ./tests/ --level=6`