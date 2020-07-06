<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<script>
	jQuery(document).ready(function () {
		popupsizes(jQuery('#light_box_size_fix'));
		function popupsizes(checkbox){
			if(checkbox.is(':checked')){
				jQuery('.lightbox-options-block .not-fixed-size').css({'display':'none'});
				jQuery('.lightbox-options-block .fixed-size').css({'display':'block'});
			}else {
				jQuery('.lightbox-options-block .fixed-size').css({'display':'none'});
				jQuery('.lightbox-options-block .not-fixed-size').css({'display':'block'});
			}
		}
		jQuery('#light_box_size_fix').change(function(){
			popupsizes(jQuery(this));
		});


		jQuery('input[data-slider="true"]').bind("slider:changed", function (event, data) {
			jQuery(this).parent().find('span').html(parseInt(data.value)+"%");
			jQuery(this).val(parseInt(data.value));
		});
	});
</script>
<?php $path_site2 = plugins_url("../images", __FILE__); ?>
<div id="post-body-heading">
	<h3><?php echo __( 'Lightbox Options', 'portfolio-gallery' );?></h3>
	<a onclick="document.getElementById('adminForm').submit()" onclick="" class="save-portfolio-options button-primary"><?php echo __( 'Save', 'portfolio-gallery' );?></a>
