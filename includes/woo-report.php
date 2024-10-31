<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<?php
if( !class_exists( 'Woo_Report' ) ) {
	include_once("woo-function.php");
	class Woo_Report extends Woo_Function{
		function __construct(){
			add_action( 'admin_menu',  array(&$this,'admin_menu' ));
			add_action( 'admin_enqueue_scripts',  array(&$this,'admin_enqueue_scripts' ));	
			add_action( 'wp_ajax_woo_report',  array(&$this,'ajax_woo_report' ));
		}
		function admin_menu(){
			add_menu_page(
			__('Sales Report','woocommercesalesreport'),
			__('Sales Report','woocommercesalesreport'),
			'manage_options',
			'woo-report',
			array(&$this,'add_menu'),
			'dashicons-analytics'
			,57.828);
			
			add_submenu_page(
				'woo-report',
				__('Dashboard','woocommercesalesreport'),  
				__('Dashboard','woocommercesalesreport'), 
				'manage_options', 
				'woo-report',
				array(&$this,'add_menu'));
				
				add_submenu_page(
				'woo-report',
				__('Order Report','woocommercesalesreport'),  
				__('Order Report','woocommercesalesreport'), 
				'manage_options', 
				'woo-order',
				array(&$this,'add_menu'));
				
				add_submenu_page(
				'woo-report',
				__('Product Report','woocommercesalesreport'),  
				__('Product Report','woocommercesalesreport'), 
				'manage_options', 
				'woo-product',
				array(&$this,'add_menu'));
		}
		function add_menu(){
			if(isset($_REQUEST["page"])){
				$page = $_REQUEST["page"];
				if ($page=="woo-report"){
					include_once("woo-dashboard.php");
					$woo = new Woo_Dashboard();
					$woo->woo_report();
				}
				if ($page=="woo-product"){
					include_once("woo-product.php");
					$woo = new Woo_Product();
					$woo->woo_report();
				}
				if ($page=="woo-order"){
					include_once("woo-order.php");
					$woo = new Woo_Order();
					$woo->woo_report();
				}
			}
			die;
		}
		/*WOO */
		function ajax_woo_report(){
			//echo json_encode($_REQUEST);
			if(isset($_REQUEST["report_name"])){
				$report_name = $_REQUEST["report_name"];
				if($report_name =="order_report"){
					include_once("woo-order.php");
					$woo = new Woo_Order();
					$woo->get_order_report();
				}
				if($report_name =="product_report"){
					include_once("woo-product.php");
					$woo = new Woo_Product();
					$woo->get_product_report();
				}
			}
			die;	
		}
		function admin_enqueue_scripts(){
			if (isset($_REQUEST["page"])){
				$page = $_REQUEST["page"];
				if ($page =="woo-order" || $page=="woo-product" || $page =="woo-report"){
					wp_enqueue_script('woo-report-script', plugins_url( 'admin/js/script.js', __FILE__ ), array('jquery') );
					wp_enqueue_script('woo-report-script-data', plugins_url( 'admin/js/woo-report.js', __FILE__ ) );
					wp_enqueue_script('woo-report-script-tablesorter', plugins_url( 'admin/js/tablesorter.min.js', __FILE__ ) );
					wp_localize_script('woo-report-script','woo_report_object',array('woo_report_ajaxurl'=>admin_url( 'admin-ajax.php' )) );
					wp_register_style('woo-tablesorter-style', plugins_url( 'admin/css/tablesorter-style.css', __FILE__ ));
					wp_enqueue_style('woo-tablesorter-style');
					
					wp_register_style('woo-report-style', plugins_url( 'admin/css/woo-report.css', __FILE__ ));
					wp_enqueue_style('woo-report-style');
				}
			}
		}	
	}
}
?>