<?php
/* This file contains functions used to process required database updates sometimes logged after PMPro is upgraded. */

/*
	Is there an update?
*/
function pmpro_isUpdateRequired() {
	$updates = get_option('pmpro_updates', array());
	return(!empty($updates));
}

/**
 * Update option to require an update.
 * @param string $update
 *
 * @since 1.8.7
 */
function pmpro_addUpdate($update) {
	$updates = get_option('pmpro_updates', array());
	$updates[] = $update;
	$updates = array_unique($updates);

	update_option('pmpro_updates', $updates, 'no');
}

/**
 * Update option to remove an update.
 * @param string $update
 *
 * @since 1.8.7
 */
function pmpro_removeUpdate($update) {
	$updates = get_option('pmpro_updates', array());
	$key = array_search($update,$updates);
	if($key!==false){
	    unset($updates[$key]);
	}

	update_option('pmpro_updates', $updates, 'no');
}

/*
	Enqueue updates.js if needed
*/
function pmpro_enqueue_update_js() {
	if(!empty($_REQUEST['page']) && $_REQUEST['page'] == 'pmpro-updates') {
		wp_enqueue_script( 'pmpro-updates', plugin_dir_url( dirname(__FILE__) ) . 'js/updates.js' );
	}
}
add_action('admin_enqueue_scripts', 'pmpro_enqueue_update_js');

/*
	Load an update via AJAX
*/
function pmpro_wp_ajax_pmpro_updates() {
	//get updates
	$updates = get_option('pmpro_updates', array());

	//run update or let them know we're done
	if(!empty($updates)) {
		//get the latest one and run it
		call_user_func($updates[0]);
		echo ". ";
	} else {
		echo "done";
	}

	exit;
}
add_action('wp_ajax_pmpro_updates', 'pmpro_wp_ajax_pmpro_updates');

/*
	Show admin notice if an update is required and not already on the updates page.
*/
if(pmpro_isUpdateRequired() && (empty($_REQUEST['page']) || $_REQUEST['page'] != 'pmpro-updates'))
	add_action('admin_notices', 'pmpro_update_notice');

/*
	Function to show an admin notice linking to the updates page.
*/
function pmpro_update_notice() {
?>
<div class="update-nag">
	<p>
	<?php 
		echo __( 'Paid Memberships Pro Data Update Required', 'pmpro' );
	?>
	</p>
	<p>
	<?php 
		echo '<a class="button button-primary" href="' . admin_url('admin.php?page=pmpro-updates') . '">' . __('Start the Update', 'pmpro') . '</a>';
	?>
	</p>
</div>
<?php
}