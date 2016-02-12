all: css scripts

css:
	node_modules/.bin/node-sass -o dist/css/ --source-map dist/css/style.css.map src/assets/scss/style.scss > dist/css/style.css
	node_modules/.bin/autoprefixer-cli -b "last 2 version" dist/css/style.css dist/css/style.css
	cat src/assets/css/components/*.css dist/css/style.css > dist/css/style.min.css
	node_modules/.bin/csso dist/css/style.min.css dist/css/style.min.css
	echo "\n/*# sourceMappingURL=style.min.css.map */" >> dist/css/style.min.css
	mv dist/css/style.css.map dist/css/style.min.css.map
	rm dist/css/style.css

lint:
	node_modules/.bin/eslint src/assets/js/packages/papi/**/*.js

phpcpd:
	vendor/bin/phpcpd --min-lines 5 --min-tokens 70 --exclude false --names "*.php" src/

phpcs:
	vendor/bin/phpcs -s --extensions=php --standard=phpcs.xml src/

scripts:
	cat src/assets/js/components/*.js > dist/js/components.js
	node_modules/.bin/webpack && cat dist/js/components.js dist/js/webpack.js > dist/js/main.min.js
	node_modules/.bin/uglifyjs dist/js/main.min.js -o dist/js/main.min.js --source-map main.min.js.map --source-map-root dist/js
	rm dist/js/components.js dist/js/webpack.js

watch:
	node_modules/.bin/watch "make all" src/assets/scss src/assets/js/packages/papi
