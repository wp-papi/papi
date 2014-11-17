module.exports = function(grunt) {

  // Load grunt tasks automatically
  require('load-grunt-tasks')(grunt);

  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    'sass': {
      dist: {
        files: [{
          expand: true,
          cwd: 'gui/scss',
          src: ['style.scss'],
          dest: 'gui/css/',
          ext: '.css'
        }]
      }
    },

    'autoprefixer': {
      style: {
        src: 'gui/css/style.css',
        dest: 'gui/css/style.css'
      }
    },

    'cssmin': {
      combine: {
        files: {
          'gui/css/style.css': ['gui/css/style.css']
        }
      }
    },

    'uglify': {
      main: {
        files: {
          'gui/js/main.js': [
            'gui/js/vendors/*.js',
            'gui/js/base.js',
            'gui/js/modules/*.js',
            'gui/js/views/*.js',
            'gui/js/properties/*.js',
            'gui/js/vendor.js',
            'gui/js/binds.js',
            'gui/js/init.js'
          ]
        }
      }
    },

    'watch': {
      scripts: {
        files: ['gui/scss/*', 'gui/scss/**/*', 'gui/js/*', 'gui/js/**/*'],
        tasks: ['sass', 'autoprefixer', 'cssmin', 'uglify'],
        options: {
          spawn: false
        },
      },
    }

  });

  grunt.registerTask('build', [
    'sass',
    'autoprefixer',
    'cssmin',
    'uglify'
  ]);

  grunt.registerTask('default', 'build');

};
