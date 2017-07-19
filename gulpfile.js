var gulp = require('gulp');
var fs = require('fs');
var $    = require('gulp-load-plugins')();
var spawn = require('child_process').spawn;
var node;


function complileSass(main, libs, dest) {
  return gulp.src(main)
    .pipe($.sass({
      includePaths: libs,
      outputStyle: 'compressed'
    })
      .on('error', $.sass.logError))
    .pipe($.autoprefixer({
      browsers: ['last 2 versions', 'ie >= 9']
    }))
    .pipe(gulp.dest(dest));
}


function iterateSassPaths(rootpath) {
  var subdirs = fs.readdirSync(rootpath).filter((item) => {
    return fs.lstatSync(path.resolve(rootpath, item)).isDirectory() && !item.startsWith('.');
  });
  return subdirs;
}

exports.complileSass = complileSass;
exports.iterateSassPaths = iterateSassPaths;
