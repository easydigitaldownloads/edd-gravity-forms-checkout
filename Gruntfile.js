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

	    // Build translations without POEdit
	    makepot: {
		    target: {
			    options: {
				    mainFile: 'edd-gravity-forms.php',
				    type: 'wp-plugin',
				    domainPath: '/languages',
				    updateTimestamp: false,
				    exclude: [ 'node_modules/', 'node_modules/.*', 'assets/.*', 'docs/', 'tmp/.*', 'vendor/.*' ],
				    potHeaders: {
					    poedit: true,
					    'x-poedit-keywordslist': true
				    },
				    processPot: function( pot, options ) {
					    pot.headers['language'] = 'en_US';
					    pot.headers['language-team'] = 'Katz Web Services, Inc. <support@katz.co>';
					    pot.headers['last-translator'] = 'Katz Web Services, Inc. <support@katz.co>';
					    pot.headers['report-msgid-bugs-to'] = 'https://support.katz.co';

					    var translation,
						    excluded_meta = [
							    'Easy Digital Downloads - Gravity Forms Checkout',
							    'Integrate Gravity Forms purchases with Easy Digital Downloads',
							    'https://easydigitaldownloads.com/downloads/gravity-forms-checkout/',
							    'Katz Web Services, Inc.',
							    'https://katz.co'
						    ];

					    for ( translation in pot.translations[''] ) {
						    if ( 'undefined' !== typeof pot.translations[''][ translation ].comments.extracted ) {
							    if ( excluded_meta.indexOf( pot.translations[''][ translation ].msgid ) >= 0 ) {
								    console.log( 'Excluded meta: ' + pot.translations[''][ translation ].msgid );
								    delete pot.translations[''][ translation ];
							    }
						    }
					    }

					    return pot;
				    }
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
	grunt.loadNpmTasks('grunt-wp-i18n');

    // Task(s).
    grunt.registerTask('default', [ 'uglify', 'watch' ]);

	// Task(s).
	grunt.registerTask('translate', [ 'exec:transifex', 'potomo', 'addtextdomain', 'makepot' ]);
};