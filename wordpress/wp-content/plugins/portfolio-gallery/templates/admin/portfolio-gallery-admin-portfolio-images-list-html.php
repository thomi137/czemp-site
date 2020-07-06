<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if (isset($__REQUEST['huge_it_portfolio_nonce'])) {
	$wp_nonce = $__REQUEST['huge_it_portfolio_nonce'];
	if (!wp_verify_nonce($wp_nonce, 'huge_it_portfolio_nonce')) {
		wp_die('Security check fail');
	}
}
global $wpdb;
$portfolio_wp_nonce = wp_create_nonce('huge_it_portfolio_nonce');
$id = intval($_GET['id']);

if(isset($_GET["addslide"])){
	if($_GET["addslide"] == 1){
		header('Location: admin.php?page=portfolios_huge_it_portfolio&id='.$row->id.'&task=apply');
	}
}
?>
<script type="text/javascript">


	function submitbutton(pressbutton)
	{
		if(!document.getElementById('name').value){
			alert("Name is required.");
			return;

		}
		filterInputs();
		document.getElementById("adminForm").action=document.getElementById("adminForm").action+"&task="+pressbutton;
		document.getElementById("adminForm").submit();
	}
	var  name_changeRight = function(e) {
		document.getElementById("name").value = e.value;
	}
	var  name_changeTop = function(e) {
		document.getElementById("huge_it_portfolio_name").value = e.value;
		//alert(e);
	};

	function change_select()
	{
		submitbutton('apply');

	}
	/***<add>***/
	function secondimageslistlisize(){
		var lisaze = jQuery('#images-list').width();
		lisaze=lisaze*0.06;
		jQuery('#images-list .widget-images-list li').not('.add-image-box').not('.first').height(lisaze);
	}

	function replaceAddImageBox() {
		jQuery(".widget-images-list").each(function(){
			var src = "";

			if(!jQuery(this).find('li').last().hasClass('add-image-box')) {
				var html = jQuery(this).find('.add-image-box').html();
				var li = jQuery('<li>');

				jQuery(this).find('.add-image-box').remove();
				li.addClass('add-image-box').append(html);
				jQuery(this).append(li);
				li.find('.add-thumb-project').css('display','block');
				li.find('.add-image-video').next().css('display','none');
				li.hover(function(){
					jQuery(this).find('.add-thumb-project').css('display','none');
					jQuery(this).find('.add-image-video').css('display','block');

				},function(){
					jQuery(this).find('.add-image-video').css('display','none');
					jQuery(this).find('.add-thumb-project').css('display','block');

				});

			}
			jQuery(this).find("li").not(".add-image-box").each(function() {
				src += (jQuery(this).hasClass('editthisvideo')==true)?jQuery(this).find('img').attr('data-video-src'):jQuery(this).find('img').attr('src');
				src += ";";
			});
			jQuery(this).find('.all-urls').val(src);
			//alert(src);
		});
	}
	function filterInputs() {

		var mainInputs = "";

		jQuery("#images-list > li.highlights").each(function(){
			jQuery(this).next().addClass('submit-post');
			jQuery(this).prev().addClass('submit-post');
			jQuery(this).prev().prev().addClass('submit-post');
			jQuery(this).addClass('submit-post');
			jQuery(this).removeClass('highlights');
		})

		if(jQuery("#images-list > li.submit-post").length) {

			jQuery("#images-list > li.submit-post").each(function(){

				var inputs = jQuery(this).find('.order_by').attr("name");
				var n = inputs.lastIndexOf('_');
				var res = inputs.substring(n+1, inputs.length);
				res +=',';
				mainInputs += res;

			});

			mainInputs = mainInputs.substring(0,mainInputs.length-1);


			jQuery(".changedvalues").val(mainInputs);

			jQuery("#images-list > li").not('.submit-post').each(function(){
				jQuery(this).find('input').removeAttr('name');
				jQuery(this).find('textarea').removeAttr('name');
				jQuery(this).find('select').removeAttr('name');
			});
			return mainInputs;

		}
		jQuery("#images-list > li").each(function(){
			jQuery(this).find('input').removeAttr('name');
			jQuery(this).find('textarea').removeAttr('name');
			jQuery(this).find('select').removeAttr('name');
		});

	}
	/***</add>***/

	jQuery(function() {

		jQuery( "#images-list > li input" ).on('keyup',function(){
			jQuery(this).parents("#images-list > li").addClass('submit-post');
			//filterInputs();
		});
		jQuery( "#images-list > li textarea" ).on('keyup',function(){
			jQuery(this).parents("#images-list > li").addClass('submit-post');
			//	filterInputs();
		});
		jQuery( "#images-list > li input" ).on('change',function(){
			jQuery(this).parents("#images-list > li").addClass('submit-post');
			//	filterInputs();
		});
		jQuery( "#images-list > li select" ).on('change',function(){
			jQuery(this).parents("#images-list > li").addClass('submit-post');
			//	filterInputs();
		});
		jQuery('.add-thumb-project').on('hover',function(){
			jQuery(this).parent().parents("li").addClass('submit-post');
			//	filterInputs();
		})

		jQuery( "#images-list" ).sortable({
			stop: function() {
				jQuery("#images-list > li").removeClass('has-background');
				count=jQuery("#images-list > li").length;
				for(var i=0;i<=count;i+=2){
					jQuery("#images-list > li").eq(i).addClass("has-background");
				}
				jQuery("#images-list > li").each(function(){
					jQuery(this).find('.order_by').val(jQuery(this).index());
				});
			},
			change: function(event, ui) {
				var start_pos = ui.item.data('start_pos');
				var index = ui.placeholder.index();
				if (start_pos < index + 2) {
					jQuery('#images-list > li:nth-child(' + index + ')').addClass('highlights');
				} else {
					jQuery('#images-list > li:eq(' + (index + 1) + ')').addClass('highlights');
				}
			},
			update: function(event, ui) {
				jQuery('#sortable li').removeClass('highlights');
			},
			revert: true
		});
		/***<add>***/
		jQuery( ".widget-images-list" ).sortable({
			stop: function() {

				jQuery(".widget-images-list > li").each(function(){
					jQuery(this).removeClass('first');
					jQuery(".widget-images-list > li").first().addClass('first');
				});

				secondimageslistlisize();
				replaceAddImageBox();
			},
			change: function(event, ui) {

				jQuery(this).parents('li').addClass('submit-post');
				var start_pos = ui.item.data('start_pos');
				var index = ui.placeholder.index();
				if (start_pos < index) {
					jQuery('.widget-images-list > li:nth-child(' + index + ')').addClass('highlights');

				} else {
					jQuery('widget-images-list > li:eq(' + (index + 1) + ')').addClass('highlights');
				}
			},
			update: function(event, ui) {
				jQuery('#sortable li').removeClass('highlights');
			},
			revert: true
		});
		jQuery(".inside ul").sortable({
			stop: function() {
				var allCategories = "";
				jQuery(this).find('.del_val').each(function(){
					var str = jQuery(this).val();
					str = str.replace(" ", "_");
					allCategories += str +",";
				});
				jQuery("#allCategories").val(allCategories);
			},
			revert: true
		});
		/***</add>***/
		// jQuery( "ul, li" ).disableSelection();

	});
