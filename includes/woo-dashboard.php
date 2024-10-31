<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<?php 
if( !class_exists( 'Woo_Dashboard' ) ) {
	class Woo_Dashboard{
		function __construct(){
		}
		function woo_report(){
		?>
         <div class="woo_report_container">
        	<div class="woo_report_search_form">
          	<?php $this->woo_report_sales_by_month(); ?>
            </div>
 		</div>           
        <?php	
			
		}
		function woo_report_sales_by_month(){
			global $wpdb;
			
			 $all_month  = $this->get_months_list();
			 $end_date =date_i18n("Y-m-d");
			
			 $start_date =  date_i18n("Y-m-d", strtotime("-6 months", strtotime($end_date)));
			
			
			$query = "";
			$query = " SELECT ";
			$query .= " SUM(order_total.meta_value) as order_total";
			$query .= ",  date_format( posts.post_date, '%Y-%m')   as month";
			$query .= "  FROM  {$wpdb->prefix}posts as posts ";
			$query  .= " LEFT JOIN  {$wpdb->prefix}postmeta as order_total ON order_total.post_id=posts.ID ";
			$query .= " WHERE 1=1 ";
			$query .= " AND posts.post_type = 'shop_order'";
			$query .= " AND order_total.meta_key = '_order_total'";
			
			$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}'";
			
			  //$query .= " AND posts.post_status IN ('{$order_status}')	";
			
			
			$query .= " GROUP BY YEAR(posts.post_date), MONTH(posts.post_date) ";
			$row = $wpdb->get_results($query);
			$_net_amount = array();
			foreach($row as $key=>$value){
				$_net_amount[$value->month] = $value->order_total;
			}
			
			
			
			
			$query = "";
			$query = " SELECT ";
			$query .= " SUM(order_itemmeta.meta_value) as order_total";
			$query .= ", date_format( posts.post_date, '%Y-%m')   as month";
			$query .= "  FROM  {$wpdb->prefix}posts as posts ";
			
			$query  .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as order_items ON order_items.order_id=posts.ID ";
			
			$query  .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as order_itemmeta ON order_itemmeta.order_item_id=order_items.order_item_id ";
			
			$query .= " WHERE 1=1 ";
			$query .= " AND posts.post_type = 'shop_order'";
			$query .= " AND order_itemmeta.meta_key = '_line_total'";
			$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}'";
			
			
			$query .= " GROUP BY YEAR(posts.post_date), MONTH(posts.post_date) ";
			$row = $wpdb->get_results($query);
			$_gross_amount = array();
			foreach($row as $key=>$value){
				$_gross_amount[$value->month] = $value->order_total;
			}
			
			foreach($all_month as $key=>$value){
				$gross_amount[$key]["Gross"] = isset($_gross_amount[$key])?$_gross_amount[$key]:0;
				$gross_amount[$key]["Net"] = isset($_net_amount[$key])?$_net_amount[$key]:0;
				$gross_amount[$key]["Month"] = $value;
			}
		
		$gross_amount = 	array_reverse ($gross_amount);
			?>
            <table style="width:100%" class="tablesorter">
            	<tr>
                	<th><?php  _e('Month Name','woocommercesalesreport') ?>  </th>
                    <th style="text-align:right"><?php  _e('Total Gross Sales','woocommercesalesreport') ?>   </th>
                    <th style="text-align:right"><?php  _e('Total Net Sales','woocommercesalesreport') ?>    </th>
                </tr>	
			<?php
			foreach($gross_amount as $key=>$value){
			?>
            <tr>
            	<td style="font-weight:bold"><?php echo $value["Month"]; ?></td>
                <td style="text-align:right"><?php echo wc_price($value["Gross"]); ?></td>
                <td style="text-align:right"><?php echo wc_price($value["Net"]); ?></td>
            </tr>
            <?php		
			}
			?>
           	</table>
            <?php
			
		 }
		 function get_months_list($amount_column = true){
			
			$cross_tab_end_date			=  date_i18n("Y-m-d");
			$cross_tab_start_date		=  date_i18n("Y-m-d", strtotime("-6 months", strtotime($cross_tab_end_date)));
			
			$startDate = strtotime($cross_tab_start_date);
			$endDate   = strtotime($cross_tab_end_date);
			$currentDate = $startDate;
			$this->months = array();
			if($amount_column){					
			
				while ($currentDate <= $endDate) {
					
					$month = date('Y-m',$currentDate);
					$this->months[$month] = date('F',$currentDate);
					$currentDate = strtotime( date('Y/m/01/',$currentDate).' 1 month');
				}
			}else{
				while ($currentDate <= $endDate) {
					$month = date('Y-m',$currentDate);
					$this->months[$month."_total"] = date('M',$currentDate)." Amt.";
					$this->months[$month."_quantity"] = date('M',$currentDate)." Qty.";
					$currentDate = strtotime( date('Y/m/01/',$currentDate).' 1 month');
				}
			}
				
			
			
			return $this->months;
		}
	}
}
?>