</div>
<form action="admin.php?page=Options_portfolio_lightbox_styles&task=save" method="post" id="adminForm" name="adminForm">
	<div class="lightbox-options-block">
		<h3>Internationalization</h3>
		<?php include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		?>
		<div class="has-background">
			<label for="light_box_style"><?php echo __( 'Lightbox style', 'portfolio-gallery' );?></label>
			<select id="light_box_style" name="params[light_box_style]">
				<option <?php if($param_values['light_box_style'] == '1'){ echo 'selected="selected"'; } ?> value="1">1</option>
				<option <?php if($param_values['light_box_style'] == '2'){ echo 'selected="selected"'; } ?> value="2">2</option>
				<option <?php if($param_values['light_box_style'] == '3'){ echo 'selected="selected"'; } ?> value="3">3</option>
				<option <?php if($param_values['light_box_style'] == '4'){ echo 'selected="selected"'; } ?> value="4">4</option>
				<option <?php if($param_values['light_box_style'] == '5'){ echo 'selected="selected"'; } ?> value="5">5</option>
			</select>
		</div>
		<?php  ?>
		<div>
			<label for="light_box_transition"><?php echo __( 'Transition type', 'portfolio-gallery' );?></label>
			<select id="light_box_transition" name="params[light_box_transition]">
				<option <?php if($param_values['light_box_transition'] == 'elastic'){ echo 'selected="selected"'; } ?> value="elastic"><?php echo __( 'Elastic', 'portfolio-gallery' );?></option>
				<option <?php if($param_values['light_box_transition'] == 'fade'){ echo 'selected="selected"'; } ?> value="fade"><?php echo __( 'Fade', 'portfolio-gallery' );?></option>
				<option <?php if($param_values['light_box_transition'] == 'none'){ echo 'selected="selected"'; } ?> value="none"><?php echo __( 'None', 'portfolio-gallery' );?></option>
			</select>
		</div>
		<div class="has-background">
			<label for="light_box_speed"><?php echo __( 'Opening speed', 'portfolio-gallery' );?></label>
			<input type="number" name="params[light_box_speed]" id="light_box_speed" value="<?php echo $param_values["light_box_speed"]; ?>" class="text">
			<span>ms</span>
		</div>
		<div>
			<label for="light_box_fadeout"><?php echo __( 'Closing speed', 'portfolio-gallery' );?></label>
			<input type="number" name="params[light_box_fadeout]" id="light_box_fadeout" value="<?php echo $param_values["light_box_fadeout"]; ?>" class="text">
			<span>ms</span>
		</div>
		<div class="has-background">
			<label for="light_box_title"><?php echo __( 'Show the title', 'portfolio-gallery' );?></label>
			<input type="hidden" value="false" name="params[light_box_title]" />
			<input type="checkbox" id="light_box_title"  <?php if($param_values['light_box_title']  == 'true'){ echo 'checked="checked"'; } ?>  name="params[light_box_title]" value="true" />
		</div>
		<div>
			<label for="light_box_opacity"><?php echo __( 'Overlay transparency', 'portfolio-gallery' );?></label>
			<div class="slider-container">
				<input name="params[light_box_opacity]" id="light_box_opacity" data-slider-highlight="true"  data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true" value="<?php echo $param_values['light_box_opacity']; ?>" />
				<span><?php echo $param_values['light_box_opacity']; ?>%</span>
			</div>
		</div>
		<div class="has-background">
			<label for="light_box_open"><?php echo __( 'Auto open', 'portfolio-gallery' );?></label>
			<input type="hidden" value="false" name="params[light_box_open]" />
			<input type="checkbox" id="light_box_open"  <?php if($param_values['light_box_open']  == 'true'){ echo 'checked="checked"'; } ?>  name="params[light_box_open]" value="true" />
		</div>
		<div>
			<label for="light_box_overlayclose"><?php echo __( 'Overlay close', 'portfolio-gallery' );?> <?php if($param_values['light_box_overlayclose']){ echo "true"; } ?></label>
			<input type="hidden" value="false" name="params[light_box_overlayclose]" />
			<input type="checkbox" id="light_box_overlayclose"  <?php if($param_values['light_box_overlayclose']  == 'true'){ echo 'checked="checked"'; } ?>  name="params[light_box_overlayclose]" value="true" />
		</div>
		<div class="has-background">
			<label for="light_box_esckey"><?php echo __( 'EscKey close', 'portfolio-gallery' );?></label>
			<input type="hidden" value="false" name="params[light_box_esckey]" />
			<input type="checkbox" id="light_box_esckey"  <?php if($param_values['light_box_esckey']  == 'true'){ echo 'checked="checked"'; } ?>  name="params[light_box_esckey]" value="true" />
		</div>
		<div>
			<label for="light_box_arrowkey"><?php echo __( 'Keyboard navigation', 'portfolio-gallery' );?></label>
			<input type="hidden" value="false" name="params[light_box_arrowkey]" />
			<input type="checkbox" id="light_box_arrowkey"  <?php if($param_values['light_box_arrowkey']  == 'true'){ echo 'checked="checked"'; } ?>  name="params[light_box_arrowkey]" value="true" />
		</div>
		<div class="has-background">
			<label for="light_box_loop"><?php echo __( 'Loop content', 'portfolio-gallery' );?></label>
			<input type="hidden" value="false" name="params[light_box_loop]" />
			<input type="checkbox" id="light_box_loop"  <?php if($param_values['light_box_loop']  == 'true'){ echo 'checked="checked"'; } ?>  name="params[light_box_loop]" value="true" />
		</div>
		<div>
			<label for="light_box_closebutton"><?php echo __( 'Show close button', 'portfolio-gallery' );?></label>
			<input type="hidden" value="false" name="params[light_box_closebutton]" />
			<input type="checkbox" id="light_box_closebutton"  <?php if($param_values['light_box_closebutton']  == 'true'){ echo 'checked="checked"'; } ?>  name="params[light_box_closebutton]" value="true" />
		</div>
	</div>
	<div class="lightbox-options-block">
		<h3><?php echo __( 'Dimensions', 'portfolio-gallery' );?></h3>

		<div class="has-background">
			<label for="light_box_size_fix"><?php echo __( 'Popup size fix', 'portfolio-gallery' );?></label>
			<input type="hidden" value="false" name="params[light_box_size_fix]" />
			<input type="checkbox" id="light_box_size_fix"  <?php if($param_values['light_box_size_fix']  == 'true'){ echo 'checked="checked"'; } ?>  name="params[light_box_size_fix]" value="true" />
		</div>

		<div class="fixed-size" >
			<label for="light_box_width"><?php echo __( 'Popup width', 'portfolio-gallery' );?></label>
			<input type="number" name="params[light_box_width]" id="light_box_width" value="<?php echo $param_values['light_box_width']; ?>" class="text">
			<span>px</span>
		</div>

		<div class="has-background fixed-size">
			<label for="light_box_height"><?php echo __( 'Popup height', 'portfolio-gallery' );?></label>
			<input type="number" name="params[light_box_height]" id="light_box_height" value="<?php echo $param_values['light_box_height']; ?>" class="text">
			<span>px</span>
		</div>

		<div class="not-fixed-size">
			<label for="light_box_maxwidth"><?php echo __( 'Popup maxWidth', 'portfolio-gallery' );?></label>
			<input type="number" name="params[light_box_maxwidth]" id="light_box_maxwidth" value="<?php echo $param_values['light_box_maxwidth']; ?>" class="text">
			<span>px</span>
		</div>
		<div class="has-background not-fixed-size">
			<label for="light_box_maxheight"><?php echo __( 'Popup maxHeight', 'portfolio-gallery' );?></label>
			<input type="number" name="params[light_box_maxheight]" id="light_box_maxheight" value="<?php echo $param_values['light_box_maxheight']; ?>" class="text">
			<span>px</span>
		</div>
		<div>
			<label for="light_box_initialwidth"><?php echo __( 'Popup initial width', 'portfolio-gallery' );?></label>
			<input type="number" name="params[light_box_initialwidth]" id="light_box_initialwidth" value="<?php echo $param_values['light_box_initialwidth']; ?>" class="text">
			<span>px</span>
		</div>
		<div class="has-background">
			<label for="light_box_initialheight"><?php echo __( 'Popup initial height', 'portfolio-gallery' );?></label>
			<input type="number" name="params[light_box_initialheight]" id="light_box_initialheight" value="<?php echo $param_values['light_box_initialheight']; ?>" class="text">
			<span>px</span>
		</div>
	</div>
	<div class="lightbox-options-block">
		<h3>Slideshow</h3>

		<div class="has-background">
			<label for="light_box_slideshow"><?php echo __( 'Slideshow', 'portfolio-gallery' );?></label>
			<input type="hidden" value="false" name="params[light_box_slideshow]" />
			<input type="checkbox" id="light_box_slideshow"  <?php if($param_values['light_box_slideshow']  == 'true'){ echo 'checked="checked"'; } ?>  name="params[light_box_slideshow]" value="true" />
		</div>
		<div>
			<label for="light_box_slideshowspeed"><?php echo __( 'Slideshow interval', 'portfolio-gallery' );?></label>
			<input type="number" name="params[light_box_slideshowspeed]" id="light_box_slideshowspeed" value="<?php echo $param_values['light_box_slideshowspeed']; ?>" class="text">
			<span>ms</span>
		</div>
		<div class="has-background">
			<label for="light_box_slideshowauto"><?php echo __( 'Slideshow auto start', 'portfolio-gallery' );?></label>
			<input type="hidden" value="false" name="params[light_box_slideshowauto]" />
			<input type="checkbox" id="light_box_slideshowauto"  <?php if($param_values['light_box_slideshowauto']  == 'true'){ echo 'checked="checked"'; } ?>  name="params[light_box_slideshowauto]" value="true" />
		</div>
		<div>
			<label for="light_box_slideshowstart"><?php echo __( 'Slideshow start button text', 'portfolio-gallery' );?></label>
			<input type="text" name="params[light_box_slideshowstart]" id="light_box_slideshowstart" value="<?php echo esc_attr($param_values['light_box_slideshowstart']); ?>" class="text">
		</div>
		<div class="has-background">
			<label for="light_box_slideshowstop"><?php echo __( 'Slideshow stop button text', 'portfolio-gallery' );?></label>
			<input type="text" name="params[light_box_slideshowstop]" id="light_box_slideshowstop" value="<?php echo esc_attr($param_values['light_box_slideshowstop']); ?>" class="text">
		</div>
	</div>
	<div class="lightbox-options-block">
		<h3>Positioning</h3>

		<div class="has-background">
			<label for="light_box_fixed"><?php echo __( 'Fixed position', 'portfolio-gallery' );?></label>
			<input type="hidden" value="false" name="params[light_box_fixed]" />
			<input type="checkbox" id="light_box_fixed"  <?php if($param_values['light_box_fixed']  == 'true'){ echo 'checked="checked"'; } ?>  name="params[light_box_fixed]" value="true" />
		</div>
		<div class="has-height">
			<label for=""><?php echo __( 'Popup position', 'portfolio-gallery' );?></label>
			<div>
				<table class="bws_position_table">
					<tbody>
					<tr>
						<td><input type="radio" value="1" id="slideshow_title_top-left" name="params[slider_title_position]" <?php if($param_values['slider_title_position'] == '1'){ echo 'checked="checked"'; } ?> /></td>
						<td><input type="radio" value="2" id="slideshow_title_top-center" name="params[slider_title_position]" <?php if($param_values['slider_title_position'] == '2'){ echo 'checked="checked"'; } ?> /></td>
						<td><input type="radio" value="3" id="slideshow_title_top-right" name="params[slider_title_position]"  <?php if($param_values['slider_title_position'] == '3'){ echo 'checked="checked"'; } ?> /></td>
					</tr>
					<tr>
						<td><input type="radio" value="4" id="slideshow_title_middle-left" name="params[slider_title_position]" <?php if($param_values['slider_title_position'] == '4'){ echo 'checked="checked"'; } ?> /></td>
						<td><input type="radio" value="5" id="slideshow_title_middle-center" name="params[slider_title_position]" <?php if($param_values['slider_title_position'] == '5'){ echo 'checked="checked"'; } ?> /></td>
						<td><input type="radio" value="6" id="slideshow_title_middle-right" name="params[slider_title_position]" <?php if($param_values['slider_title_position'] == '6'){ echo 'checked="checked"'; } ?> /></td>
					</tr>
					<tr>
						<td><input type="radio" value="7" id="slideshow_title_bottom-left" name="params[slider_title_position]" <?php if($param_values['slider_title_position'] == '7'){ echo 'checked="checked"'; } ?> /></td>
						<td><input type="radio" value="8" id="slideshow_title_bottom-center" name="params[slider_title_position]" <?php if($param_values['slider_title_position'] == '8'){ echo 'checked="checked"'; } ?> /></td>
						<td><input type="radio" value="9" id="slideshow_title_bottom-right" name="params[slider_title_position]" <?php if($param_values['slider_title_position'] == '9'){ echo 'checked="checked"'; } ?> /></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</form>

<input type="hidden" name="option" value=""/>
<input type="hidden" name="task" value=""/>
<input type="hidden" name="controller" value="options"/>
<input type="hidden" name="op_type" value="styles"/>
<input type="hidden" name="boxchecked" value="0"/>