<?php
/**
 * Icon finder for "Icon Fonts" Wordpress plugin
 *
 * Builds a list of icons from the font stylesheets.
 * Also shows how many icons are in each font.
 * The JSON file is used by the icon dialog.
 */
 
// Exit if accessed directly. Remove this before running script!
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

$total = 0;
$icons = array();
$fonts = array(
	'dashicons' => array( 'path' => 'dashicons.css', 'prefix' => 'dashicons-' ),
	'elegant' => array( 'path' => '../elegant/style.css', 'prefix' => '' ),
	'elusive' => array( 'path' => '../elusive/css/elusive-icons.css', 'prefix' => 'el-' ),
	'entypo' => array( 'path' => '../entypo/entypo.css', 'prefix' => 'icon-' ),
	'font-awesome' => array( 'path' => '../font-awesome/css/font-awesome.css', 'prefix' => 'fa-' ),
	'foundation' => array( 'path' => '../foundation/foundation-icons.css', 'prefix' => 'fi-' ),
	'genericons' => array( 'path' => '../genericons/genericons.css', 'prefix' => 'genericon-' ),
	'icomoon-free' => array( 'path' => '../icomoon-free/style.css', 'prefix' => 'icon-' ),
	'ionicons' => array( 'path' => '../ionicons/css/ionicons.css', 'prefix' => 'ion-' ),
	'map-icons' => array( 'path' => '../map-icons/css/map-icons.css', 'prefix' => 'map-icon-' ),
	'material-design' => array( 'path' => '../material-design/css/materialdesignicons.css', 'prefix' => 'mdi-' ),
	'mfglabs' => array( 'path' => '../mfglabs/mfglabs_iconset.css', 'prefix' => 'icon-' ),
	'octicons' => array( 'path' => '../octicons/octicons.css', 'prefix' => 'octicon-' ),
	'open-iconic' => array( 'path' => '../open-iconic/css/open-iconic-bootstrap.css', 'prefix' => 'oi-' ),
	'openweb' => array( 'path' => '../openweb/css/openwebicons.css', 'prefix' => 'icon-' ),
	'sosa' => array( 'path' => '../sosa/style.css', 'prefix' => 'sosa-' ),
	'themify' => array( 'path' => '../themify/themify-icons.css', 'prefix' => 'ti-' ),
	'typicons' => array( 'path' => '../typicons/typicons.css', 'prefix' => 'typcn-' ),
);

/** 
 *	Comparison callback for sorting class names
 */
function compare_names($a, $b) {
	return $a['text'] > $b['text'];
}

foreach( $fonts as $name => $data ) {
	// Get class names from stylesheet
	$handle = fopen( $data['path'], "r" );
	$classes = array();
	$count = 0;
	while ( $line = fgets( $handle ) ) {
		$result = preg_match( '/\.(-?[_a-zA-Z]+[_a-zA-Z0-9-]*)::?before\s*\{/', $line, $matches );
		if ( $result !== false ) {
			if ( ! empty( $matches[1] ) ) {
				// Skip base classes
				if( 'typcn' == $matches[1] || 'dashicons-before' == $matches[1] ) {
					continue;
				}
				$class = array();
				$text = str_replace( $data['prefix'], '', $matches[1] );
				$text = str_replace( array( '-', '_' ), ' ', $text );
				$class['text'] = ucwords( $text );
				$class['value'] = $matches[1];
				$classes[] = $class;
				$count++;
			}
		}
	}
	fclose( $handle );
	usort( $classes, 'compare_names' );
	$icons[$name] = $classes;
	echo $name . ' ( ' . $count . ' icons )<br>'; 
	$total += $count;
}

//Print total count
echo '<br>Total Icons = ' . $total;

// Print names in JSON notation
$json = json_encode( $icons, JSON_PRETTY_PRINT );
echo '<pre>' . $json . '</pre>';

// Save names as JSON file
$handle = fopen( 'icon-names.json', 'w' );
if ( $handle ) {
	$json = json_encode( $icons );
	fwrite( $handle, $json );
}
fclose( $handle );