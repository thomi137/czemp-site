<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb;
$portfolio_wp_nonce = wp_create_nonce('huge_it_portfolio_nonce');
?>
<script language="javascript">
	function ordering(name,as_or_desc)
	{
		document.getElementById('asc_or_desc').value=as_or_desc;
		document.getElementById('order_by').value=name;
		document.getElementById('admin_form').submit();
	}
	function saveorder()
	{
		document.getElementById('saveorder').value="save";
		document.getElementById('admin_form').submit();

	}
	function listItemTask(this_id,replace_id)
	{
		document.getElementById('oreder_move').value=this_id+","+replace_id;
		document.getElementById('admin_form').submit();
		function doNothing() {
			var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
			if( keyCode == 13 ) {


				if(!e) var e = window.event;

				e.cancelBubble = true;
				e.returnValue = false;

				if (e.stopPropagation) {
					e.stopPropagation();
					e.preventDefault();
				}
			}
		}
	}
</script>


<div class="wrap">
	<?php $path_site2 = plugins_url("../images", __FILE__); ?>
	<div id="poststuff">
		<div id="portfolios-list-page">
			<form method="post"  onkeypress="doNothing()" action="admin.php?page=portfolios_huge_it_portfolio" id="admin_form" name="admin_form">
				<h2>Huge-IT Portfolios
					<a onclick="window.location.href='admin.php?page=portfolios_huge_it_portfolio&task=add_portfolio&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>'" class="add-new-h2" ><?php echo __( 'Add New Portfolio', 'portfolio-gallery' );?></a>
				</h2>
				<?php
				$serch_value='';
				if(isset($_POST['serch_or_not'])) {if($_POST['serch_or_not']=="search"){ $serch_value=esc_html(stripslashes($_POST['search_events_by_title'])); }else{$serch_value="";}}
				$serch_fields='<div class="alignleft actions"">
				<label for="search_events_by_title" style="font-size:14px">Filter: </label>
					<input type="text" name="search_events_by_title" value="'.$serch_value.'" id="search_events_by_title" onchange="clear_serch_texts()">
			</div>
			<div class="alignleft actions">
				<input type="button" value="Search" onclick="document.getElementById(\'page_number\').value=\'1\'; document.getElementById(\'serch_or_not\').value=\'search\';
				 document.getElementById(\'admin_form\').submit();" class="button-secondary action">
				 <input type="button" value="Reset" onclick="window.location.href=\'admin.php?page=portfolios_huge_it_portfolio\'" class="button-secondary action">
			</div>';

				portfolio_gallery_print_html_nav($pageNav['total'],$pageNav['limit'],$serch_fields);
				?>
				<table class="wp-list-table widefat fixed pages" style="width:95%">
					<thead>
					<tr>
						<th scope="col" id="id" style="width:30px" ><span><?php echo __( 'ID', 'portfolio-gallery' );?></span><span class="sorting-indicator"></span></th>
						<th scope="col" id="name" style="width:85px" ><span><?php echo __( 'Name', 'portfolio-gallery' );?></span><span class="sorting-indicator"></span></th>
						<th scope="col" id="prod_count"  style="width:75px;" ><span><?php echo __( 'Images', 'portfolio-gallery' );?></span><span class="sorting-indicator"></span></th>
						<th style="width:40px"><?php echo __( 'Delete', 'portfolio-gallery' );?></th>
					</tr>
					</thead>
					<tbody>
					<?php
					$trcount=1;
					for($i=0; $i<count($rows);$i++){
						$trcount++;
						$ka0=0;
						$ka1=0;
						if(isset($rows[$i-1]->id)){
							if($rows[$i]->sl_width==$rows[$i-1]->sl_width){
								$x1=$rows[$i]->id;
								$x2=$rows[$i-1]->id;
								$ka0=1;
							}
							else
							{
								$jj=2;
								while(isset($rows[$i-$jj]))
								{
									if($rows[$i]->sl_width==$rows[$i-$jj]->sl_width)
									{
										$ka0=1;
										$x1=$rows[$i]->id;
										$x2=$rows[$i-$jj]->id;
										break;
									}
									$jj++;
								}
							}
							if($ka0){
								$move_up='<span><a href="#reorder" onclick="return listItemTask(\''.$x1.'\',\''.$x2.'\')" title="Move Up">   <img src="'.plugins_url('images/uparrow.png',__FILE__).'" width="16" height="16" border="0" alt="Move Up"></a></span>';
							}
							else{
								$move_up="";
							}
						}else{$move_up="";}


						if(isset($rows[$i+1]->id)){

							if($rows[$i]->sl_width==$rows[$i+1]->sl_width){
								$x1=$rows[$i]->id;
								$x2=$rows[$i+1]->id;
								$ka1=1;
							}
							else
							{
								$jj=2;
								while(isset($rows[$i+$jj]))
								{
									if($rows[$i]->sl_width==$rows[$i+$jj]->sl_width)
									{
										$ka1=1;
										$x1=$rows[$i]->id;
										$x2=$rows[$i+$jj]->id;
										break;
									}
									$jj++;
								}
							}

							if($ka1){
								$move_down='<span><a href="#reorder" onclick="return listItemTask(\''.$x1.'\',\''. $x2.'\')" title="Move Down">  <img src="'.plugins_url('images/downarrow.png',__FILE__).'" width="16" height="16" border="0" alt="Move Down"></a></span>';
							}else{
								$move_down="";
							}
						}

						$uncat=$rows[$i]->par_name;
						if(isset($rows[$i]->prod_count))
							$pr_count=$rows[$i]->prod_count;
						else
							$pr_count=0;


						?>
						<tr <?php if($trcount%2==0){ echo 'class="has-background"';}?>>
							<td><?php echo $rows[$i]->id; ?></td>
							<td><a  href="admin.php?page=portfolios_huge_it_portfolio&task=edit_cat&id=<?php echo $rows[$i]->id; ?>&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>"><?php echo esc_html(stripslashes($rows[$i]->name)); ?></a></td>
							<td>(<?php if(!($pr_count)){echo '0';} else{ echo $rows[$i]->prod_count;} ?>)</td>
							<td><a  href="admin.php?page=portfolios_huge_it_portfolio&task=remove_cat&id=<?php echo $rows[$i]->id?>"><?php echo __( 'Delete', 'portfolio-gallery' );?></a></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<input type="hidden" name="oreder_move" id="oreder_move" value="" />
				<input type="hidden" name="asc_or_desc" id="asc_or_desc" value="<?php if(isset($_POST['asc_or_desc'])) echo esc_attr($_POST['asc_or_desc']);?>"  />
				<input type="hidden" name="order_by" id="order_by" value="<?php if(isset($_POST['order_by'])) echo esc_attr($_POST['order_by']);?>"  />
				<input type="hidden" name="saveorder" id="saveorder" value="" />

				<?php
				?>



			</form>
		</div>
	</div>
</div>