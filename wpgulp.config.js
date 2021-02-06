/**
 * WPGulp Configuration File
 *
 * 1. Edit the variables as per your project requirements.
 * 2. In paths you can add <<glob or array of globs>>.
 *
 * @package WPGulp
 */

// Project options.
const pkg = require( './package.json' );

const buildSrc = `./${ pkg.name }/`;
const buildDest = `./plugins/${ pkg.name }/`;

// Local project URL of your already running WordPress site.
// > Could be something like "wpgulp.local" or "localhost"
// > depending upon your local WordPress setup.
const projectURL = 'localhost:8080';

// Theme/Plugin URL. Leave it like it is; since our gulpfile.js lives in the root folder.
const productURL = './';
const browserAutoOpen = false;
const injectChanges = true;

// >>>>> Style options.
// Path to main .scss file.
const styleSRC = `./_dev/css/${ pkg.name }.scss`;

// Path to place the compiled CSS file. Default set to root folder.
const styleDestination = `${ pkg.name }/assets/css/`;

// Available options → 'compact' or 'compressed' or 'nested' or 'expanded'
const outputStyle = 'compressed';
const errLogToConsole = true;
const precision = 10;

// JS Vendor options.

// Path to JS vendor folder.
const jsVendorSRC = './_dev/js/vendor/*.js';

// Path to place the compiled JS vendors file.
const jsVendorDestination = `./${ pkg.name }/assets/js/`;

// Compiled JS vendors file name. Default set to vendors i.e. vendors.js.
const jsVendorFile = `${ pkg.name }-lib`;

// JS Custom options.

// Path to JS custom scripts folder.
const jsCustomSRC = './_dev/js/*.js';

// Path to place the compiled JS custom scripts file.
const jsCustomDestination = `./${ pkg.name }/assets/js/`;

// Compiled JS custom file name. Default set to custom i.e. custom.js.
const jsCustomFile = `${ pkg.name }`;

// Images options.

// Source folder of images which should be optimized and watched.
// > You can also specify types e.g. raw/**.{png,jpg,gif} in the glob.
const imgSRC = './_dev/img/raw/**/*';

// Destination folder of optimized images.
// > Must be different from the imagesSRC folder.
const imgDST = `./${ pkg.name }/assets/img/`;

// >>>>> Watch files paths.
// Path to all *.scss files inside css folder and inside them.
const watchStyles = './_dev/css/**/*.scss';

// Path to all vendor JS files.
const watchJsVendor = './_dev/js/vendor/*.js';

// Path to all custom JS files.
const watchJsCustom = './_dev/js/*.js';

// Path to project directory
const watchBuildDir = `./${ pkg.name }/**/*`;

// Path to all PHP files.
const watchPhp = `./${ pkg.name }/**/*.php`;

// >>>>> Zip file config.
const zipSRC = `./${ pkg.name }/`;
// Must have.zip at the end.
const zipName = `${ pkg.name }.${ pkg.version }.zip`;

// Must be a folder outside of the zip folder.
const zipDestination = './'; // Default: Parent folder.

// >>>>> Translation options.
// Your text domain here.
const textDomain = `${ pkg.name }`;

// Name of the translation file.
const translationFile = `${ pkg.name }.pot`;

// Where to save the translation files.
const translationDestination = `./${ pkg.name }/languages`;

// Package name.
const packageName = `${ pkg.name }`;

// Where can users report bugs.
const bugReport = `https://github.com/${ pkg.repository }/issues/`;

// Last translator Email ID.
const lastTranslator = `${ pkg.author }`;

// Team's Email ID.
const team = `${ pkg.author }`;

// Browsers you care about for auto-prefixing. Browserlist https://github.com/ai/browserslist
// The following list is set as per WordPress requirements. Though; Feel free to change.
const BROWSERS_LIST = [ 'last 2 version', '> 1%' ];

// Export.
module.exports = {
	buildSrc,
	buildDest,
	projectURL,
	productURL,
	browserAutoOpen,
	injectChanges,
	styleSRC,
	styleDestination,
	outputStyle,
	errLogToConsole,
	precision,
	jsVendorSRC,
	jsVendorDestination,
	jsVendorFile,
	jsCustomSRC,
	jsCustomDestination,
	jsCustomFile,
	imgSRC,
	imgDST,
	watchStyles,
	watchJsVendor,
	watchJsCustom,
	watchBuildDir,
	watchPhp,
	zipSRC,
	zipName,
	zipDestination,
	textDomain,
	translationFile,
	translationDestination,
	packageName,
	bugReport,
	lastTranslator,
	team,
	BROWSERS_LIST
};
