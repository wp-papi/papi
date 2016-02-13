all: css js

css:
	node_modules/.bin/node-sass -o dist/css/ --source-map dist/css/style.css.map src/assets/scss/style.scss > dist/css/style.css
	node_modules/.bin/autoprefixer-cli -b "last 2 version" dist/css/style.css dist/css/style.css
	cat src/assets/css/components/*.css dist/css/style.css > dist/css/style.min.css
	node_modules/.bin/csso dist/css/style.min.css dist/css/style.min.css
	echo "\n/*# sourceMappingURL=style.min.css.map */" >> dist/css/style.min.css
	mv dist/css/style.css.map dist/css/style.min.css.map
	rm dist/css/style.css

deps:
	composer install
	npm install
	brew install gettext
	brew link --force gettext

js:
	node_modules/.bin/webpack

lint:
	node_modules/.bin/eslint src/assets/js/packages/papi/**/*.js

phpcpd:
	vendor/bin/phpcpd --min-lines 5 --min-tokens 70 --exclude false --names "*.php" src/

phpcs:
	vendor/bin/phpcs -s --extensions=php --standard=phpcs.xml src/

pot:
	xgettext --language=php \
           --add-comments=L10N \
           --keyword=__ \
           --keyword=_e \
           --keyword=_n:1,2 \
           --keyword=_x:1,2c \
           --keyword=_ex:1,2c \
           --keyword=_nx:4c,1,2 \
           --keyword=esc_attr_ \
           --keyword=esc_attr_e \
           --keyword=esc_attr_x:1,2c \
           --keyword=esc_html_ \
           --keyword=esc_html_e \
           --keyword=esc_html_x:1,2c \
           --keyword=_n_noop:1,2 \
           --keyword=_nx_noop:3c,1,2 \
           --keyword=__ngettext_noop:1,2 \
           --package-name=papi \
           --from-code=UTF-8 \
           --output=languages/papi.pot \
           src/**/*.php
	# Add Poedit information to the template file.
	cat languages/papi.pot|perl -pe 's/8bit\\n/8bit\\n\"\n\"X-Poedit-Basepath:\
	..\\n"\n"X-Poedit-SourceCharset: UTF-8\\n"\n\
	"X-Poedit-KeywordsList: __;_e;_n:1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;esc_attr__;esc_attr_e;esc_attr_x:1,2c;esc_html__;esc_html_e;esc_html_x:1,2c;_n_noop:1,2;_nx_noop:3c,1,2;__ngettext_noop:1,2\\n"\n\
	"X-Poedit-SearchPath-0: .\\n"\n\
	"X-Poedit-SearchPathExcluded-0: *.js\\n"\n"Plural-Forms: nplurals=2; plural=(n != 1);\\n\\n/g'|tail -n +7|tee languages/papi.pot >/dev/null 2>/dev/null

watch:
	node_modules/.bin/parallelshell "make watch:css" "make watch:js"

watch\:css:
	node_modules/.bin/watch 'make css' src/assets/scss

watch\:js:
	node_modules/.bin/watch 'make js' src/assets/js/packages/papi
