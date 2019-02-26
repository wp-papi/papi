all: css js

css:
	node_modules/.bin/node-sass -o dist/css/ --source-map dist/css/style.css.map src/assets/scss/style.scss > dist/css/style.css
	node_modules/.bin/autoprefixer-cli -b "last 2 version" dist/css/style.css dist/css/style.css
	node_modules/.bin/csso dist/css/style.css dist/css/style.min.css
	echo "\n/*# sourceMappingURL=style.min.css.map */" >> dist/css/style.min.css
	mv dist/css/style.css.map dist/css/style.min.css.map
	rm dist/css/style.css

deps:
	composer install
	npm install -g yarn wp-pot-cli
	yarn

js:
	node_modules/.bin/webpack

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
	node_modules/.bin/concurrently "make watch:css" "make watch:js"

watch\:css:
	node_modules/.bin/watch 'make css' src/assets/scss

watch\:js:
	node_modules/.bin/watch 'make js' src/assets/js
