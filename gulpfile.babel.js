/**
 * Gulpfile
 * Copyright (c) 2015 Fredrik Forsmo & Johnie Hjelm
 * Autoprefixer, Sass, Uglify, Header, Cssmin etc
 */

import del from 'del';
import gulp from 'gulp';
import gutil from 'gulp-util';
import sass from 'gulp-sass';
import sourcemaps from 'gulp-sourcemaps';
import cssmin from 'gulp-cssmin';
import uglify from 'gulp-uglify';
import concat from 'gulp-concat';
import header from 'gulp-header';
import autoprefixer from 'gulp-autoprefixer';
import eslint from 'gulp-eslint';
import phpcs from 'gulp-phpcs';
import phpcpd from 'gulp-phpcpd';
import wpPot from 'gulp-wp-pot';
import sort from 'gulp-sort';
import plumber from 'gulp-plumber';
import pkg from './package.json';
import runSequence from 'run-sequence';
import webpack from 'webpack-stream';
import webpackconfig from './webpack.config.js';

/**
 * Config.
 */
const dist   = './dist/';
const src    = './src/';
const assets = src + 'assets/';
const config = {
  php: {
    src: src + '/**/*.php'
  },
  sass: {
    autoprefixer: {
      browsers: ['last 2 version']
    },
    entries: [
      assets + 'scss/**/*.scss',
      assets + 'css/components/*.css'
    ],
    dest: dist + 'css/',
    dist: 'style.min.css',
    settings: {
      sourceComments: 'map'
    },
    src: assets + 'scss/**/*.scss'
  },
  scripts: {
    components: [
      assets + 'js/components/*.js'
    ],
    entry: assets + 'js/main.js',
    lint: assets + 'js/**/*.js'
  }
};

/**
 * Banner using meta data from package.json.
 */
const banner = [
  '/*!\n' +
  ' * <%= package.name %>\n' +
  ' * <%= package.description %>\n' +
  ' * http://github.com/<%= package.homepage %>\n' +
  ' * @author <%= package.author %>\n' +
  ' * @version <%= package.version %>\n' +
  ' * Copyright ' + new Date().getFullYear() + '. <%= package.license %> licensed.\n' +
  ' */',
  '\n'
].join('');

/**
 * Clean task for [./dist/css]
 */
gulp.task('clean:before:css', cb => del([ `${dist}css/` ], {
  read: false,
  dot: true,
  force: true
}, cb));

/**
 * Clean task for [./dist/js]
 */
gulp.task('clean:before:js', cb => del([ `${dist}js/` ], {
  read: false,
  dot: true,
  force: true
}, cb));

/**
 * Component task.
 */
gulp.task('components', () => {
  gulp.src(config.scripts.components)
    .pipe(concat('components.js'))
    .pipe(uglify())
    .pipe(gulp.dest(`${dist}js`));
});

/**
 * Lint task using ESLint.
 */
gulp.task('lint', () => {
  gulp.src(config.scripts.lint)
    .pipe(eslint())
    .pipe(eslint.format())
    .pipe(eslint.failOnError());
});

/**
 * PHPCS task.
 */
gulp.task('phpcs', () => {
  gulp.src(config.php.src)
    .pipe(phpcs({
      bin: 'vendor/bin/phpcs',
      standard: 'phpcs.xml'
    }))
    .pipe(phpcs.reporter('log'));
});

/**
 * PHPCPD task.
 */
gulp.task('phpcpd', () => {
  gulp.src(config.php.src)
    .pipe(phpcpd({
      bin: 'vendor/bin/phpcpd'
    }));
});

gulp.task('pot', () => {
  gulp.src(config.php.src)
    .pipe(sort())
    .pipe(wpPot( {
        domain: 'papi',
        destFile:'papi.pot',
        package: 'papi'
    } ))
    .pipe(gulp.dest('languages'));
});

/**
 * Sass task.
 */
gulp.task('sass', () => {
  gulp.src(config.sass.entries)
    .pipe(concat(config.sass.dist))
    .pipe(sourcemaps.init())
    .pipe(sass(config.sass.settings))
    .pipe(autoprefixer(config.sass.autoprefixer))
    .pipe(cssmin())
    .pipe(header(banner, {
      package: pkg
    }))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(config.sass.dest));
});

/**
 * Scripts task.
 */
gulp.task('scripts', ['clean:before:js', 'components'], () => {
  gulp.src([config.scripts.entry])
    .pipe(plumber())
    .pipe(webpack(webpackconfig))
    .pipe(gulp.dest(`${dist}js`))
    .on('end', function () {
      gulp.src([`${dist}js/*.js`])
        .pipe(concat('main.min.js'))
        .pipe(header(banner, {
          package: pkg
        }))
        .pipe(gulp.dest(`${dist}js`));
    });
});

/**
 * Watch task.
 */
gulp.task('watch', () => {
  gulp.watch(config.sass.src, ['sass']);
  gulp.watch(config.scripts.lint, ['scripts']);
});

/**
 * Default task.
 */
gulp.task('default', ['clean:before:css', 'clean:before:js'], cb => {
  runSequence('sass', 'scripts', 'watch', cb);
});

/**
 * Build task.
 */
gulp.task('build', ['clean:before:css', 'clean:before:js'], cb => {
  runSequence('sass', 'scripts', cb);
});
