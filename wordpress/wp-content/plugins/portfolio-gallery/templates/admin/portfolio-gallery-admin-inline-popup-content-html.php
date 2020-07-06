<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<style>
    #portfolio-unique-options-list label {
        width: 152px;
        display: inline-block;
    }

    #hugeitportfolioinsert {
        margin-left: 10px;
    }

    #portfolio-unique-options-list input[type='checkbox'] {
        margin-left: 1px;
    }

    #TB_window {
        background: #f1f1f1;
    }
</style>
<!------------------------------------------------------------------------------->

<!--------------------------------Option's Script-------------------------------->
<script type="text/javascript">
    jQuery(document).ready(function () {
        var ht_show_sorting;
        var ht_show_filtering;
        var auto_slide_on;

        jQuery('#ht_show_sorting').change(function () {
            if (jQuery('#ht_show_sorting').prop('checked') == false) {
                jQuery('#ht_show_sorting').val('off');
            }
            else if (jQuery('#ht_show_sorting').prop('checked') == true) {
                jQuery('#ht_show_sorting').val('on');
            }


        });
        //ht_show_sorting = jQuery('#ht_show_sorting').val();
        jQuery('#ht_show_filtering').change(function () {
            if (jQuery('#ht_show_filtering').prop('checked') == false) {
                jQuery('#ht_show_filtering').val('off');
            }
            else if (jQuery('#ht_show_filtering').prop('checked') == true) {
                jQuery('#ht_show_filtering').val('on');
            }
            //ht_show_filtering = jQuery('#ht_show_filtering').val();

        });

        jQuery('#auto_slide_on').change(function () {
            alert(5);
            if (jQuery('#auto_slide_on').prop('checked') == false) {
                jQuery('#auto_slide_on').val('off');
            }
            else if (jQuery('#auto_slide_on').prop('checked') == true) {
                jQuery('#auto_slide_on').val('on');
            }
            //auto_slide_on = jQuery('#auto_slide_on').val();

        });

        jQuery('#hugeitportfolioinsert').on('click', function () {
            console.log(1);

            ht_show_sorting = jQuery('#ht_show_sorting').val();
            ht_show_filtering = jQuery('#ht_show_filtering').val();
            auto_slide_on = jQuery('#auto_slide_on').val();
            var id = jQuery('#huge_it_portfolio-select option:selected').val();
            var portfolio_effects_list = jQuery('#portfolio_effects_list').val();
            var sl_pausetime = jQuery('#sl_pausetime').val();
            var sl_changespeed = jQuery('#sl_changespeed').val();
            var err = 0;
            var data = {
                action: 'portfolio_gallery_action',
                post: 'portfolioSaveOptions',
                htportfolio_id: id,
                portfolio_effects_list: portfolio_effects_list,
                ht_show_sorting: ht_show_sorting,
                ht_show_filtering: ht_show_filtering,
                sl_pausetime: sl_pausetime,
                sl_changespeed: sl_changespeed,
                pause_on_hover: auto_slide_on
            };

            if (!jQuery.isNumeric(sl_pausetime) || sl_pausetime < 0) {
                err = err + 1;
            } else {
                sl_pausetime = Math.round(sl_pausetime);
            }

            if (!jQuery.isNumeric(sl_changespeed) || sl_changespeed < 0) {
                err = err + 1;
            } else {
                sl_changespeed = Math.round(sl_changespeed);
            }

            if (err > 0) {
                alert('Fill the fields correctly.');
                return false;
            }


            jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data, function (response) {

            });
            window.send_to_editor('[huge_it_portfolio id="' + id + '"]');
            tb_remove();

        });
        jQuery('#portfolio_effects_list').on('change', function () {
            var sel = jQuery(this).val();

            if (sel == 5) {
                jQuery('.for-content-slider').css('display', 'block')
            }
            else {
                jQuery('.for-content-slider').css('display', 'none')
            }
        });
        jQuery('#portfolio_effects_list').change();

        //////////////////portfolio change options/////////////////////
        jQuery('#huge_it_portfolio-select').change(function () {

            var sel = jQuery(this).val();
            var data = {
                action: 'portfolio_gallery_action',
                post: 'portfolioChangeOptions',
                id: sel
            };
            console.log(data);
            jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data, function (response) {
                response = JSON.parse(response);
                console.log(response);
                var list_effect = response.portfolio_list_effects_s;
                jQuery('#portfolio_effects_list').val(response.portfolio_effects_list);
                jQuery('#portfolio_effects_list option[value=list_effect]').attr('selected');
                jQuery('#ht_show_sorting').val(response.ht_show_sorting);
                if (jQuery('#ht_show_sorting').val() == 'on') {
                    jQuery('#ht_show_sorting').attr('checked', 'checked');
                }
                else jQuery('#ht_show_sorting').removeAttr('checked');
                jQuery('#ht_show_filtering').val(response.ht_show_filtering);
                if (jQuery('#ht_show_filtering').val() == 'on') {
                    jQuery('#ht_show_filtering').attr('checked', 'checked');
                }
                else jQuery('#ht_show_filtering').removeAttr('checked');
                jQuery('#sl_pausetime').val(response.sl_pausetime);
                jQuery('#sl_changespeed').val(response.sl_changespeed);
                jQuery('#auto_slide_on').val(response.pause_on_hover);
                if (jQuery('#auto_slide_on').val() == 'on') {
                    jQuery('#auto_slide_on').attr('checked', 'checked');
                }
                else jQuery('#auto_slide_on').removeAttr('checked');
                if (response) {
                    sel1 = jQuery('#portfolio_effects_list').val();
                    if (sel1 == 5) {
                        jQuery('.for-content-slider').css('display', 'block')
                    }
                    else {
                        jQuery('.for-content-slider').css('display', 'none')
                    }
                    ;
                }

            });


        });
    });
