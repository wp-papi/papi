all: css js

css:
	npm run css

deps:
	composer install
	npm install -g yarn wp-pot-cli
	yarn

js:
	npm run js

lint:
	make lint:js
	make lint:php

lint\:js:
	node_modules/.bin/eslint src/assets/js/*.js src/assets/js/properties/*.js

lint\:php:
	vendor/bin/phpcs -s --extensions=php --standard=phpcs.xml src/

pot:
	wp-pot --src 'src/**/*.php' --dest-file languages/papi.pot --package papi

watch:
	npm run watch

watch\:css:
	npm run css

watch\:js:
	npm run js
