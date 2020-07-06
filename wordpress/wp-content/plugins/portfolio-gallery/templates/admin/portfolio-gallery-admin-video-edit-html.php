<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$wp_nonce = $_GET['huge_it_portfolio_nonce'];
if(!wp_verify_nonce($wp_nonce, 'huge_it_portfolio_nonce')) {
	wp_die('Security check fail');
}
$portfolio_wp_nonce = wp_create_nonce('huge_it_portfolio_nonce');
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
	iframe {height:150px !important;}
	#TB_window {height:150px !important;}
	.html5-video-player:not(.ad-interrupting):not(.hide-info-bar) .html5-info-bar {
		display: none !important;
	}
	.iframe-text-area {
		float: left;
	}
	.iframe-area {
		float: left;
		height: 100%;
		width: 40%;
		margin: 5px;
	}
	.text-area {
		float: left;
		width: 50%;
		margin: 5px;
	}
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
				//var ID1 = jQuery('#huge_it_add_video_input').val();

				window.parent.uploadID.val(ID1);

				tb_remove();
				//jQuery("#save-buttom").click();
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
	/***add***/
	jQuery(function($) {

		jQuery(".set-new-video").on('click',function() {
			var showcontrols,prefix;
			var new_video = jQuery("#huge_it_add_video_input").val();
			//alert(new_video);return;
			var new_video_id= youtube_parser(new_video);
			if(!new_video_id)
				return;
			if(new_video_id.length == 11) {
				showcontrols = "?modestbranding=1&showinfo=0&controls=0";
				prefix = "//www.youtube.com/embed/";
			}
			else {
				showcontrols = "?title=0&amp;byline=0&amp;portrait=0";
				prefix = "//player.vimeo.com/video/";

			}
			var old_url =jQuery(".text-area");



			jQuery(".iframe-area").fadeOut(300);
			old_url.html("");
			jQuery(".text-area").html(new_video);
			jQuery(".iframe-area").attr("src",prefix+new_video_id+showcontrols);
			jQuery("#huge_it_edit_video_input").val(prefix+new_video_id+showcontrols);
			jQuery(".iframe-area").fadeIn(1000);
		})
	});
</script>
<h1><?php echo __( 'Update video', 'portfolio-gallery' );?></h1>
<form method="post" action="admin.php?page=portfolios_huge_it_portfolio&task=portfolio_video_edit&portfolio_id=<?php echo $portfolio_id;?>&id=<?php echo $id; ?>&huge_it_portfolio_nonce=<?php echo $portfolio_wp_nonce; ?>&thumb=<?php echo $thumb;?>&TB_iframe=1&closepop=1" >
	<div class="iframe-text-area">
		<?php if($edit == '') { ?>
			<iframe class="iframe-area" src="<?php if($video == 'youtube') { ?>//www.youtube.com/embed/<?php echo esc_attr($video_id[0]); ?>?modestbranding=1&showinfo=0&controls=0
 <?php }
			else { ?>//player.vimeo.com/video/<?php echo $video_id[0]; ?>?title=0&amp;byline=0&amp;portrait=0 <?php } ?>" frameborder="0" allowfullscreen></iframe>
		<?php } else  { ?>
			<iframe class="iframe-area" src=<?php echo $edit;?>  frameborder="0" allowfullscreen></iframe>
		<?php } ?>
		<textarea rows="4" cols="50" class="text-area" disabled >
<?php echo esc_html(stripslashes($input_edit_video_thumb));?>
	</textarea>
		<input type="text" id="huge_it_add_video_input" name="huge_it_add_video_input" value="" placeholder = "New video url" /><br />
		<input type="hidden" id="huge_it_edit_video_input" name="huge_it_edit_video_input" value="" placeholder = "New video url" /><br />
	</div>
	<a class='button-primary set-new-video'><?php echo __( 'See New Video', 'portfolio-gallery' );?></a>
	<button class='save-slider-options button-primary huge-it-insert-video-button' id='huge-it-insert-video-button'><?php echo __( 'Insert Video Slide', 'portfolio-gallery' );?></button>
</form>