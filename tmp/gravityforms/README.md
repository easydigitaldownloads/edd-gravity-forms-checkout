Gravity Forms
==============================

[![Build Status](https://travis-ci.com/gravityforms/gravityforms.svg?token=dWdigWFPjUjwVzDjbyxv&branch=master)](https://travis-ci.com/gravityforms/gravityforms)
[![CircleCI](https://circleci.com/gh/gravityforms/gravityforms.svg?style=svg&circle-token=7b7a144cca432551513421c180e6b395755b91df)](https://circleci.com/gh/gravityforms/gravityforms)

This repository contains the development version of Gravity Forms intended to facilitate communication with developers. It is not stable and not intended for installation on production sites.

## Installation Instructions
The only thing you need to do to get this development version working is clone this repository into your plugins directory and activate script debug mode. If you try to use this version without script mode on the scripts and styles will not load and it will not work properly.

To enable script debug mode just add the following line to your wp-config.php file:

define( 'SCRIPT_DEBUG', true );


## Unit Tests

The unit tests can be installed from the terminal using:

    bash tests/bin/install.sh [DB_NAME] [DB_USER] [DB_PASSWORD] [DB_HOST]


If you're using VVV you can use this command:

	bash tests/bin/install.sh wordpress_unit_tests root root localhost


## Developer Guidelines

### Version Numbering
Gravity Forms follows the version numbering scheme used by WordPress for public auto-update releases. e.g. 2.0.6

- https://make.wordpress.org/core/handbook/about/release-cycle/version-numbering/

Interim releases, which are available on the downloads page, use a fourth level to denote the patch version: e.g. 2.0.6.2

Alpha and beta versions use hyphens: e.g. 2.1.0-alpha-1, 2.1.0-beta-2


### Minimum Versions

Gravity Forms follows the WordPress minimum requirements, Currently: PHP 5.2.4+ and MySQL 5.0+

- https://wordpress.org/about/requirements/

Minimum version WordPress required: Defined by the GF_MIN_WP_VERSION constant. Only raised when it's absolutely necessary.

Minimum version WordPress for support: Defined by the GF_MIN_WP_VERSION_SUPPORT_TERMS. This is generally one major release back.

### Coding Standards

Gravity Forms follows the code style and standards for WordPress with a few exceptions such as Yoda conditions. Further details:

WordPress Coding standards:

- https://make.wordpress.org/core/handbook/best-practices/coding-standards/

Setting up Code Sniffer in PHPStorm:

- https://github.com/gravityforms/gravityforms/wiki/Coding-Standards-in-PHPStorm

### Documentation Standards

Gravity Forms follows a combination of the WordPress and PSR-5 documentation standards.  For further details on these, see the following:

Gravity Forms documentation standards:

- https://github.com/gravityforms/gravityforms/wiki/PHP-Documentation-Standards

WordPress documentation standards:

- https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/php/

PSR-5 documentation standards:

- https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md

### Hooks

Hooks are documented both inline and online. Naming convention:

- https://github.com/gravityforms/gravityforms/wiki/Naming-Convention-for-Hooks

### Security

Some background reading:

- https://github.com/gravityforms/gravityforms/wiki/Writing-Secure-Code
- https://www.gravityhelp.com/documentation/article/security/
- https://www.gravityhelp.com/security/

### Tests

Add unit tests and acceptance tests where appropriate to ensure that functionality doesn't break between releases.

- https://github.com/gravityforms/gravityforms/wiki/Acceptance-Testing

### Scripts and Styles

The repository doesn't contain minified scripts or styles. Minification is handled by the build processes.

Setting up minification in PHPStorm:

- https://github.com/gravityforms/gravityforms/wiki/Minification-in-PHPStorm

### Translations

Translations for selected locales are handled by a team of professional translators on transifex:

- https://www.transifex.com/rocketgenius/gravity-forms/

The .po files for the remaining locales can be found on the support site:

- https://www.gravityhelp.com/downloads/translations/

The .pot file is not committed to the repository as it's generated automatically during the build process. .po files are not committed to the repository and are not distributed with the build.

### The Gravity Forms Build Process

Travis CI runs on every commit and runs the unit tests on a matrix of PHP and WordPress versions. The result is posted to Slack.

- https://travis-ci.com/gravityforms

Send a direct message on the Rocketgenius Slack to Hal to run the build process on demand. Hal will generate the zip file and the md5 checksums, upload them to Dropbox and S3 and publish them to the downloads page.

- `build gravityforms` will upload the build to Dropbox and upload the MD5 checksums to S3 but it will not publish the build on the the downloads page.
- `publish gravityforms` will run the build process and make it available on the downloads page as an interim build (not available for auto-update).

Auto-update releases are currently pushed manually.

### Recommended tools

* PHPStorm
* XDebug
* VVV or Pressmatic

### Development Workflow

The master branch is always ready to be published.

Each new feature has its own branch with a descriptive name. The changes are then merged into the master branch when ready.

Small fixes can be made directly on the master branch. More complex fixes, or fixes that require review, will need either a pull request or a new branch.

All issues are managed and prioritised in the private Trello boards.

### Outside Collaborators

Bug reports or feature requests should be submitted via the support form:

- https://www.gravityhelp.com/request-support/

Security issues should be submitted via the contact form:

- https://www.gravityhelp.com/contact-us/


Pull requests are welcome. We will be reviewing them frequently, but can't guarantee a response time. We also can't guarantee that they will get approved. When submitting pull request, keep the following in mind:

- We love bug fixes! These are very helpful. Please let us know the steps to replicate the bug so we can properly test it.

- New hooks and filters are generally welcome. When adding new hooks, be certain to follow the hook naming conventions and add inline documentation for the hook; including a link to an example on how to use it. Make your hook as generic as possible so that it will be useful to other users as well.

- Be careful with adding new features or making major changes to existing features. We are critical of new features primarily because they have potential for creating bugs and adding support requests. We also believe in backwards compatibility, and any new feature will need to be backwards compatible. If you are planning to develop a new feature, we recommend reaching out to our team first to ensure you don't waste time developing a feature that won't get approved.
