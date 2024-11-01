<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('clsReport')){
	class clsReport
	{	
		/*Graph Report*/
		function ic_cr_Last_7_days_sales_amount()
		{
			global $wpdb,$sql,$Limit;
	
				$weekarray = array();
				$timestamp = time();
				for ($i = 0 ; $i < 7 ; $i++) {
					$weekarray[] =  date('Y-m-d', $timestamp);
					$timestamp -= 24 * 3600;
				}
				
					$sql = "SELECT DATE(FROM_UNIXTIME(date)) AS 'order_date'
							,sum(totalprice) as 'total_amount'    FROM {$wpdb->prefix}wpsc_purchase_logs
							
							WHERE( DATE(FROM_UNIXTIME(date)) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY))
							
							GROUP BY DATE(FROM_UNIXTIME(date))
							";
					$order_items = $wpdb->get_results($sql);
					$item_dates = array();
					$item_data = array();
					
					foreach($order_items as $item)
					{
						$item_dates[] = trim($item->order_date);
						$item_data[$item->order_date]	= $item->total_amount;
					}
					$new_data = array();
					foreach($weekarray as $date)
					{	if(in_array($date, $item_dates))
						{
							
							$new_data[$date] = $item_data[$date];
						}
						else
						{
							$new_data[$date] = 0;
						}
					}
					
					$new_data2 = array();
					$i = 0;
					foreach($new_data as $key => $value)
					{
						$new_data2[$i]["order_date"]	= $key;
						$new_data2[$i]["total_amount"]	= $value;
						$i++;
					}
					//print_array($new_data2);				
					if(isset($_POST['graph_by_type']) && $_POST['graph_by_type'] == "Last_7_days_sales_order_amount"){
						echo	json_encode($new_data2);
					}
					else
					{
						return $order_items;
					}		
		}	
		function ic_cr_total_categories()
		{
			global $wpdb,$sql,$Limit;
			$sql = "SELECT count(*) as 'total_categories' FROM {$wpdb->prefix}term_taxonomy as term_taxonomy  
						LEFT JOIN  {$wpdb->prefix}terms as terms ON terms.term_id=term_taxonomy.term_id
						WHERE taxonomy ='wpsc_product_category'
						";
			
			return $wpdb->get_var($sql);
		}
		function ic_cr_total_product()
		{	global $wpdb,$sql,$Limit;
			$sql = "SELECT count(*) as 'total_product'   FROM {$wpdb->prefix}posts as posts WHERE posts.post_type='wpsc-product'";
		
			return $wpdb->get_var($sql);
		}
		function ic_cr_top_product_chart()
		{
			global $wpdb,$sql,$Limit;
			$sql = "SELECT 
						count(*) as 'total_count'
						,(wpsc_cart_contents.price*SUM(wpsc_cart_contents.quantity)) as 'total_amount'
						,wpsc_cart_contents.name as 'product_name'
						,SUM(wpsc_cart_contents.quantity) as 'total_quantity'
						,wpsc_cart_contents.price as 'price'
						FROM {$wpdb->prefix}wpsc_cart_contents as wpsc_cart_contents
					GROUP BY purchaseid
					";
					$order_items = $wpdb->get_results($sql); 
				
				if(isset($_POST['graph_by_type']) && $_POST['graph_by_type'] == "top_product"){
					echo	json_encode($order_items);
				
				}
				else
				{
					return $order_items;
				}					
			
			
		}
		function ic_cr_top_product()
		{
			global $wpdb,$sql,$Limit;
			$sql = "SELECT 
						count(*) as 'total_count'
						,(wpsc_cart_contents.price*SUM(wpsc_cart_contents.quantity)) as 'total_amount'
						,wpsc_cart_contents.name as 'product_name'
						,SUM(wpsc_cart_contents.quantity) as 'total_quantity'
						,wpsc_cart_contents.price as 'price'
						FROM {$wpdb->prefix}wpsc_cart_contents as wpsc_cart_contents
					GROUP BY purchaseid
					 LIMIT 5
					";
			
			return $wpdb->get_var($sql);
		}
		function ic_cr_today_order_count()
		{
			global $wpdb,$sql,$Limit;
			$TodayDate = date("Y-m-d");
			$sql = "SELECT 
			count(*) AS 'total_count'
			,wpsc_purchase_logs.id  As order_id
			,wpsc_purchase_logs.totalprice as 'total_amount'
			,'Today' AS 'SalesOrder'
			FROM {$wpdb->prefix}wpsc_purchase_logs As wpsc_purchase_logs
			WHERE DATE(FROM_UNIXTIME(wpsc_purchase_logs.date)) = '".$TodayDate."'
			
			";
			$order_items = $wpdb->get_results($sql);
			
			if(isset($_POST['graph_by_type']) && $_POST['graph_by_type'] == "today_order_count"){
				echo	json_encode($order_items);
				
			}
			else
			{
				return $order_items;
			}			
				
		}
		function ic_cr_total_customer()
		{
			$user_query = new WP_User_Query( array( 'role' => 'subscriber' ) );
			
			return $user_query->total_users;
			
		}
		function ic_cr_total_order()
		{
			global $wpdb,$sql,$Limit;
			$sql = "SELECT  count(*) as 'total_count' FROM {$wpdb->prefix}wpsc_purchase_logs as wpsc_purchase_logs";
			
			return $wpdb->get_var($sql);
		}
		function ic_cr_total_amount()
		{
			global $wpdb,$sql,$Limit;
			$sql = "SELECT  sum(wpsc_purchase_logs.totalprice) as 'total_amount' FROM {$wpdb->prefix}wpsc_purchase_logs as wpsc_purchase_logs";
		
			return $wpdb->get_var($sql);
		}
		function ic_cr_top_billing_country()
		{
			global $wpdb,$sql,$Limit;
			$sql = "SELECT
					count(*) as 'total_count' 
					,sum(wpsc_purchase_logs.totalprice) as 'total_amount' 
					,billing_countory.value As billing_countory 
					
					FROM {$wpdb->prefix}wpsc_purchase_logs as wpsc_purchase_logs
					
					LEFT JOIN  {$wpdb->prefix}wpsc_submited_form_data as billing_countory ON billing_countory.log_id=wpsc_purchase_logs.id
					
					WHERE billing_countory.form_id=7
					GROUP BY billing_countory 	 
					Order By DATE(FROM_UNIXTIME(wpsc_purchase_logs.date)) DESC 
					
					 LIMIT 5
					";
		
			$order_items = $wpdb->get_results($sql); 
			if(count($order_items)>0):
				
			?>
			<table style="width:100%" class="widefat">
				<thead>
					<tr class="first">
						<th>Billing Country</th>
						<th>Order Count</th>                           
						<th class="amount">Amount</th>
					</tr>
				</thead>
				<tbody>
					<?php					
					foreach ( $order_items as $key => $order_item ) {
					if($key%2 == 1){$alternate = "alternate ";}else{$alternate = "";};
					?>
						<tr class="<?php echo $alternate."row_".$key;?>">
							<td><?php echo $this->ic_cr_get_country_name( $order_item->billing_countory);?></td>
							<td><?php echo $order_item->total_count?></td>
							<td class="amount"><?php echo  $this->ic_cr_price($order_item->total_amount)?></td>
						 <?php } ?>		
						</tr>
				<tbody>           
			</table>
			<style type="text/css">
				td.amount{ text-align:right; width:100px;}
				th.amount{ text-align:right; width:100px;}
			</style>	
			<?php 
			else:
				echo '<p>No order found.</p>';
			endif;
		}
		
		function ic_cr_top_customer_list(){
			global $wpdb,$sql,$Limit;
			$sql = "SELECT
					count(*) as 'total_count' 
					,sum(wpsc_purchase_logs.totalprice) as 'total_amount' 
					,billing_first_name.value As 'billing_first_name' 
					,billing_email.value As 'billing_email'
					 
					FROM {$wpdb->prefix}wpsc_purchase_logs as wpsc_purchase_logs
					
					LEFT JOIN  {$wpdb->prefix}wpsc_submited_form_data as billing_email ON billing_email.log_id=wpsc_purchase_logs.id
					LEFT JOIN  {$wpdb->prefix}wpsc_submited_form_data as billing_first_name ON billing_first_name.log_id=wpsc_purchase_logs.id
					
					WHERE billing_email.form_id=9
					AND billing_first_name.form_id=2
					GROUP BY billing_email 	 
					Order By DATE(FROM_UNIXTIME(wpsc_purchase_logs.date)) DESC 
					
					 LIMIT 5
					";
			
			$order_items = $wpdb->get_results($sql );
				if(count($order_items)>0):?>
				<table style="width:100%" class="widefat">
					<thead>
						<tr class="first">
							<th>Billing First Name</th>
							<th>Billing Email</th>                           
							<th>Order Count</th>
							<th class="amount">Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php					
							foreach ( $order_items as $key => $order_item ) {
								if($key%2 == 1){$alternate = "alternate ";}else{$alternate = "";};
								?>
								<tr class="<?php echo $alternate."row_".$key;?>">
									<td><?php echo $order_item->billing_first_name?></td>
									<td><?php echo $order_item->billing_email?></td>
									<td><?php echo $order_item->total_count?></td>
									<td class="amount"><?php echo $this->ic_cr_price($order_item->total_amount)?></td>
								</tr>
							 <?php } ?>	
					<tbody>           
				</table>	
				<?php
				else:
					echo '<p>No orders found.</p>';
				endif;
		}
		function ic_cr_recent_orders(){
			global $wpdb,$sql,$Limit;
			
			$sql = "SELECT 
					count(*) AS 'total_count'
					,wpsc_cart_contents.purchaseid  As order_id
					,DATE(FROM_UNIXTIME(wpsc_purchase_logs.date)) AS 'order_date'
					,wpsc_purchase_logs.totalprice as 'total_amount'
					,billing_country AS billing_country
					,billing_email.value As 'billing_email' 
					,billing_first_name.value As 'billing_first_name' 
					,processed as 'order_status_id'
					 FROM {$wpdb->prefix}wpsc_cart_contents as wpsc_cart_contents
					 
					 LEFT JOIN  {$wpdb->prefix}wpsc_purchase_logs as wpsc_purchase_logs ON wpsc_purchase_logs.id=wpsc_cart_contents.purchaseid
					 
					 LEFT JOIN  {$wpdb->prefix}wpsc_submited_form_data as billing_email ON billing_email.log_id=wpsc_cart_contents.purchaseid
					 LEFT JOIN  {$wpdb->prefix}wpsc_submited_form_data as billing_first_name ON billing_first_name.log_id=wpsc_cart_contents.purchaseid
					 
					 WHERE billing_email.form_id=9
					 AND billing_first_name.form_id=2
					 
					 GROUP BY purchaseid
					 Order By DATE(FROM_UNIXTIME(wpsc_purchase_logs.date)) DESC 
					 
					 LIMIT 5
					 ";
			
			
			$order_items = $wpdb->get_results($sql );
						if(count($order_items)>0):
					?>
					 <table style="width:100%" class="widefat">
						<thead>
							<tr class="first">
								<th>Order ID</th>
								<th>Order Date</th>                           
								<th>Billing Email</th>
								<th>First Name</th>
							  <!--  <th>Item Count</th>-->
								<th class="amount">Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php					
								foreach ( $order_items as $key => $order_item ) {
								if($key%2 == 1){$alternate = "alternate ";}else{$alternate = "";};
							?>
							<tr class="<?php echo $alternate."row_".$key;?>">
								<td><?php echo $order_item->order_id?></td>
								<td><?php echo $order_item->order_date?></td>
								<td><?php echo $order_item->billing_email?></td>
								<td><?php echo $order_item->billing_first_name?></td>
							   <!-- <td><?php //echo $order_item->ItemCount?></td>-->
								<td class="amount"><?php echo $this->ic_cr_price($order_item->total_amount);?></td>
							</tr>
							 <?php } ?>
						<tbody>           
					</table>
			<?php 
					else:
						echo '<p>No recent order found.</p>';
					endif;
			
		}
		function ic_cr_sales_order_count_value()
		{	global $wpdb,$sql,$Limit;
			$TodayDate = date("Y-m-d");
			/*Today*/	
			$sql = "SELECT 
				count(*) AS 'total_count'
				,wpsc_purchase_logs.id  As order_id
				,wpsc_purchase_logs.totalprice as 'total_amount'
				,'Today' AS 'SalesOrder'
				FROM {$wpdb->prefix}wpsc_purchase_logs As wpsc_purchase_logs
				WHERE DATE(FROM_UNIXTIME(wpsc_purchase_logs.date)) = '".$TodayDate."'
				
				";
			$sql .= "	 UNION ";
			/*Yesterday*/
			$sql .= "SELECT 
					count(*) AS 'total_count'
					,wpsc_purchase_logs.id  As order_id
					,wpsc_purchase_logs.totalprice as 'total_amount'
					,'Yesterday' AS 'Sales Order'
					FROM {$wpdb->prefix}wpsc_purchase_logs As wpsc_purchase_logs
					WHERE DATE(FROM_UNIXTIME(wpsc_purchase_logs.date)) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))
			
					";
				$sql .= "	 UNION ";	
			/*Week*/		
			$sql .= "SELECT 
					count(*) AS 'total_count'
					,wpsc_purchase_logs.id  As order_id
					,wpsc_purchase_logs.totalprice as 'total_amount'
					,'Week' AS 'Sales Order'
					FROM {$wpdb->prefix}wpsc_purchase_logs As wpsc_purchase_logs
					WHERE WEEK(FROM_UNIXTIME(wpsc_purchase_logs.date)) =WEEK(DATE(CURDATE()))
			
			";
			/*Month*/	
			$sql .= "	 UNION ";
			$sql .= "SELECT 
				count(*) AS 'total_count'
				,wpsc_purchase_logs.id  As order_id
				,wpsc_purchase_logs.totalprice as 'total_amount'
				,'Month' AS 'Sales Order'
				FROM {$wpdb->prefix}wpsc_purchase_logs As wpsc_purchase_logs
				WHERE 
				MONTH(FROM_UNIXTIME(wpsc_purchase_logs.date)) =MONTH(DATE(CURDATE()))
				AND YEAR(DATE(CURDATE())) = YEAR(FROM_UNIXTIME(wpsc_purchase_logs.date))
					";	
			/*Year*/	
			$sql .= "	 UNION ";
				$sql .= "SELECT 
				count(*) AS 'total_count'
				,wpsc_purchase_logs.id  As order_id
				,wpsc_purchase_logs.totalprice as 'total_amount'
				,'Year' AS 'Sales Order'
				FROM {$wpdb->prefix}wpsc_purchase_logs As wpsc_purchase_logs
				WHERE 
				YEAR(DATE(CURDATE())) = YEAR(FROM_UNIXTIME(wpsc_purchase_logs.date))
				";	
			
			//$order_items = $wpdb->get_results($sql); 
			//print_array($order_items);			
			//return $wpdb->get_var($sql);
			
			$order_items = $wpdb->get_results($sql ); 
					?>	
				 <table style="width:100%" class="widefat">
					<thead>
						<tr class="first">
							<th>Sales</th>
							<th>Order Count</th>
							<th class="amount">Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php					
							foreach ( $order_items as $key => $order_item ) {
							if($key%2 == 1){$alternate = "alternate ";}else{$alternate = "";};
						?>
							<tr class="<?php echo $alternate."row_".$key;?>">
								<td><?php echo $order_item->SalesOrder?></td>
								<td><?php echo $order_item->total_count?></td>
								<td class="amount"><?php echo wpsc_currency_display($order_item->total_amount);?></td>
							</tr>
						 <?php } ?>	
					<tbody>           
				</table>		
				<?php
			
		}
		function ic_cr_price($vlaue){
			return wpsc_currency_display( $vlaue);
		} 
		function ic_cr_get_country_name($country_code)
		{
			$countries = new WPSC_Country( $country_code, 'isocode' );
				
				return  $countries->get( 'country' );
		}
	}
}
?>