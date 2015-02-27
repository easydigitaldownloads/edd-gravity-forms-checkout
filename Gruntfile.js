module.exports = function(grunt) {
    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        jshint: [
            "assets/js/admin.js"
        ],

        uglify: {
            dist: {
                files: {
                    'assets/js/admin.min.js': ['assets/js/admin.js']
                }
            }
        },

        watch: {
            scripts: {
                files: ['assets/js/*.js', '!assets/js/*.min.js'],
                tasks: ['jshint', 'uglify']
            },
        },

        dirs: {
            lang: 'languages'
        },

        // Convert the .po files to .mo files
        potomo: {
            dist: {
                options: {
                    poDel: false
                },
                files: [{
                    expand: true,
                    cwd: '<%= dirs.lang %>',
                    src: ['*.po'],
                    dest: '<%= dirs.lang %>',
                    ext: '.mo',
                    nonull: true
                }]
            }
        },

        // Pull in the latest translations
        exec: {
            // Create a ZIP file
            zip: 'python /usr/bin/git-archive-all ../edd-gravity-forms.zip'
        }
    });

    // Load the plugin(s).
    grunt.loadNpmTasks("grunt-contrib-jshint");
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-exec');

    // Task(s).
    grunt.registerTask('default', [ 'uglify', 'watch' ]);
};