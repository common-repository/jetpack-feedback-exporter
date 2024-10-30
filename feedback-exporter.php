<?php
/**
 * @package Jetpack_Feedback_Exporter
 * @version 1.23
 */
/*
Plugin Name: Jetpack Feedback Exporter
Plugin URI: http://jtwventures.com/projects/jetpack-feedback-exporter
Description: This plugin allows users who have Jetpack installed to export their feedback data to excel or csv format.
Author: J. Tyler Wiest
Version: 1.23
Author URI: http://www.jtwventures.com
License: GPLv2
*/
/*  Copyright 2013  J. Tyler Wiest  (http://www.jtwventures.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Add Page to Admin Menu and Run Init Function
add_action( 'init', 'fbe_export_to_csv' );
add_action( 'admin_menu', 'fbe_admin_page');

// Grabs the subject if it exisits then genenerates 
// and exports the csv file
function fbe_export_to_csv() {
	if( isset($_POST['Export']) && $_POST['Export'] == 'Export' && isset($_POST['subject'])) {
		// Get List of Subjects if they exist
		if ( fbe_get_subjects() != false ) {
			$subject = strip_tags( $_POST['subject'] );
			$include_date = strip_tags ( $_POST['include_date'] );
			if( isset( $_POST['sep_val']) ) $sep_val = strip_tags( $_POST['sep_val'] );
			else $sep_val = ',';
			
			fbe_generate_csv( $subject, $include_date, $sep_val );
		}	

	}
}

// Generate the csv file given the user's selected subject
function fbe_generate_csv( $subject, $include_date, $sep_val ) {

	// Get all of the posts with the same subject
	global $wpdb;
	$query = $wpdb->get_results( "
		SELECT `post_id`, `meta_value`, p.`post_date`
		FROM  	`{$wpdb->postmeta}`
        INNER JOIN `{$wpdb->posts}` AS p ON `post_id` = p.`ID`
		WHERE 	`meta_key` 
		LIKE  	'_feedback_all_fields'
		AND 	`post_id` 
		IN (
				SELECT  `post_id` 
				FROM  `{$wpdb->postmeta}` 
				WHERE  `meta_value` 
				LIKE '" . $subject . "'
		)
        ORDER BY p.`post_date` DESC");
		
	// Create file Name and edit Header Information
	$filename = $subject . ".csv";

	header( 'Content-Description: File Transfer' );
	header( 'Content-Disposition: attachment; filename=' . $filename );
	header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );	
		
	// Get all of the fields from the associated posts/form submissions
	$printed_header = false; 
	$header = array();
	$results = array();

	// Loop tha gerenates CSV	
	foreach ( $query as $row ) {
		
		$line = array(); 
		$header_line = array();
		
		// Add Date field if requested and header hasn't been printed
		if( $include_date == 'yes' and $printed_header == false ) array_push( $header_line, 'Date');
		if( $include_date == 'yes' )  array_push( $line, $row->post_date );		

		// Split the meta value into variables
		$meta_value = preg_replace('/\s\s+/', ' ', $row->meta_value );
		preg_match_all('/:\"(.*?)\";/', $meta_value, $matches);
		
		// Loop through the array of matches which 
		// are the custom values and their titles
		$i = 0;
		foreach ( $matches[0] as $value ) {
		
			// Clean the data
			$value = str_replace (':"', '', $value );
			$value = str_replace ('";', '', $value );
			
			// Preg_match based on sep_val not just comma
			$pattern = '/' . $sep_val . '/';
			if( preg_match($pattern, $value ) == true ) $value = '"' . $value . '"';
						
			// Grab the header values (even values) if they haven't been printed already
			if( $printed_header == false ){
				if( $i % 2 == 0 ) {
					array_push( $header_line, $value );
				}
			} 
			
			// Grab the actual meta information
			if( $i != 0 and $i % 2 == 1 ) {
				array_push( $line, $value );
			}
		
			$i++;	
		}	 			
		
		// Print the header row if it hasn't been already
		if( $printed_header == false ) { echo implode( $sep_val, $header_line ) . "\n"; }
		
		// Print the row on the csv
		echo implode( $sep_val, $line ) . "\n";
				
		// Set to True so it will only go through this loop once		
		$printed_header = true;
	}
	exit;
}

// Add 'Feedback Exporter' to the Tools Menu
function fbe_admin_page() {
	add_submenu_page( 'tools.php', 'Feedback Exporter', 'Feedback Exporter', 'manage_options', 'feedback_exporter', 'fbe_page_display' );
}

// Feedback Exporter Options Page Display
function fbe_page_display() { 

	// Get List of Subjects
	$fbe_subject = fbe_get_subjects(); 
		
	// Print Header
	fbe_display_header();
	
	// If there is a subject set grab it
	$user_subject = '';
	if( isset($_POST['Export']) && $_POST['Export'] == 'Export' && isset($_POST['subject'])) {
        $user_subject = strip_tags( $_POST['subject'] );
	}
	
	// Check if there are results, if there are display the dropdown
	if ( fbe_get_subjects() != FALSE ) {
		fbe_display_options( $fbe_subject, $user_subject );
	}
	
	// Otherwise show an error message
	else {
		fbe_display_error();
	}
	
	// Print Footer 
	fbe_display_footer();
}

// Queries the database to get all Subjects
function fbe_get_subjects() {
	global $wpdb;
	return $wpdb->get_results( "
			SELECT DISTINCT `meta_value` 
			FROM `{$wpdb->postmeta}` 
			WHERE `meta_key` = '_feedback_subject' 
			ORDER BY `meta_value`" 
			); 
}

// Displays the Export form on the 'Feedback Exporter' page
function fbe_display_options( $fbe_subject, $user_subject ) { ?>
					
	<form name="subject_form" method="post" action="">
		<p>Select Form: 
			<select name="subject">
				<?php foreach ( $fbe_subject as $row ) { ?>
					<option value="<?php echo $row->meta_value ?>" <?php if ( $user_subject == $row->meta_value ) echo 'selected="selected"'; ?>><?php echo $row->meta_value ?></option>
				<?php } ?>
			</select>
		
			<input type="submit" name="Export" class="button-primary" value="Export" />
		</p>
		
		<p>Include Date? 
			<select name="include_date">
				<option value="yes" <?php if ( isset($_POST['include_date']) and $_POST['include_date'] == 'yes' ) echo 'selected="selected"'; ?>>Yes</option>
				<option value="no" <?php if ( isset($_POST['include_date']) and $_POST['include_date'] == 'no' ) echo 'selected="selected"'; ?>>No</option>
			</select>
		</p>
		<p>
			Separator Value:
			<select name="sep_val">
				<option value="," <?php if ( isset($_POST['sep_val']) and $_POST['sep_val'] == ',' ) echo 'selected="selected"'; ?>>,</option>
				<option value=";" <?php if ( isset($_POST['sep_val']) and $_POST['sep_val'] == ';' ) echo 'selected="selected"'; ?>>;</option>
				<option value=":" <?php if ( isset($_POST['sep_val']) and $_POST['sep_val'] == ':' ) echo 'selected="selected"'; ?>>:</option>
			</select>
		</p>
		
		<p><strong>Note:</strong> If you don't see your form in the list it may not have any entries.</p>
	</form>	   
	
<?php   

}

function fbe_display_error() {
	echo "
		<p>	Sorry something went wrong,<br /> 
			either you don't have any results <br />
			or you don't have Jetpack installed.<br /> <br />
			Double check you have both of these and try again.
		</p>";
}

function fbe_display_header() { ?>	
	
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Feedback Exporter</h2>

	<?php
}

function fbe_display_footer() { ?> 
	
	</div><!-- .wrap --> 
	
	<?php
}
?>