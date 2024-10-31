<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<?php 
if( !class_exists( 'Woo_Product' ) ) {
	class Woo_Product{
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
                    	<select name="order_range"  id="order_range"  style="width:250px; border:1px solid #00796B">
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
                	<td colspan="2" style="text-align:right">
                    	<input type="submit" value="Search" class="woo_report_submit_button" />
                    </td>
                </tr>
            </table>
           <input type="hidden" name="action" value="woo_report" />
           <input type="hidden" name="report_name" value="product_report" />
        	
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
			
			
			$query .= "	, qty.meta_value as qty";
			$query .= "	, line_subtotal.meta_value as line_subtotal";
			
			$query .= "	, line_total.meta_value as line_total";
		
			$query .= "	,order_items.order_item_name as product_name";
			$query .= "	FROM {$wpdb->prefix}posts as posts		";	
			
			
			$query .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as order_items ON order_items.order_id=posts.ID ";
			
			/*Qty*/
			$query .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as qty ON qty.order_item_id=order_items.order_item_id ";
			
			/*line Subtotal*/
			$query .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as line_subtotal ON line_subtotal.order_item_id=order_items.order_item_id ";
			
			$query .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as line_total ON line_total.order_item_id=order_items.order_item_id ";
			
			
			
			
			$query .= " WHERE 1= 1";
			$query .= " AND posts.post_type ='shop_order' ";
	
			$query .= " AND order_items.order_item_type ='line_item' ";
			$query .= " AND qty.meta_key ='_qty' ";
			$query .= " AND line_subtotal.meta_key ='_line_subtotal' ";
			$query .= " AND line_total.meta_key ='_line_total' ";
			
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
			$query .= "order by posts.post_date DESC";	
							
			$row = $wpdb->get_results( $query);		
			//$this->report_print($row);
			return $row;		
			
		}
		function get_product_report(){
			$row = $this->get_query();
			if (count($row)>0){
			?>
            <table id="order_report"  class="tablesorter">
            	<thead>
                    <tr>
                        <th><?php _e('ID','woocommercesalesreport') ?></th>
                        <th><?php _e('Date','woocommercesalesreport') ?></th>
                        <th><?php _e('Order Status','woocommercesalesreport') ?></th>
                        <th><?php _e('Product Name','woocommercesalesreport') ?></th>
                        <th><?php _e('Qty','woocommercesalesreport') ?></th>
                        <th><?php _e('Price','woocommercesalesreport') ?></th>
                        <th><?php _e('Subtotal','woocommercesalesreport') ?></th>
                        <th><?php _e('Total','woocommercesalesreport') ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($row as $key=>$value): ?>
                <tr>
                	<td><?php echo $value->order_id ?></td>
                    <td><?php echo $value->order_date ?></td>
                    <td><?php echo $value->order_status ?></td>
                    <td><?php echo $value->product_name ?></td>
                    <td  class="woo_report_text_align_right"><?php echo $value->qty ?></td>
                    <td  class="woo_report_text_align_right"><?php echo ($value->line_subtotal/$value->qty) ?></td>
                    <td class="woo_report_text_align_right"><?php echo $this->get_woo_price( $value->line_subtotal); ?></td>
                    <td class="woo_report_text_align_right"><?php echo $this->get_woo_price( $value->line_total); ?></td>
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