</script>

<div id="huge_it_portfolio" style="display:none;"  class="post-content">
    <h3>Select Huge IT Portfolio Gallery to insert into post</h3>
    <?php
    global $wpdb;
    $query = "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios";
    $firstrow = $wpdb->get_row($query);
    $container_id = 'huge_it_portfolio';
    if (isset($_POST["hugeit_portfolio_id"])) {
        $id = $_POST["hugeit_portfolio_id"];
    } else {
        $id = $firstrow->id;
    }
    $query = "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios order by id ASC";
    $shortcodeportfolios = $wpdb->get_results($query);
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE id= %d", $id);
    $row = $wpdb->get_row($query);
    ?>

    <?php
    if (count($shortcodeportfolios)) {
        ?>

        <?php
        echo "<select id='huge_it_portfolio-select'  name='hugeit_portfolio_id'>";
        foreach ($shortcodeportfolios as $shortcodeportfolio) {
            echo "<option value='" . $shortcodeportfolio->id . "'>" . $shortcodeportfolio->name . "</option>";
        }
        echo "</select>"; ?>
        <?php echo "<button class='button primary' id='hugeitportfolioinsert'>Insert portfolio gallery</button>";
    } else {
        echo "No slideshows found", "huge_it_portfolio";
    }
    ?>
    <!--------------------------------Option's HTML-------------------------------->
    <h3>Current Portfolio Options</h3>
    <ul id="portfolio-unique-options-list">
        <li style="display:none;">
            <label for="sl_width"><?php echo __('The requested action is not valid.', 'portfolio-gallery'); ?></label>
            <input type="text" name="sl_width" id="sl_width" value="1111" class="text_area"/>
        </li>
        <li style="display:none;">
            <label for="sl_height"><?php echo __('Height', 'portfolio-gallery'); ?></label>
            <input type="text" name="sl_height" id="sl_height" value="<?php echo $row->sl_height; ?>"
                   class="text_area"/>
        </li>
        <li>
            <label for="portfolio_effects_list"><?php echo __('Select The View', 'portfolio-gallery'); ?></label>
            <select name="portfolio_effects_list" id="portfolio_effects_list">
                <option <?php if ($row->portfolio_list_effects_s == '0') {
                    echo 'selected';
                } ?> value="0"><?php echo __('Blocks Toggle Up/Down', 'portfolio-gallery'); ?></option>
                <option <?php if ($row->portfolio_list_effects_s == '1') {
                    echo 'selected';
                } ?> value="1"><?php echo __('Full-Height Blocks', 'portfolio-gallery'); ?></option>
                <option <?php if ($row->portfolio_list_effects_s == '2') {
                    echo 'selected';
                } ?> value="2"><?php echo __('Gallery/Content-Popup', 'portfolio-gallery'); ?></option>
                <option <?php if ($row->portfolio_list_effects_s == '3') {
                    echo 'selected';
                } ?> value="3"><?php echo __('Full-Width Blocks', 'portfolio-gallery'); ?></option>
                <option <?php if ($row->portfolio_list_effects_s == '4') {
                    echo 'selected';
                } ?> value="4"><?php echo __('FAQ Toggle Up/Down', 'portfolio-gallery'); ?></option>
                <option <?php if ($row->portfolio_list_effects_s == '5') {
                    echo 'selected';
                } ?> value="5"><?php echo __('Content Slider', 'portfolio-gallery'); ?></option>
                <option <?php if ($row->portfolio_list_effects_s == '6') {
                    echo 'selected';
                } ?> value="6"><?php echo __('Lightbox-Gallery', 'portfolio-gallery'); ?></option>
            </select>
        </li>
        <li class="allowIsotope">
            <label for="ht_show_sorting"><?php echo __('Show Sorting Buttons', 'portfolio-gallery'); ?></label>
            <input type="checkbox" id="ht_show_sorting" <?php if ($row->ht_show_sorting == 'on') {
                echo 'checked="checked"';
            } ?> name="ht_show_sorting" value="<?php echo $row->ht_show_sorting; ?>"/>
        </li>
        <li class="allowIsotope">
            <label for="ht_show_filtering"><?php echo __('Show Category Buttons', 'portfolio-gallery'); ?></label>
            <input type="checkbox" id="ht_show_filtering" <?php if ($row->ht_show_filtering == 'on') {
                echo 'checked="checked"';
            } ?> name="ht_show_filtering" value="<?php $row->ht_show_filtering; ?>"/>
        </li>
        <li style="display:none;" class="for-content-slider">
            <label for="sl_pausetime"><?php echo __('Pause time', 'portfolio-gallery'); ?></label>
            <input type="text" name="sl_pausetime" id="sl_pausetime" value="<?php echo $row->description; ?>"
                   class="text_area"/>
        </li>
        <li style="display:none;" class="for-content-slider">
            <label for="sl_changespeed"><?php echo __('Change speed', 'portfolio-gallery'); ?></label>
            <input type="text" name="sl_changespeed" id="sl_changespeed" value="<?php echo $row->param; ?>"
                   class="text_area"/>
        <li style="display:none;margin-top:10px" class="for-content-slider">
            <label for="auto_slide_on"><?php echo __('Autoslide ', 'portfolio-gallery'); ?></label>
            <input type="checkbox" name="pause_on_hover" value="<?php echo $row->pause_on_hover; ?>"
                   id="auto_slide_on" <?php if ($row->pause_on_hover == 'on') {
                echo 'checked="checked"';
            } ?> />
        </li>
    </ul>
    <!--------------------------------------------------------------------------------->


</div>