/**
 * Gulpfile
 * Copyright (c) 2015 Fredrik Forsmo & Johnie Hjelm
 * Autoprefixer, Sass, Uglify, Header, Cssmin etc
 */

/*-------------------------------------------------------------------

Required plugins

-------------------------------------------------------------------*/

var gulp         = require('gulp');
var sass         = require('gulp-sass');
var sourcemaps   = require('gulp-sourcemaps');
var cssmin       = require('gulp-cssmin');
var uglify       = require('gulp-uglify');
var concat       = require('gulp-concat');
var header       = require('gulp-header');
var autoprefixer = require('gulp-autoprefixer');
var eslint       = require('gulp-eslint');
var browserify   = require('browserify');
var babelify     = require('babelify');
var source       = require('vinyl-source-stream');
var pkg          = require('./package.json');

/*-------------------------------------------------------------------

Config

-------------------------------------------------------------------*/
var src = './src/assets/';
var dist = './dist/';
var config = {
  sass: {
    src: src + 'scss/**/*.{sass,scss}',
    dest: dist + 'css/',
    settings: {
      sourceComments: 'map',
      imagePath: '/images' // Used by the image-url helper
    }
  },
  scripts: {
    main: src + 'js/main.js',
    babel: src + 'js/packages/',
    files: [
      src + 'js/components/*.js',
      dist + 'js/*.js'
    ],
    dest: dist + 'js/'
  }
};

/*-------------------------------------------------------------------

Banner using meta data from package.json

-------------------------------------------------------------------*/

var banner = [
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


/*-------------------------------------------------------------------

Tasks

-------------------------------------------------------------------*/

// Sass
gulp.task('sass', function() {
  return gulp.src([config.sass.src, src + 'css/components/*.css'])
    .pipe(concat(
      'style.min.css'
    ))
    .pipe(sourcemaps.init())
    .pipe(sass(config.sass.settings))
    .pipe(sourcemaps.write())
    .pipe(autoprefixer({ browsers: ['last 2 version'] }))
    .pipe(cssmin())
    .pipe(header(banner, {
      package: pkg
    }))
    .pipe(gulp.dest(config.sass.dest));
});

// ES6 with Babel
gulp.task('es6to5', function(cb) {
  return browserify({
    entries: config.scripts.main,
    debug: true
  })
  .transform(babelify.configure({
    resolveModuleSource: function (f, p) {
      var parts = f.split('/');
      var file  = parts.pop();
      var first = parts.shift();
      var path  = parts.join('/');

      path = (path.length ? path + '/' : '');

      if (!first) {
        first = file;
      }

      return require('path').join(__dirname, config.scripts.babel, first, path, file);
    }
  }))
  .bundle()
  .pipe(source('main.min.js'))
  .pipe(gulp.dest('./dist/js'));
});

// ES5 Scripts
gulp.task('scripts', ['es6to5'], function() {
  return gulp.src(config.scripts.files)
    .pipe(concat(
      'main.min.js'
    ))
    .pipe(uglify())
    .pipe(header(banner, {
      package: pkg
    }))
    .pipe(gulp.dest(config.scripts.dest));
});

// Lint using Eslint
gulp.task('lint', function () {
  return gulp.src(['src/assets/js/**/*.js'])
    .pipe(eslint())
    .pipe(eslint.format())
    .pipe(eslint.failOnError());
});


// Watch
gulp.task('watch', function() {
  gulp.watch(config.sass.src,       ['sass']);
  gulp.watch(config.scripts.files,  ['scripts']);
});

// Build
gulp.task('build', function () {
  gulp.start(
    'sass',
    'scripts'
  );
});

// Default
gulp.task('default', function () {
  gulp.start(
    'watch',
    'sass',
    'scripts'
  );
});

/**
 * Keep on kicking ass Fredik! ;)
 * – The Crip
 */
