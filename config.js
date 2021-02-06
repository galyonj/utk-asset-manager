/**
 * Gulp configuration file.
 */

// get our package.json file so that we can use the
// data there to save on repeating ourselves as well as
// maintain consistency and avoid errors.
const pkg = require( './package.json' );

/**
 * BrowserSync config
 * 1. projectURL is set to match the URL to which our local dev
 *    site is accessible. Change it to fit your environment
 * 2. productURL should resolve to the project root
 */
const projectURL = 'localhost:8080';
const productURL = './';
const browserAutoOpen = false;
const injectChanges = true;

/**
 * CSS config
 * 1. cssSrc and cssDest are self-explanatory, interpolating the
 *    package name from pkg.json into the directory structure for each
 * 2. outputStyle is limited to two choices: 'compressed', and 'expanded'
 * 3. errLogToConsole helps us with debugging
 */
const cssSrc = `/_dev/css/${ pkg.name }.scss`;
const cssDest = `${ pkg.name }/assets/css/`;
const outputStyle = 'compressed';
const errLogToConsole = true;

// Export all our settings so they're available to gulp.
module.exports = {
	projectURL,
	productURL,
	browserAutoOpen,
	injectChanges,
	cssSrc,
	cssDest,
	outputStyle,
	errLogToConsole,
};
