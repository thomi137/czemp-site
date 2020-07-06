<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$wp_nonce = $_GET['huge_it_portfolio_nonce'];
if(!wp_verify_nonce($wp_nonce, 'huge_it_portfolio_nonce')) {
	wp_die('Security check fail');
}
$portfolio_wp_nonce = wp_create_nonce('huge_it_portfolio_nonce');
global $wpdb;
$id = intval($_GET['id']);
?>
<style>
	html.wp-toolbar {
		padding:0px !important;
	}
	#wpadminbar,#adminmenuback,#screen-meta, .update-nag,#dolly {
		display:none;
	}
	#wpbody-content {
		padding-bottom:30px;
	}
	#adminmenuwrap {display:none !important;}
	.auto-fold #wpcontent, .auto-fold #wpfooter {
		margin-left: 0px;
	}
	#wpfooter {display:none;}
	iframe {height:250px !important;}
	#TB_window {height:250px !important;}
</style>
<script type="text/javascript">
	function youtube_parser(url){
		var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
		var match = url.match(regExp);
		var match_vimeo = /vimeo.*\/(\d+)/i.exec( url );
		if (match&&match[7].length==11){
			return match[7];
		}else if(match_vimeo) {
			return match_vimeo[1];
		}
		else {
			return false;
		}
	}

	jQuery(document).ready(function() {

		jQuery('.huge-it-insert-video-button').click(function(){
			var ID1 = jQuery('#huge_it_add_video_input').val();
			if(ID1==""){alert("Please copy and past url form Youtube or Vimeo to insert into slider.");return false;}
			if (youtube_parser(ID1) == false) {
				alert("Url is incorrect");
				return false;
			}
			jQuery('.huge-it-insert-post-button').on('click', function() {
				var ID1 = jQuery('#huge_it_add_video_input').val();
				if(ID1==""){alert("Please copy and past url form youtube or Vimeo to insert into slider.");return false;}

				window.parent.uploadID.val(ID1);

				tb_remove();
				//	jQuery("#save-buttom").click();
			});

			jQuery('#huge_it_add_video_input').change(function(){

				if (jQuery(this).val().indexOf("youtube") >= 0){
					jQuery('#add-video-popup-options > div').removeClass('active');
					jQuery('#add-video-popup-options  .youtube').addClass('active');
				}else if (jQuery(this).val().indexOf("vimeo") >= 0){
					jQuery('#add-video-popup-options > div').removeClass('active');
					jQuery('#add-video-popup-options  .vimeo').addClass('active');
				}else {
					jQuery('#add-video-popup-options > div').removeClass('active');
					jQuery('#add-video-popup-options  .error-message').addClass('active');
				}
			})
		});
		<?php
		if(isset($_GET["closepop"])){
		if($_GET["closepop"] == 1){ ?>
		jQuery("#closepopup").click();
		self.parent.location.reload();
		<?php	}	} ?>
		jQuery('.updated').css({"display":"none"});
	});
</script>
<a id="closepopup"  onclick=" parent.eval('tb_remove()')" style="display:none;"> [X] </a>

<div id="huge_it_slider_add_videos">
	<div id="huge_it_slider_add_videos_wrap">
		<h2><?php echo __( 'Add Video URL From Youtube or Vimeo', 'portfolio-gallery' );?></h2>
		<div class="control-panel">
			<?php if (!isset($_GET['thumb_parent'])) { ?>
				<form method="post" action="admin.php?page=portfolios_huge_it_portfolio&task=portfolio_video&id=<?php echo $id; ?>&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>&closepop=1" >
					<input type="text" id="huge_it_add_video_input" name="huge_it_add_video_input" />
					<button class='save-slider-options button-primary huge-it-insert-video-button' id='huge-it-insert-video-button'><?php echo __( 'Insert Video Slide', 'portfolio-gallery' );?></button>
					<div id="add-video-popup-options">
						<div>
							<div>
								<label for="show_title"><?php echo __( 'Title', 'portfolio-gallery' );?>:</label>
								<div>
									<input name="show_title" value="" type="text" />
								</div>
							</div>
							<div>
								<label for="show_description"><?php echo __( 'Description', 'portfolio-gallery' );?>:</label>
								<textarea id="show_description" name="show_description"></textarea>
							</div>
							<div>
								<label for="show_url"><?php echo __( 'Url', 'portfolio-gallery' );?>:</label>
								<input type="text" name="show_url" value="" />
							</div>
						</div>
					</div>
				</form>
			<?php } else { $thumb_parent = $_GET["thumb_parent"] //get project image's id and sent to form by get ,who addes thumb_video by $_get thumb_parent ?>
				<form method="post" action="admin.php?page=portfolios_huge_it_portfolio&task=portfolio_video&id=<?php echo $id; ?>&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>&thumb_parent=<?php echo $thumb_parent ; ?>&closepop=1" >
					<input type="text" id="huge_it_add_video_input" name="huge_it_add_video_input" />
					<button class='save-slider-options button-primary huge-it-insert-video-button' id='huge-it-insert-video-button'><?php echo __( 'Insert Video Slide', 'portfolio-gallery' );?></button>
				</form>
			<?php } ?>
		</div>
	</div>
</div>