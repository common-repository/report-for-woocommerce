<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<?php
/*
Plugin Name: Report For WooCommerce
Description: WooCommerce order and product analysis sales report.
Author: woocommercereport
Version: 1.0
Author URI: http://woocommercereport.com/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/agpl-3.0.html
Last Updated Date : 12-July-2017

*/
if ( !class_exists( 'Report_For_WooCommerce' ) ) {
	class Report_For_WooCommerce { 
		 function __construct(){
			include_once("includes/woo-report.php");
			$woo = new Woo_Report();
		 }
	}
	$report = new Report_For_WooCommerce();
}
?>