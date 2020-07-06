jQuery(document).ready(function () {
	jQuery('#arrows-type input[name="params[portfolio_navigation_type]"]').change(function(){
		jQuery(this).parents('ul').find('li.active').removeClass('active');
		jQuery(this).parents('li').addClass('active');
	});
	jQuery('#portfolio-view-tabs > li > a').click(function(){
		jQuery('#portfolio-view-tabs > li').removeClass('active');
		jQuery(this).parent().addClass('active');
		jQuery('#portfolio-view-tabs-contents > li').removeClass('active');
		var liID=jQuery(this).attr('href');
		jQuery(liID).addClass('active');
		jQuery('#adminForm').attr('action',"admin.php?page=Options_portfolio_styles&task=save"+liID);
		return false;
	});
	
	
	
	imageslistlisize();
	jQuery(window).resize(function(){
		imageslistlisize();
	});
	
	function imageslistlisize(){
		var lisaze = jQuery('#images-list').width();
		lisaze=lisaze*0.06;
		jQuery('#images-list .widget-images-list li').not('.add-image-box').not('.first').height(lisaze);
	}
        
        jQuery('#portfolio-loading-icon li').click(function(){ //alert(jQuery(this).find("input:checked").val());
		jQuery(this).parents('ul').find('li.act').removeClass('act');
		jQuery(this).addClass('act');
	});	
        
        jQuery('input#show_loading').change(function(){
            if(jQuery(this).prop('checked') == false){
                jQuery('li.loading_opton').hide();
            }
            else{
                jQuery('li.loading_opton').show();
            }
        });
        jQuery('input#show_loading').change();

	jQuery('table.wp-list-table a[href*="remove_cat"]').click(function(){
		if(!confirm("Are you sure you want to delete this item?"))
			return false;
	});
});
	

	