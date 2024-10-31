<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<?php 
if( !class_exists( 'Woo_Order' ) ) {
	class Woo_Order extends Woo_Function{
		function __construct(){
		}
		function woo_report(){
		?>
        <div class="woo_report_container">
        	<div class="woo_report_search_form">
            	<form id="frm_woo_report" name="frm_woo_report">
        	<table>
            	<tr>
                	<td>Order Range</td>
                    <td>
                    	<select name="order_range"  id="order_range"  class="woo_report_select_option">
                          <option value="today">Today</option>
                          <option value="yesterday">Yesterday</option>
                          <option value="last_7_days">Last 7 days</option>
                          <option value="last_30_days">Last 30 days</option>
                          <option value="last_60_days">Last 60 days</option>
                          <option value="last_90_days">Last 90 days</option>
                          
                        </select>
                    </td>
                </tr>
                <tr>
                	<td>
                    	Billing First Name
                    </td>
                    <td>
                    	<input type="text" name="billing_first_name" id="billing_first_name" class="woo_report_input_type_text" />
                    </td>
                </tr>
                <tr>
                	<td colspan="2" style="text-align:right">
                    	<input type="submit" value="Search" class="woo_report_submit_button" />
                    </td>
                </tr>
            </table>
            <input type="hidden" name="action" value="woo_report" />
            <input type="hidden" name="report_name" value="order_report" />
        	
        </form>
            </div>
        <div class="_woo_report_content"></div>
        </div>
        <?php
		}
		function get_query(){
			global $wpdb;
			
		
			
			$today_date = $this->get_date();
	    	$order_range = isset($_REQUEST["order_range"])?$_REQUEST["order_range"]:$this->get_date(); 
			$billing_first_name = isset($_REQUEST["billing_first_name"])?$_REQUEST["billing_first_name"]:''; 
		
		
			
			
			$query = "";
			$query .= " SELECT ";
			$query .= "	posts.ID as order_id ";
			$query .= "	,posts.post_status as order_status ";
			$query .= "	, date_format( posts.post_date, '%Y-%m-%d') as order_date ";
			$query .= "	, order_total.meta_value as order_total";
			$query .= "	, billing_email.meta_value as billing_email";
			$query .= "	, billing_first_name.meta_value as billing_first_name";
			$query .= "	FROM {$wpdb->prefix}posts as posts		";	
			
			
			$query .= " LEFT JOIN  {$wpdb->prefix}postmeta as  order_total ON order_total.post_id=posts.ID ";
			$query .= " LEFT JOIN  {$wpdb->prefix}postmeta as  billing_first_name ON billing_first_name.post_id=posts.ID ";
			$query .= " LEFT JOIN  {$wpdb->prefix}postmeta as  billing_email ON billing_email.post_id=posts.ID ";
			
			$query .= " WHERE 1= 1";
			$query .= " AND posts.post_type ='shop_order' ";
			$query .= " AND order_total.meta_key ='_order_total' ";
			$query .= " AND billing_first_name.meta_key ='_billing_first_name' ";
			$query .= " AND billing_email.meta_key ='_billing_email' ";
			
			 switch ($order_range) {
					case "today":
						$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$today_date}' AND '{$today_date}'";
						break;
					case "yesterday":
						$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') = date_format( DATE_SUB(CURDATE(), INTERVAL 1 DAY), '%Y-%m-%d')";
						break;
					case "last_10_days":
						$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 10 DAY), '%Y-%m-%d') AND   '{$today_date}' ";
						break;	
					case "last_30_days":
							$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 30 DAY), '%Y-%m-%d') AND   '{$today_date}' ";
						break;	
					case "last_60_days":
							$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 60 DAY), '%Y-%m-%d') AND   '{$today_date}' ";
						break;	
					case "last_90_days":
							$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 90 DAY), '%Y-%m-%d') AND   '{$today_date}' ";
						break;			
					default:
						$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$today_date}' AND '{$today_date}'";
				}
				
				if (strlen($billing_first_name)>0 && $billing_first_name !=""){
					$query .= " AND  billing_first_name.meta_value  LIKE '%{$billing_first_name}%'";
				}
				$query .= "order by posts.post_date DESC";	
			
						
			$row = $wpdb->get_results( $query);	
				
			if ($wpdb->last_error!=""){
				echo $wpdb->last_error;
			}
		
			//$this->report_print($row);
			return $row;		
			
		}
		function get_order_report(){
			$row = $this->get_query();
			if (count($row)>0){
			?>
            <table id="order_report"  class="tablesorter">
            	<thead>
                    <tr>
                        <th><?php _e('ID','woocommercesalesreport') ?></th>
                        <th><?php _e('Date','woocommercesalesreport') ?></th>
                        <th><?php _e('Billing First Name','woocommercesalesreport') ?></th>
                        <th><?php _e('Billing Email','woocommercesalesreport') ?></th>
                        <th><?php _e('Order Status','woocommercesalesreport') ?></th>
                        <th><?php _e('Order Total','woocommercesalesreport') ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($row as $key=>$value): ?>
                <tr>
                	<td><?php echo $value->order_id ?></td>
                    <td><?php echo $value->order_date ?></td>
                    <td><?php echo $value->billing_first_name ?></td>
                    <td><?php echo $value->billing_email ?></td>
                    <td><?php echo $value->order_status ?></td>
                    <td class="woo_report_text_align_right"><?php echo $this->get_woo_price( $value->order_total); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php
			}
		}
		function report_print($data){
			print "<pre>";
			print_r($data);
			print "</pre>";
		}
		function get_date(){
			return date_i18n("Y-m-d");
		}
		function get_woo_price($price = 0){
		return wc_price($price);
		}
		
	}
}
?>