'use strict';
/**
 *
 */

var gulp = require('gulp');
var cp = require('child_process');

function exec(cmd) {
    cp.exec(cmd, function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
    });
}

gulp.task('reset', function() {
    exec('php artisan migrate:refresh --seed --seeder DevelopmentSeeder');
});