</script>

<!-- GENERAL PAGE, ADD IMAGES PAGE -->


<div class="wrap">
	<?php $path_site2 = PORTFOLIO_GALLERY_IMAGES_URL; ?>
	<form action="admin.php?page=portfolios_huge_it_portfolio&id=<?php echo $row->id; ?>" method="post" name="adminForm" id="adminForm">
		<input type="hidden" class="changedvalues" value="" name="changedvalues" size="80">
		<div id="poststuff" >
			<div id="portfolio-header">
				<ul id="portfolios-list">

					<?php
					foreach($rowsld as $rowsldires){
						if($rowsldires->id != $row->id){
							?>
							<li>
								<a href="#" onclick="window.location.href='admin.php?page=portfolios_huge_it_portfolio&task=edit_cat&id=<?php echo $rowsldires->id; ?>&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>'" ><?php echo $rowsldires->name; ?></a>
							</li>
							<?php
						}
						else{ ?>
							<li class="active" onclick = "this.firstElementChild.style.width = ((this.firstElementChild.value.length + 1) * 8) + 'px';" style="background-image:url(<?php echo PORTFOLIO_GALLERY_IMAGES_URL.'/admin_images/edit.png'; ?>);cursor: pointer;">
								<input class="text_area" onkeyup = "name_changeTop(this)" onfocus="this.style.width = ((this.value.length + 1) * 8) + 'px'" type="text" name="name" id="name" maxlength="250" value="<?php echo esc_html(stripslashes($row->name));?>" />
							</li>
							<?php
						}
					}
					?>
					<li class="add-new">
						<a onclick="window.location.href='admin.php?page=portfolios_huge_it_portfolio&amp;task=add_portfolio&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>'">+</a>
					</li>
				</ul>
			</div>
			<div id="post-body" class="metabox-holder columns-2">
				<!-- Content -->
				<div id="post-body-content">


					<?php add_thickbox(); ?>

					<div id="post-body">
						<div id="post-body-heading">
							<h3><?php echo __( 'Projects / Images', 'portfolio-gallery' );?></h3>
							<script>
								jQuery(document).ready(function($){

									jQuery('.huge-it-newuploader .button').click(function(e) {
										var send_attachment_bkp = wp.media.editor.send.attachment;

										var button = jQuery(this);
										var id = button.attr('id').replace('_button', '');
										_custom_media = true;

										jQuery("#"+id).val('');
										wp.media.editor.send.attachment = function(props, attachment){
											if ( _custom_media ) {
												jQuery("#"+id).val(attachment.url+';;;'+jQuery("#"+id).val());
												jQuery("#save-buttom").click();
											} else {
												return _orig_send_attachment.apply( this, [props, attachment] );
											};
										}

										wp.media.editor.open(button);

										return false;
									});

									/*#####HIDE NEW UPLOADER'S LEFT MENU######*/
									jQuery(".wp-media-buttons-icon").click(function() {
										jQuery(".media-menu .media-menu-item").css("display","none");
										jQuery(".media-menu-item:first").css("display","block");
										jQuery(".separator").next().css("display","none");
										jQuery('.attachment-filters').val('image').trigger('change');
										jQuery(".attachment-filters").css("display","none");
									});
									jQuery('.widget-images-list .add-image-box').hover(function() {
										jQuery(this).find('.add-thumb-project').css('display','none');
										jQuery(this).find('.add-image-video').css('display','block');
									},function() {
										jQuery(this).find('.add-image-video').css('display','none');
										jQuery(this).find('.add-thumb-project').css('display','block');
									});
									jQuery('#portfolio_effects_list').on('change',function(){
										var sel = jQuery(this).val();
										if(sel == 5) {
											jQuery('.for-content-slider').css('display','block');
											jQuery('.no-content-slider').css('display','none');
											jQuery('ul.for_loading').parent().css('display','none');
										}
										else if(sel == 3) {
											jQuery('.no-content-slider').css('display','none');
										}
										else {
											jQuery('.for-content-slider').css('display','none');
											jQuery('.no-content-slider').css('display','block');
											jQuery('ul.for_loading').parent().css('display','block');
										}
									});
									jQuery('#portfolio_effects_list').change();
								});
							</script>

							<input type="hidden" name="imagess" id="_unique_name" />
							<span class="wp-media-buttons-icon"></span>
							<div class="huge-it-newuploader uploader button button-primary add-new-image">
								<input type="button" class="button wp-media-buttons-icon" name="_unique_name_button" id="_unique_name_button" value="Add Project / Image" />
							</div>

							<a href="admin.php?page=portfolios_huge_it_portfolio&task=portfolio_video&id=<?php echo $id; ?>&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>&TB_iframe=1" class="button button-primary add-video-slide thickbox"  id="slideup3s" value="iframepop">
								<span class="wp-media-buttons-icon"></span><?php echo __( 'Add Video Slide', 'portfolio-gallery' );?>
							</a>
						</div>
						<ul id="images-list">
							<?php
							/***<add>***/
							function get_youtube_id_from_url($url){
								if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
									return $match[1];
								}
							}
							function get_image_from_video($image_url) {
								if(strpos($image_url,'youtube') !== false || strpos($image_url,'youtu') !== false) {
									$liclass="youtube";
									$video_thumb_url=get_youtube_id_from_url($image_url);
									$thumburl='http://img.youtube.com/vi/'.$video_thumb_url.'/mqdefault.jpg';
								} else
									if (strpos($image_url,'vimeo') !== false) {
										$liclass="vimeo";
										$vimeo = $image_url;
										$vimeo_explode = explode( "/", $vimeo );
										$imgid =  end($vimeo_explode);
										$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$imgid.".php"));
										$imgsrc=$hash[0]['thumbnail_large'];
										$thumburl =$imgsrc;
									}
								return $thumburl;
							}
							/***</add>***/
							$j=2;

							$myrows = explode(",",$row->categories);

							foreach ($rowim as $key=>$rowimages){?>
								<!--<add>  swirch case--->
								<?php if($rowimages->sl_type == ''){$rowimages->sl_type = 'image';}
								switch($rowimages->sl_type){
									case 'image':	?>
										<li <?php if($j%2==0){echo "class='has-background'";}$j++; ?>>
											<input class="order_by" type="hidden" name="order_by_<?php echo $rowimages->id; ?>" value="<?php echo $rowimages->ordering; ?>" />
											<div class="image-container">
												<ul class="widget-images-list">
													<?php $imgurl=explode(";",$rowimages->image_url);
													array_pop($imgurl);
													$i=0;
													//$imgurl = array_reverse($imgurl);
													foreach($imgurl as $key1=>$img)
													{//var_dump(portfolio_gallery_youtube_or_vimeo_portfolio$img));
														if(portfolio_gallery_youtube_or_vimeo_portfolio($img) != 'image') {?>
															<li class="editthisvideo editthisimage<?php echo $key; ?><?php if($i==0){echo 'first';} ?>" >
																<img class="editthisvideo" src="<?php echo get_image_from_video($img); ?>" data-video-src="<?php echo esc_attr($img);?>"  alt = "<?php echo esc_attr($img);?>" />
																<div class="play-icon <?php if (portfolio_gallery_youtube_or_vimeo_portfolio($img) == 'youtube') {?> youtube-icon<?php } else {?> vimeo-icon <?php }?>"></div>
																<a class="thickbox" href="admin.php?page=portfolios_huge_it_portfolio&task=portfolio_video_edit&portfolio_id=<?php echo $rowimages->portfolio_id;?>&id=<?php echo $rowimages->id; ?>&thumb=<?php echo $i;?>&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>&TB_iframe=1&closepop=1" id="xxx">
																	<input type="button"   class="edit-video" id ="edit-video_<?php echo $rowimages->id; ?>_<?php echo $key; ?>" value="Edit" />
																</a>
																<a href="#remove" title = "<?php echo $i;?>" class="remove-image">remove</a>
															</li>
															<?php
														}
														else {?>
															<li class="editthisimage<?php echo $key; ?> <?php if($i==0){echo 'first';} ?>">
																<img src="<?php echo esc_attr($img); ?>" />
																<input type="button" class="edit-image"  id="" value="Edit" />
																<a href="#remove" title = "<?php echo $i;?>" class="remove-image">remove</a>
															</li>

															<?php
														}
														$i++;
													} ?>
													<li class="add-image-box" >
														<div class="add-thumb-project">
															<img src="<?php echo  Portfolio_Gallery()->plugin_url().'/assets/images/admin_images/plus.png'; ?>" class="plus" alt="" />
														</div>
														<div class="add-image-video">
															<input type="hidden" name="imagess<?php echo $rowimages->id; ?>" id="unique_name<?php echo $rowimages->id; ?>" class="all-urls" value="<?php echo $rowimages->image_url; ?>" />
															<a title="Add video"  class="add-video-slide thickbox" href="admin.php?page=portfolios_huge_it_portfolio&task=portfolio_video&id=<?php echo $row->id; ?>&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>&thumb_parent=<?php echo $rowimages->id;?>&TB_iframe=1"><!--</add> thumb parent is project's image id-->
																<img src="<?php echo Portfolio_Gallery()->plugin_url().'/assets/images/admin_images/icon-video.png'; ?>" title="Add video" alt="" class="plus" />
																<input type="button" class="button<?php echo $rowimages->id; ?> wp-media-buttons-icon add-video"  id="unique_name_button<?php echo $rowimages->id; ?>" value="+" />
															</a>
															<div class="add-image-slide" title="Add image">
																<img src="<?php echo  Portfolio_Gallery()->plugin_url().'/assets/images/admin_images/icon-img.png'; ?>" title="Add image" alt="" class="plus" />
																<input type="button" class="button<?php echo $rowimages->id; ?> wp-media-buttons-icon add-image"  id="unique_name_button<?php echo $rowimages->id; ?>" value="+" />
															</div>
														</div>
													</li>
												</ul>
												<script>
													jQuery(document).ready(function($){
														function secondimageslistlisize(){
															var lisaze = jQuery('#images-list').width();
															lisaze=lisaze*0.06;
															jQuery('#images-list .widget-images-list li').not('.add-image-box').not('.first').height(lisaze);
														}
														jQuery(".wp-media-buttons-icon").click(function() {
															jQuery(".attachment-filters").css("display","none");
														});
														var _custom_media = true,
															_orig_send_attachment = wp.media.editor.send.attachment;

														/*#####ADD NEW PROJECT######*/
														jQuery('.huge-it-newuploader .button').click(function(e) {
															var send_attachment_bkp = wp.media.editor.send.attachment;
															var button = jQuery(this);
															var id = button.attr('id').replace('_button', '');
															_custom_media = true;

															jQuery("#"+id).val('');
															wp.media.editor.send.attachment = function(props, attachment){
																if ( _custom_media ) {
																	jQuery("#"+id).val(attachment.url+';;;'+jQuery("#"+id).val());
																	jQuery("#save-buttom").click();
																} else {
																	return _orig_send_attachment.apply( this, [props, attachment] );
																};
															}
															wp.media.editor.open(button);
															return false;
														});

														jQuery('.widget-images-list').on('click','.edit-image',function(e) {
															jQuery(this).parents("#images-list > li").addClass('submit-post');
															var send_attachment_bkp = wp.media.editor.send.attachment;
															var $src;
															var button = jQuery(this);
															var id = button.parents('.widget-images-list').find('.all-urls').attr('id');
															var img= button.prev('img');
															_custom_media = true;
															jQuery(".media-menu .media-menu-item").css("display","none");
															jQuery(".media-menu-item:first").css("display","block");
															jQuery(".separator").next().css("display","none");
															jQuery('.attachment-filters').val('image').trigger('change');
															jQuery(".attachment-filters").css("display","none");
															wp.media.editor.send.attachment = function(props, attachment){
																if ( _custom_media ) {
																	img.attr('src',attachment.url);
																	var allurls ='';
																	img.parents('.widget-images-list').find('img').not('.plus').each(function(){
																		if(jQuery(this).hasClass('editthisvideo')) {
																			$src = jQuery(this).attr('data-video-src');
																		}
																		else $src = jQuery(this).attr('src');
																		console.log($src);
																		allurls = allurls+$src+';';
																	});
																	jQuery("#"+id).val(allurls);
																	secondimageslistlisize();
																	//jQuery("#save-buttom").click();
																} else {
																	return _orig_send_attachment.apply( this, [props, attachment] );
																};
															}
															wp.media.editor.open(button);
															return false;
														});

														jQuery('.add_media').on('click', function(){
															_custom_media = false;
														});
														/*#####ADD IMAGE######*/
														jQuery('.add-image.button<?php echo $rowimages->id; ?>').click(function(e) {
															jQuery(this).parents("#images-list > li").addClass('submit-post');
															var send_attachment_bkp = wp.media.editor.send.attachment;

															var button = jQuery(this);
															var id = button.attr('id').replace('_button', '');
															_custom_media = true;

															wp.media.editor.send.attachment = function(props, attachment){
																if ( _custom_media ) {
																	jQuery("#"+id).parent().parent().before('<li class="editthisimage1 "><img src="'+attachment.url+'" alt="" /><input type="button" class="edit-image"  id="" value="Edit" /><a href="#remove" class="remove-image">remove</a></li>');
																	//alert(jQuery("#"+id).val());
																	jQuery("#"+id).val(jQuery("#"+id).val()+attachment.url+';');

																	secondimageslistlisize();

																} else {
																	return _orig_send_attachment.apply( this, [props, attachment] );
																};
															}

															wp.media.editor.open(button);

															return false;
														});


														/*#####REMOVE IMAGE######*/
														jQuery("ul.widget-images-list").on('click','.remove-image',function () {
															jQuery(this).parents("#images-list > li").addClass('submit-post');
															jQuery(this).parent().find('img').remove();

															var allUrls="";
															var $src;

															jQuery(this).parents('ul.widget-images-list').find('img').not('.plus').each(function(){
																//console.log(jQuery(this).parent());
																if(jQuery(this).hasClass('editthisvideo')) {
																	$src = jQuery(this).attr('data-video-src');
																}
																else $src = jQuery(this).attr('src');
																console.log($src);
																allUrls=allUrls+$src+';';
																jQuery(this).parent().parent().parent().find('input.all-urls').val(allUrls);
																secondimageslistlisize();
															});
															jQuery(this).parent().remove();
															return false;
														});


														/*#####HIDE NEW UPLOADER'S LEFT MENU######*/
														jQuery(".wp-media-buttons-icon").click(function() {
															jQuery(".media-menu .media-menu-item").css("display","none");
															jQuery(".media-menu-item:first").css("display","block");
															jQuery(".separator").next().css("display","none");
															jQuery('.attachment-filters').val('image').trigger('change');
															jQuery(".attachment-filters").css("display","none");
														});
													});
												</script>
											</div>
											<div class="image-options">
												<div class="options-container">
													<div>
														<label for="titleimage<?php echo $rowimages->id; ?>"><?php echo __( 'Title', 'portfolio-gallery' );?>:</label>
														<input  class="text_area" type="text" id="titleimage<?php echo $rowimages->id; ?>" name="titleimage<?php echo $rowimages->id; ?>" id="titleimage<?php echo $rowimages->id; ?>"  value="<?php echo htmlspecialchars($rowimages->name); ?>">
													</div>
													<div class="description-block">
														<label for="im_description<?php echo $rowimages->id; ?>"><?php echo __( 'Description', 'portfolio-gallery' );?>:</label>
														<textarea id="im_description<?php echo $rowimages->id; ?>" name="im_description<?php echo $rowimages->id; ?>" ><?php echo esc_html(stripslashes($rowimages->description)); ?></textarea>
													</div>
													<div class="link-block">
														<label for="sl_url<?php echo $rowimages->id; ?>"><?php echo __( 'URL', 'portfolio-gallery' );?>:</label>
														<input class="text_area url-input" type="text" id="sl_url<?php echo $rowimages->id; ?>" name="sl_url<?php echo $rowimages->id; ?>"  value="<?php echo esc_attr($rowimages->sl_url); ?>" >
														<label class="long" for="sl_link_target<?php echo $rowimages->id; ?>">
															<span><?php echo __( 'Open in new tab', 'portfolio-gallery' );?></span>
															<input type="hidden" name="sl_link_target<?php echo $rowimages->id; ?>" value="" />
															<input  <?php if($rowimages->link_target == 'on'){ echo 'checked="checked"'; } ?>  class="link_target" type="checkbox" id="sl_link_target<?php echo $rowimages->id; ?>" name="sl_link_target<?php echo $rowimages->id; ?>" />
														</label>
													</div>
												</div>
												<div class="category-container">
													<strong><?php echo __( 'Select Categories', 'portfolio-gallery' );?></strong>
													<em>(<?php echo __( 'Press Ctrl And Select multiply', 'portfolio-gallery' );?>)</em>
													<select id="multipleSelect" multiple="multiple">
														<?php           //    var_dump($huge_cat);
														$huge_cat = explode(",",$rowimages->category);
														foreach ($myrows as $value) {
															if(!empty($value)){
																?>
																<option <?php if(in_array(str_replace(' ','_',$value),str_replace(' ','_',$huge_cat))) { echo "selected='selected' "; } ?> value="<?php echo esc_attr(str_replace(' ','_',$value)); ?>" > <!-- attrForDelete="<?php// echo str_replace(" ","_",$value); ?>" -->
																	<?php echo str_replace('_',' ',$value); ?>
																</option>
																<?php
															}
														}     ?>
														}
													</select>
													<input type="hidden" id="category<?php echo $rowimages->id; ?>" name="category<?php echo $rowimages->id; ?>" value="<?php echo esc_attr(str_replace(' ','_',$rowimages->category)); ?>"/>
												</div>
												<div class="remove-image-container">
													<a class="button remove-image" href="admin.php?page=portfolios_huge_it_portfolio&id=<?php echo $row->id; ?>&task=apply&removeslide=<?php echo $rowimages->id; ?>&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>"><?php echo __( 'Remove Project', 'portfolio-gallery' );?></a>
												</div>
											</div>
											<div class="clear"></div>
										</li>
										<?php
										break;
									case 'video'://$i = 0;?>
										<!--<add>-->
										<li <?php if($j%2==0){echo "class='has-background'";}$j++; ?>  >
											<input class="order_by" type="hidden" name="order_by_<?php echo $rowimages->id; ?>" value="<?php echo esc_attr($rowimages->ordering); ?>" />
											<div class="video-container">
												<ul class="widget-images-list">
													<?php $imgurl=explode(";",$rowimages->image_url);
													array_pop($imgurl);
													$i=0;
													foreach($imgurl as $key1=>$img)
													{
														if(portfolio_gallery_youtube_or_vimeo_portfolio($img) != 'image') {?>
															<li class="editthisvideo editthisimage<?php echo $key; ?> <?php if($i==0){echo 'first';} ?>" >
																<img class="editthisvideo" src="<?php echo esc_attr(get_image_from_video($img)); ?>"  data-video-src="<?php echo esc_attr($img);?>"  alt = "<?php echo esc_attr($img);?>"/>
																<div class="play-icon <?php if (portfolio_gallery_youtube_or_vimeo_portfolio($img) == 'youtube') {?> youtube-icon<?php } else {?> vimeo-icon <?php }?>"></div>
																<a class="thickbox" href="admin.php?page=portfolios_huge_it_portfolio&task=portfolio_video_edit&portfolio_id=<?php echo $rowimages->portfolio_id;?>&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>&id=<?php echo $rowimages->id; ?>&thumb=<?php echo $i;?>&TB_iframe=1&closepop=1" id="xxx">
																	<input type="button"   class="edit-video" id ="edit-video_<?php echo $rowimages->id; ?>_<?php echo $key; ?>" value="Edit" />
																</a>
																<a href="#remove" title = "<?php echo $i;?>" class="remove-image">remove</a>
															</li>
															<?php
														}
														else {?>
															<li class="editthisimage<?php echo $key; ?> <?php if($i==0){echo 'first';} ?>">
																<img src="<?php echo esc_attr($img); ?>" />
																<input type="button" class="edit-image"  id="" value="Edit" />
																<a href="#remove" title = "<?php echo $i;?>" class="remove-image">remove</a>
															</li>

															<?php
														}
														$i++;
													} ?>

													<li class="add-image-box">
														<div class="add-thumb-project">
															<img src="<?php echo  Portfolio_Gallery()->plugin_url().'/assets/images/admin_images/plus.png'; ?>" class="plus" alt="" />
														</div>
														<div class="add-image-video">
															<input type="hidden" name="imagess<?php echo $rowimages->id; ?>" id="unique_name<?php echo $rowimages->id; ?>" class="all-urls" value="<?php echo esc_attr($rowimages->image_url); ?>" />
															<a title="Add video"  class="add-video-slide thickbox" href="admin.php?page=portfolios_huge_it_portfolio&task=portfolio_video&id=<?php echo $row->id; ?>&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>&thumb_parent=<?php echo $rowimages->id;?>&TB_iframe=1"><!--</add> thumb parent is project's image id-->
																<img src="<?php echo  Portfolio_Gallery()->plugin_url().'/assets/images/admin_images/icon-video.png'; ?>" title="Add video" alt="" class="plus" />
																<input type="button" class="button<?php echo $rowimages->id; ?> wp-media-buttons-icon add-video"  id="unique_name_button<?php echo $rowimages->id; ?>" value="+" />
															</a>
															<div class="add-image-slide" title="Add image">
																<img src="<?php echo  Portfolio_Gallery()->plugin_url().'/assets/images/admin_images/icon-img.png'; ?>" title="Add image" alt="" class="plus"  />
																<input type="button" class="button<?php echo $rowimages->id; ?> wp-media-buttons-icon add-image"  id="unique_name_button<?php echo $rowimages->id; ?>" value="+" />
															</div>
														</div>
													</li>
												</ul>
												<script>
													jQuery(document).ready(function($){
														function secondimageslistlisize(){
															var lisaze = jQuery('#images-list').width();
															lisaze=lisaze*0.06;
															jQuery('#images-list .widget-images-list li').not('.add-image-box').not('.first').height(lisaze);
														}
														jQuery(".wp-media-buttons-icon").click(function() {
															jQuery(".attachment-filters").css("display","none");
														});
														var _custom_media = true,
															_orig_send_attachment = wp.media.editor.send.attachment;

														/*#####ADD NEW PROJECT######*/
														jQuery('.huge-it-newuploader .button').click(function(e) {
															var send_attachment_bkp = wp.media.editor.send.attachment;
															var button = jQuery(this);
															var id = button.attr('id').replace('_button', '');
															_custom_media = true;

															jQuery("#"+id).val('');
															wp.media.editor.send.attachment = function(props, attachment){
																if ( _custom_media ) {
																	jQuery("#"+id).val(attachment.url+';;;'+jQuery("#"+id).val());
																	jQuery("#save-buttom").click();
																} else {
																	return _orig_send_attachment.apply( this, [props, attachment] );
																};
															}
															wp.media.editor.open(button);
															return false;
														});

														/*#####EDIT IMAGE######*/
														jQuery('.widget-images-list').on('click','.edit-image',function(e) {
															var send_attachment_bkp = wp.media.editor.send.attachment;
															var $src;
															var button = jQuery(this);
															var id = button.parents('.widget-images-list').find('.all-urls').attr('id');
															var img= button.prev('img');
															_custom_media = true;
															jQuery(".media-menu .media-menu-item").css("display","none");
															jQuery(".media-menu-item:first").css("display","block");
															jQuery(".separator").next().css("display","none");
															jQuery('.attachment-filters').val('image').trigger('change');
															jQuery(".attachment-filters").css("display","none");
															wp.media.editor.send.attachment = function(props, attachment){
																if ( _custom_media ) {
																	img.attr('src',attachment.url);
																	var allurls ='';
																	img.parents('.widget-images-list').find('img').not('.plus').each(function(){
																		if(jQuery(this).hasClass('editthisvideo')) {
																			$src = jQuery(this).attr('data-video-src');
																		}
																		else $src = jQuery(this).attr('src');
																		console.log($src);
																		allurls = allurls+$src+';';
																	});
																	jQuery("#"+id).val(allurls);
																	secondimageslistlisize();
																	//jQuery("#save-buttom").click();
																} else {
																	return _orig_send_attachment.apply( this, [props, attachment] );
																};
															}
															wp.media.editor.open(button);
															return false;
														});

														jQuery('.add_media').on('click', function(){
															_custom_media = false;
														});

														/*#####ADD IMAGE######*/
														jQuery('.add-image.button<?php echo $rowimages->id; ?>').click(function(e) {
															var send_attachment_bkp = wp.media.editor.send.attachment;

															var button = jQuery(this);
															var id = button.attr('id').replace('_button', '');
															_custom_media = true;

															wp.media.editor.send.attachment = function(props, attachment){
																if ( _custom_media ) {
																	jQuery("#"+id).parent().parent().before('<li class="editthisimage1 "><img src="'+attachment.url+'" alt="" /><input type="button" class="edit-image"  id="" value="Edit" /><a href="#remove" class="remove-image">remove</a></li>');
																	//alert(jQuery("#"+id).val());
																	jQuery("#"+id).val(jQuery("#"+id).val()+attachment.url+';');

																	secondimageslistlisize();

																} else {
																	return _orig_send_attachment.apply( this, [props, attachment] );
																};
															}

															wp.media.editor.open(button);

															return false;
														});


														/*#####REMOVE IMAGE######*/
														jQuery("ul.widget-images-list").on('click','.remove-image',function () {
															jQuery(this).parent().find('img').remove();

															var allUrls="";
															var $src;

															jQuery(this).parents('ul.widget-images-list').find('img').not('.plus').each(function(){
																//console.log(jQuery(this).parent());
																if(jQuery(this).hasClass('editthisvideo')) {
																	$src = jQuery(this).attr('data-video-src');
																}
																else $src = jQuery(this).attr('src');
																console.log($src);
																allUrls=allUrls+$src+';';
																jQuery(this).parent().parent().parent().find('input.all-urls').val(allUrls);
																secondimageslistlisize();
															});
															jQuery(this).parent().remove();
															return false;
														});


														/*#####HIDE NEW UPLOADER'S LEFT MENU######*/
														jQuery(".wp-media-buttons-icon").click(function() {
															jQuery(".media-menu .media-menu-item").css("display","none");
															jQuery(".media-menu-item:first").css("display","block");
															jQuery(".separator").next().css("display","none");
															jQuery('.attachment-filters').val('image').trigger('change');
															jQuery(".attachment-filters").css("display","none");
														});
														/*jQuery("ul.widget-images-list").on('click','.remove-video',function () {
														 var new_video_list,del_video_number,old_video_list,old_video_array;

														 new_video_list = "";
														 del_video_number = jQuery(this).attr("title");
														 old_video_list = jQuery(this).parent().parent().find('input.all-urls').val();
														 old_video_array = old_video_list.split(";");console.log(old_video_array);

														 for(var video in old_video_array) {
														 if(video==del_video_number)
														 continue;
														 new_video_list += old_video_array[video]+";";

														 }

														 new_video_list = new_video_list.substr(0,new_video_list.length-1);

														 jQuery(this).parent().parent().find('input.video-all-urls').val(new_video_list);

														 jQuery(this).parent().remove();
														 return;
														 });*/
													});

												</script>
											</div>
											<div class="image-options">
												<div class="options-container">
													<div>
														<label for="titleimage<?php echo $rowimages->id; ?>"><?php echo __( 'Title', 'portfolio-gallery' );?>:</label>
														<input  class="text_area" type="text" id="titleimage<?php echo $rowimages->id; ?>" name="titleimage<?php echo $rowimages->id; ?>" id="titleimage<?php echo $rowimages->id; ?>"  value="<?php echo esc_html(stripslashes($rowimages->name)); ?>">
													</div>
													<div class="description-block">
														<label for="im_description<?php echo $rowimages->id; ?>"><?php echo __( 'Description', 'portfolio-gallery' );?>:</label>
														<textarea id="im_description<?php echo $rowimages->id; ?>" name="im_description<?php echo $rowimages->id; ?>" ><?php echo esc_html(stripslashes($rowimages->description)); ?></textarea>
													</div>
													<div class="link-block">
														<label for="sl_url<?php echo $rowimages->id; ?>"><?php echo __( 'URL', 'portfolio-gallery' );?>:</label>
														<input class="text_area url-input" type="text" id="sl_url<?php echo $rowimages->id; ?>" name="sl_url<?php echo $rowimages->id; ?>"  value="<?php echo esc_attr($rowimages->sl_url); ?>" >
														<label class="long" for="sl_link_target<?php echo $rowimages->id; ?>">
															<span>Open in new tab</span>
															<input type="hidden" name="sl_link_target<?php echo $rowimages->id; ?>" value="" />
															<input  <?php if($rowimages->link_target == 'on'){ echo 'checked="checked"'; } ?>  class="link_target" type="checkbox" id="sl_link_target<?php echo $rowimages->id; ?>" name="sl_link_target<?php echo $rowimages->id; ?>" />
														</label>
													</div>
												</div>
												<div class="category-container">
													<strong><?php echo __( 'Select Categories', 'portfolio-gallery' );?></strong>
													<em>(<?php echo __( 'Press Ctrl And Select multiply', 'portfolio-gallery' );?>)</em>
													<select id="multipleSelect" multiple="multiple"  >
														<?php
														$huge_cat = explode(",",$rowimages->category);
														foreach ($myrows as $value) {
															if(!empty($value))
															{ ?>
																<option <?php if(in_array(str_replace(' ','_',$value),str_replace(' ','_',$huge_cat))) { echo "selected='selected' "; } ?> value="<?php echo esc_attr(str_replace(' ','_',$value)); ?>" > <!-- attrForDelete="<?php// echo str_replace(" ","_",$value); ?>" -->
																	<?php echo str_replace('_',' ',$value); ?>
																</option>
																<?php
															}
														}     ?>
														}
													</select>
													<input type="hidden" id="category<?php echo $rowimages->id; ?>" name="category<?php echo $rowimages->id; ?>" value="<?php echo esc_attr(str_replace(' ','_',$rowimages->category)); ?>"/>
												</div>
												<div class="remove-image-container">
													<a class="button remove-image" href="admin.php?page=portfolios_huge_it_portfolio&id=<?php echo $row->id; ?>&task=apply&removeslide=<?php echo $rowimages->id; ?>&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>"><?php echo __( 'Remove Project', 'portfolio-gallery' );?></a>
												</div>
											</div>
											<div class="clear"></div>
										</li>
										<!--</add>-->
										<?php break;?>
									<?php	} ?>
							<?php } ?>
						</ul>
					</div>

				</div>
				<script>
					jQuery('.category-container select').change(function(){
						var cat_new_val = jQuery(this).val();
						var new_cat_name = jQuery(this).parent().find('input').attr('name');
						jQuery('#'+new_cat_name).attr('value',cat_new_val+',');
						//console.log(cat_new_val);  console.log(new_cat_name);
					});
					//ok  
					jQuery(document).on('click', '#add_new_cat_buddon', function () {
						var newCatVal =  jQuery('.inside #add_cat_input input').val();
						if(newCatVal !== "") {
							var oldValue = jQuery('.inside input:hidden').val()
							var newValue = oldValue + newCatVal + ',';
							jQuery('.inside input:hidden').val(newValue.replace(/ /g,"_"));
							jQuery('.inside #add_cat_input input').val('');
							jQuery('.inside > ul').find('#allCategories').before(
								"<li class='hndle'><input class='del_val' value='"+newCatVal+"' style=''>"+"<span id='delete_cat' style='' value='a'><img src='<?php echo PORTFOLIO_GALLERY_IMAGES_URL."/admin_images/delete1.png";?>' width='9' height='9' value='a'>"+
								"</span><span id='edit_cat' style=''><img src='<?php echo PORTFOLIO_GALLERY_IMAGES_URL."/admin_images/edit3.png";?>' width='10' height='10'>"+
								"</span></li>");

							jQuery('.category-container #multipleSelect').each(function(){
								jQuery(this).append("<option attrForDelete='"+newCatVal+"'>"+newCatVal+"</option>");
							});
						}
						else { alert("Please fill the line"); }
					});
					//  ok a 
					jQuery(document).on('click', '#delete_cat', function (){
						var del_val = jQuery(this).parent().find('.del_val').val().replace(/ /g, '_');
						del_val = del_val + ",";
						var old_val_for_delete = jQuery('.inside input:hidden').val();
						var newValue = old_val_for_delete.replace(del_val, "");
						jQuery('.inside input:hidden').val(newValue);
						jQuery(this).parents("li").remove();
						var valForDelete = del_val.replace(',', '').replace(/ /g, '_');
						jQuery('.category-container').each(function(){
							jQuery(this).find('option[value='+valForDelete+']').remove();
						});
					});
					//ok a

					jQuery(document).on('click', '#edit_cat', function (){
						jQuery(this).parent().find('.del_val').focus();
						var changing_val = jQuery(this).parent().find('.del_val').val().replace(/ /g, '_');
						jQuery('#changing_val').removeAttr('value').attr('value',changing_val);
						//console.log(changing_val);
					});
					//ok a

					jQuery(document).on('click', '#portfolios-list .active', function (){
						jQuery(this).find('input').focus();
					});

					//getting category old name
					jQuery(document).on('focus', '.del_val', function (){ // Know which category we want to change 
						var changing_val = jQuery(this).val().replace(/ /g,"_");  //console.log(changing_val);
						jQuery('#changing_val').removeAttr('value').attr('value',changing_val);
					});

					jQuery(document).on('change', '.del_val', function (){
						//alert("ok")
						var no_edited_cats = jQuery("#allCategories").val().replace(/ /g,"_");
						var old_name = jQuery('#changing_val').val();
						var edited_cat = jQuery(this).val();
						edited_cat = edited_cat.replace(/ /g,"_");
						var new_cat = no_edited_cats.replace(old_name,edited_cat);
						jQuery('#allCategories').val(new_cat);  // console.log(no_edited_cats); console.log(old_name); console.log(edited_cat); console.log(new_cat);
					});

				</script>

				<!-- SIDEBAR -->
				<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables ui-sortable">
						<div id="portfolio-unique-options" class="postbox">
							<h3 class="hndle"><span><?php echo __( 'Select The Portfolio/Gallery View', 'portfolio-gallery' );?></span></h3>
							<ul id="portfolio-unique-options-list">
								<li>
									<label for="huge_it_portfolio_name"><?php echo __( 'Portfolio Name', 'portfolio-gallery' );?></label>
									<input type = "text" name="name" id="huge_it_portfolio_name" value="<?php echo esc_html(stripslashes($row->name));?>" onkeyup = "name_changeRight(this)">
								</li>
								<li style="display:none;">
									<label for="sl_width"><?php echo __( 'The requested action is not valid.', 'portfolio-gallery' );?></label>
									<input type="text" name="sl_width" id="sl_width" value="1111" class="text_area" />
								</li>
								<li style="display:none;">
									<label for="sl_height"><?php echo __( 'Height', 'portfolio-gallery' );?></label>
									<input type="text" name="sl_height" id="sl_height" value="<?php echo esc_html(stripslashes($row->sl_height)); ?>" class="text_area" />
								</li>
								<li style="display:none;">
									<label for="pause_on_hover"><?php echo __( 'Pause on hover', 'portfolio-gallery' );?></label>
									<input type="hidden" value="off" name="pause_on_hover" />
									<input type="checkbox" name="pause_on_hover"  value="on" id="pause_on_hover"  <?php if($row->pause_on_hover  == 'on'){ echo 'checked="checked"'; } ?> />
								</li>
								<li>
									<label for="portfolio_effects_list"><?php echo __( 'Select The View', 'portfolio-gallery' );?></label>
									<select name="portfolio_effects_list" id="portfolio_effects_list">
										<option <?php if($row->portfolio_list_effects_s == '0'){ echo 'selected'; } ?>  value="0"><?php echo __( 'Blocks Toggle Up/Down', 'portfolio-gallery' );?></option>
										<option <?php if($row->portfolio_list_effects_s == '1'){ echo 'selected'; } ?>  value="1"><?php echo __( 'Full-Height Blocks', 'portfolio-gallery' );?></option>
										<option <?php if($row->portfolio_list_effects_s == '2'){ echo 'selected'; } ?>  value="2"><?php echo __( 'Gallery/Content-Popup', 'portfolio-gallery' );?></option>
										<option <?php if($row->portfolio_list_effects_s == '3'){ echo 'selected'; } ?>  value="3"><?php echo __( 'Full-Width Blocks', 'portfolio-gallery' );?></option>
										<option <?php if($row->portfolio_list_effects_s == '4'){ echo 'selected'; } ?>  value="4"><?php echo __( 'FAQ Toggle Up/Down', 'portfolio-gallery' );?></option>
										<option <?php if($row->portfolio_list_effects_s == '5'){ echo 'selected'; } ?>  value="5"><?php echo __( 'Content Slider', 'portfolio-gallery' );?></option>
										<option <?php if($row->portfolio_list_effects_s == '6'){ echo 'selected'; } ?>  value="6"><?php echo __( 'Lightbox-Gallery', 'portfolio-gallery' );?></option>
									</select>
								</li>

								<li style="display:none;" class="for-content-slider">
									<label for="sl_pausetime"><?php echo __( 'Pause time', 'portfolio-gallery' );?></label>
									<input type="text" name="sl_pausetime" id="sl_pausetime" value="<?php echo esc_html(stripslashes($row->description)); ?>" class="text_area" />
								</li>
								<li style="display:none;"  class="for-content-slider">
									<label for="sl_changespeed"><?php echo __( 'Change speed', 'portfolio-gallery' );?></label>
									<input type="text" name="sl_changespeed" id="sl_changespeed" value="<?php echo esc_html(stripslashes($row->param)); ?>" class="text_area" />
								</li>
								<li class="no-content-slider no-full-width">
									<label for="slider_effect"><?php echo __( 'Show In Center', 'portfolio-gallery' );?></label>
									<select name="sl_position" id="slider_effect">
										<option <?php if($row->sl_position == 'off'){ echo 'selected'; }?> value="off">Off</option>
										<option <?php if($row->sl_position == 'on'){ echo 'selected'; }?> value="on">On</option>

									</select>
								</li>
								<li style="display:none;margin-top:10px"  class="for-content-slider">
									<label for="pause_on_hover"><?php echo __( 'Pause On Hover ', 'portfolio-gallery' );?></label>
									<input type="hidden" value="off" name="pause_on_hover" />
									<input type="checkbox" name="pause_on_hover"  value="on" id="pause_on_hover"  <?php if($row->pause_on_hover  == 'on'){ echo 'checked="checked"'; } ?> />
								</li>
								<li style="display:none;margin-top:10px"  class="for-content-slider">
									<label for="autoslide"><?php echo __( 'Autoslide ', 'portfolio-gallery' );?></label>
									<input type="hidden" value="off" name="autoslide" />
									<input type="checkbox" name="autoslide"  value="on" id="autoslide"  <?php if($row->autoslide  == 'on'){ echo 'checked="checked"'; } ?> />
								</li>
							</ul>
							<div id="major-publishing-actions">
								<div id="publishing-action">
									<input type="button" onclick="submitbutton('apply')" value="Save Portfolio" id="save-buttom" class="button button-primary button-large">
								</div>
								<div class="clear"></div>
								<!--<input type="button" onclick="window.location.href='admin.php?page=portfolios_huge_it_portfolio'" value="Cancel" class="button-secondary action">-->
							</div>
						</div>

						<div class="postbox">
							<div class="inside2">
								<ul>
									<li class="allowIsotope">
										<?php echo __( ' Show Sorting Buttons', 'portfolio-gallery' );?> :
										<input type="hidden" value="off" name="ht_show_sorting" />
										<input type="checkbox" id="ht_show_sorting"  <?php if($row->ht_show_sorting  == 'on'){ echo 'checked="checked"'; } ?>  name="ht_show_sorting" value="on" />
									</li>
									<li class="allowIsotope">
										<?php echo __( ' Show Category Buttons', 'portfolio-gallery' );?> :
										<input type="hidden" value="off" name="ht_show_filtering" />
										<input type="checkbox" id="ht_show_filtering"  <?php if($row->ht_show_filtering  == 'on'){ echo 'checked="checked"'; } ?>  name="ht_show_filtering" value="on" />
									</li>
								</ul>
							</div>
						</div>

						<div class="postbox">
							<h3 class="hndle"><span><?php echo __( 'Categories', 'portfolio-gallery' );?></span></h3>
							<div class="inside">
								<ul>
									<?php
									$ifforempty= $row->categories;
									$ifforempty= stripslashes($ifforempty);
									$ifforempty= esc_html($ifforempty);
									$ifforempty= empty($ifforempty);
									if(!($ifforempty))
									{
										foreach ($myrows as $value) {
											if(!empty($value))
											{
												?>

												<li class="hndle">
													<input class="del_val" value="<?php echo str_replace("_", " ", $value); ?>" style="">
													<span id="delete_cat" style="" value="a"><img src="<?php echo PORTFOLIO_GALLERY_IMAGES_URL."/admin_images/delete1.png";?>" width="9" height="9" value="a"></span>
													<span id="edit_cat" style=""><img src='<?php echo PORTFOLIO_GALLERY_IMAGES_URL."/admin_images/edit3.png"; ?>' width="10" height="10"></span>
												</li>
												<?php
											}
										}
									}

									?>
									<input type="hidden" value="<?php if (strpos($row->categories,',,') !== false)  { $row->categories = str_replace(",,",",",$row->categories); }echo esc_attr($row->categories); ?>" id="allCategories" name="allCategories">
									<li id="add_cat_input" style="">
										<input type="text" size="12">
										<a style="" id="add_new_cat_buddon">+<?php echo __( 'Add New Category', 'portfolio-gallery' );?></a>
									</li>
								</ul>
								<input type="hidden" value="" id="changing_val">
							</div>
						</div>

						<div class="postbox" >
							<h3 class="hndle"><span><?php echo __( 'Loading Icons', 'portfolio-gallery' );?></span></h3>
							<div class="inside">
								<ul id="portfolio-unique-options-list" class="for_loading">
									<li>
										<label><?php echo __( ' Show Loading Icon', 'portfolio-gallery' );?> :</label>
										<input type="hidden" value="off" name="show_loading" />
										<input type="checkbox" id="show_loading"  <?php if($row->show_loading  == 'on'){ echo 'checked="checked"'; } ?>  name="show_loading" value="on" />
									</li>
									<li class="loading_opton">
										<label for="portfolio_load_icon" style="width: 100%"><?php echo __( 'Image while portfolio loads:', 'portfolio-gallery' );?></label>
										<ul id="portfolio-loading-icon">
											<li <?php if($row->loading_icon_type == 1){ echo 'class="act"'; } ?>>
												<label for="loading_icon_type_1">
													<div class="image-block-icon">
														<img src="<?php echo $path_site2; ?>/loading/loading-1.svg" alt="" />
													</div>
													<input type="radio" id="loading_icon_type_1" name="loading_icon_type" value="1" <?php if($row->loading_icon_type == 1){ echo 'checked="checked"'; } ?>>
												</label>
											</li>
											<li <?php if($row->loading_icon_type == 2){ echo 'class="act"'; } ?>>
												<label for="loading_icon_type_2">
													<div class="image-block-icon">
														<img src="<?php echo $path_site2; ?>/loading/loading-2.svg" alt="" />
													</div>
													<input type="radio" id="loading_icon_type_2" name="loading_icon_type" value="2" <?php if($row->loading_icon_type == 2){ echo 'checked="checked"'; } ?>>
												</label>
											</li>
											<li <?php if($row->loading_icon_type == 3){ echo 'class="act"'; } ?>>
												<label for="loading_icon_type_3">
													<div class="image-block-icon">
														<img src="<?php echo $path_site2; ?>/loading/loading-3.svg" alt="" />
													</div>
													<input type="radio" id="loading_icon_type_3" name="loading_icon_type" value="3" <?php if($row->loading_icon_type == 3){ echo 'checked="checked"'; } ?>>
												</label>
											</li>
											<li <?php if($row->loading_icon_type == 4){ echo 'class="act"'; } ?>>
												<label for="loading_icon_type_4">
													<div class="image-block-icon">
														<img src="<?php echo $path_site2; ?>/loading/loading-4.svg" alt="" />
													</div>
													<input type="radio" id="loading_icon_type_4" name="loading_icon_type" value="4" <?php if($row->loading_icon_type == 4){ echo 'checked="checked"'; } ?>>
												</label>
											</li>
										</ul>
									</li>
								</ul>
							</div>
						</div>

						<div id="portfolio-shortcode-box" class="postbox shortcode ms-toggle">
							<h3 class="hndle"><span><?php echo __( 'Usage', 'portfolio-gallery' );?></span></h3>
							<div class="inside">
								<ul>
									<li rel="tab-1" class="selected">
										<h4><?php echo __( 'Shortcode', 'portfolio-gallery' );?></h4>
										<p><?php echo __( 'Copy &amp; paste the shortcode directly into any WordPress post or page', 'portfolio-gallery' );?>.</p>
										<textarea class="full" readonly="readonly">[huge_it_portfolio id="<?php echo $row->id; ?>"]</textarea>
									</li>
									<li rel="tab-2">
										<h4><?php echo __( 'Template Include', 'portfolio-gallery' );?></h4>
										<p><?php echo __( 'Copy &amp; paste this code into a template file to include the slideshow within your theme', 'portfolio-gallery' );?>.</p>
										<textarea class="full" readonly="readonly">&lt;?php echo do_shortcode("[huge_it_portfolio id='<?php echo $row->id; ?>']"); ?&gt;</textarea>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo wp_nonce_field('huge_it_portfolio_nonce','huge_it_portfolio_nonce')?>
		<input type="hidden" name="task" value="" />
	</form>
</div>