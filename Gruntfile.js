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
		    transifex: 'tx pull -a',

		    // Create a ZIP file
		    zip: {
			    cmd: function ( filename = 'edd-gravity-forms' ) {

				    // First, create the full archive
				    var command = 'git-archive-all edd-gravity-forms.zip &&';

				    command += 'unzip -o edd-gravity-forms.zip &&';

				    command += 'zip -r ../' + filename + '.zip "edd-gravity-forms" &&';

				    command += 'rm -rf edd-gravity-forms/ && rm -f edd-gravity-forms.zip';

				    return command;
			    }
		    }
	    },
	    // Add textdomain to all strings, and modify existing textdomains in included packages.
	    addtextdomain: {
		    options: {
			    textdomain: 'edd-gf',    // Project text domain.
			    updateDomains: [ 'edd_sl', 'edd', 'gravityforms' ]  // List of text domains to replace.
		    },
		    target: {
			    files: {
				    src: [
					    '*.php',
					    '**/*.php',
					    '!node_modules/**',
					    '!tests/**',
					    '!tmp/**'
				    ]
			    }
		    }
	    }
    });

    // Load the plugin(s).
    grunt.loadNpmTasks("grunt-contrib-jshint");
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-exec');
	grunt.loadNpmTasks('grunt-potomo');

    // Task(s).
    grunt.registerTask('default', [ 'uglify', 'watch' ]);

	// Task(s).
	grunt.registerTask('translate', [ 'exec:transifex', 'potomo' ]);
};