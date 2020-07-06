<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
class Portfolio_Gallery_Install
{

    /**
     * Install Portfolio Gallery.
     */
    public static function install()
    {
        if (!defined('PORTFOLIO_GALLERY_INSTALLING')) {
            define('PORTFOLIO_GALLERY_INSTALLING', true);
        }
        self::create_tables();
        // Flush rules after install
        flush_rewrite_rules();
        // Trigger action
        do_action('portfolio_gallery_installed');
    }

    private static function create_tables()
    {
        global $wpdb;

/// creat database tables


        $sql_huge_itportfolio_params = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "huge_itportfolio_params`(
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `title` varchar(200) CHARACTER SET utf8 NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `value` varchar(200) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
)   DEFAULT CHARSET=latin1 AUTO_INCREMENT=200 ";


        $sql_huge_itportfolio_images = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "huge_itportfolio_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `portfolio_id` varchar(200) DEFAULT NULL,
  `description` text,
  `image_url` text,
  `sl_url` text DEFAULT NULL,
  `sl_type` text NOT NULL,
  `link_target` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(4) unsigned DEFAULT NULL,
  `published_in_sl_width` tinyint(4) unsigned DEFAULT NULL,
  `category` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
)  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5";

        $sql_huge_itportfolio_portfolios = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "huge_itportfolio_portfolios` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `sl_height` int(11) unsigned DEFAULT NULL,
  `sl_width` int(11) unsigned DEFAULT NULL,
  `pause_on_hover` text,
  `portfolio_list_effects_s` text,
  `description` text,
  `param` text,
  `sl_position` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` text,
  `categories` text NOT NULL,
  `ht_show_sorting` text NOT NULL,
  `ht_show_filtering` text NOT NULL,
  `autoslide` varchar(5) NOT NULL DEFAULT 'on',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
)   DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ";


        $table_name = $wpdb->prefix . "huge_itportfolio_params";
        $sql_1 = <<<query1
INSERT INTO `$table_name` (`name`, `title`,`description`, `value`) VALUES

/*############################## VIEW 0 #####################################*/

('ht_view0_togglebutton_style', 'Toggle Button Style', 'Toggle Button Style','dark'),
('ht_view0_show_separator_lines', 'Show Separator Lines', 'Show Separator Lines','on'),
('ht_view0_linkbutton_text', 'Link Button Text', 'Link Button Text', 'View More'),
('ht_view0_show_linkbutton', 'Show Link Button', 'Show Link Button', 'on'),
('ht_view0_linkbutton_background_hover_color', 'Link Button Background Hover Color', 'Link Button Background Hover Color', 'df2e1b'),
('ht_view0_linkbutton_background_color', 'Link Button Background Color', 'Link Button Background Color', 'e74c3c'),
('ht_view0_linkbutton_font_hover_color', 'Link Button Font Hover Color', 'Link Button Font Hover Color', 'ffffff'),
('ht_view0_linkbutton_color', 'Link Button Font Color', 'Link Button Font Color', 'ffffff'),
('ht_view0_linkbutton_font_size', 'Link Button Font Size', 'Link Button Font Size', '14'),
('ht_view0_description_color', 'Description Font Color', 'Description Font Color', '5b5b5b'),
('ht_view0_description_font_size', 'Description Font Size', 'Description Font Size', '14'),
('ht_view0_show_description', 'Show Description', 'Show Description', 'on'),
('ht_view0_thumbs_width', 'Thumbnails Width', 'Thumbnails Width', '75'),
('ht_view0_thumbs_position', 'Thumbnails Position', 'Thumbnails Position', 'before'),
('ht_view0_show_thumbs', 'Show Thumbnails', 'Show Thumbnails', 'on'),
('ht_view0_title_font_size', 'Title Font Size', 'Title Font Size', '15'),
('ht_view0_title_font_color', 'Title Font Color', 'Title Font Color', '555555'),
('ht_view0_element_border_width', 'Element Border Width', 'Element Border Width', '1'),
('ht_view0_element_border_color', 'Element Border Color', 'Element Border Color', 'D0D0D0'),
('ht_view0_element_background_color', 'Element Background Color', 'Element Background Color', 'f7f7f7'),
('ht_view0_block_width', 'Block Width', 'Block Width', '275'),
('ht_view0_block_height', 'Block Height', 'Block Height', '160'),


/*############################## VIEW 1 #####################################*/

('ht_view1_show_separator_lines', 'Show Separator Lines', 'Show Separator Lines','on'),
('ht_view1_linkbutton_text', 'Link Button Text', 'Link Button Text', 'View More'),
('ht_view1_show_linkbutton', 'Show Link Button', 'Show Link Button', 'on'),
('ht_view1_linkbutton_background_hover_color', 'Link Button Background Hover Color', 'Link Button Background Hover Color', 'df2e1b'),
('ht_view1_linkbutton_background_color', 'Link Button Background Color', 'Link Button Background Color', 'e74c3c'),
('ht_view1_linkbutton_font_hover_color', 'Link Button Font Hover Color', 'Link Button Font Hover Color', 'ffffff'),
('ht_view1_linkbutton_color', 'Link Button Font Color', 'Link Button Font Color', 'ffffff'),
('ht_view1_linkbutton_font_size', 'Link Button Font Size', 'Link Button Font Size', '14'),
('ht_view1_description_color', 'Description Font Color', 'Description Font Color', '5b5b5b'),
('ht_view1_description_font_size', 'Description Font Size', 'Description Font Size', '14'),
('ht_view1_show_description', 'Show Description', 'Show Description', 'on'),
('ht_view1_thumbs_width', 'Thumbnails Width', 'Thumbnails Width', '75'),
('ht_view1_thumbs_position', 'Thumbnails Position', 'Thumbnails Position', 'before'),
('ht_view1_show_thumbs', 'Show Thumbnails', 'Show Thumbnails', 'on'),
('ht_view1_title_font_size', 'Title Font Size', 'Title Font Size', '15'),
('ht_view1_title_font_color', 'Title Font Color', 'Title Font Color', '555555'),
('ht_view1_element_border_width', 'Element Border Width', 'Element Border Width', '1'),
('ht_view1_element_border_color', 'Element Border Color', 'Element Border Color', 'D0D0D0'),
('ht_view1_element_background_color', 'Element Background Color', 'Element Background Color', 'f7f7f7'),
('ht_view1_block_width', 'Block Width', 'Block Width', '275'),



/*############################## VIEW 2 Popup #####################################*/

('ht_view2_element_linkbutton_text', 'Link Button Text', 'Link Button Text', 'View More'),
('ht_view2_element_show_linkbutton', 'Show Link Button On Element', 'Show Link Button On Element', 'on'),
('ht_view2_element_linkbutton_color', 'Element Link Button Font Color', 'Element Link Button Font Color', 'ffffff'),
('ht_view2_element_linkbutton_font_size', 'Element Link Button Font Size', 'Element Link Button Font Size', '14'),
('ht_view2_element_linkbutton_background_color', 'Element Link Button Background Color', 'Element Link Button Background Color', '2ea2cd'),
('ht_view2_show_popup_linkbutton', 'Show Link Button On Popup', 'Show Link Button On Popup', 'on'),
('ht_view2_popup_linkbutton_text', 'Popup Link Button Text', 'Link Button Text', 'View More'),
('ht_view2_popup_linkbutton_background_hover_color', 'Link Button Background Hover Color', 'Link Button Background Hover Color', '0074a2'),
('ht_view2_popup_linkbutton_background_color', 'Link Button Background Color', 'Link Button Background Color', '2ea2cd'),
('ht_view2_popup_linkbutton_font_hover_color', 'Link Button Font Hover Color', 'Link Button Font Hover Color', 'ffffff'),
('ht_view2_popup_linkbutton_color', 'Element Link Button Font Color', 'Link Button Font Color', 'ffffff'),
('ht_view2_popup_linkbutton_font_size', 'Element Link Button Font Size', 'Link Button Font Size', '14'),
('ht_view2_description_color', 'Description Font Color', 'Description Font Color', '222222'),
('ht_view2_description_font_size', 'Description Font Size', 'Description Font Size', '14'),
('ht_view2_show_description', 'Show Description', 'Show Description', 'on'),
('ht_view2_thumbs_width', 'Thumbnails Width', 'Thumbnails Width', '75'),
('ht_view2_thumbs_height', 'Thumbnails Height', 'Thumbnails Height', '75'),
('ht_view2_thumbs_position', 'Thumbnails Position', 'Thumbnails Position', 'before'),
('ht_view2_show_thumbs', 'Show Thumbnails', 'Show Thumbnails', 'on'),
('ht_view2_popup_background_color', 'Popup Background Color', 'Popup Background Color', 'FFFFFF'),
('ht_view2_popup_overlay_color', 'Popup Overlay Color', 'Popup Overlay Color', '000000'),
('ht_view2_popup_overlay_transparency_color', 'Popup Overlay Transparency', 'Popup Overlay Transparency ', '70'),
('ht_view2_popup_closebutton_style', 'Popup Close Button Style', 'Popup Close Button Style', 'dark'),
('ht_view2_show_separator_lines', 'Show Separator Lines', 'Show Separator Lines','on'),
('ht_view2_show_popup_title', 'Show Popup Title', 'Show Popup Title','on'),
('ht_view2_element_title_font_size', 'Element Title Font Size', 'Element Title Font Size', '18'),
('ht_view2_element_title_font_color', 'Element Title Font Color', 'Element Title Font Color', '222222'),
('ht_view2_popup_title_font_size', 'Popup Title Font Size', 'Popup Title Font Size', '18'),
('ht_view2_popup_title_font_color', 'Popup Title Font Color', 'Popup Title Font Color', '222222'),
('ht_view2_element_overlay_color', 'Element Overlay Color', 'Element Overlay Color', 'FFFFFF'),
('ht_view2_element_overlay_transparency', 'Element Overlay Transparency', 'Element Overlay Transparency ', '70'),
('ht_view2_zoombutton_style', 'Zoom Button Style', 'Zoom Button Style','light'),
('ht_view2_element_border_width', 'Element Border Width', 'Element Border Width', '1'),
('ht_view2_element_border_color', 'Element Border Color', 'Element Border Color', 'dedede'),
('ht_view2_element_background_color', 'Element Background Color', 'Element Background Color', 'f9f9f9'),
('ht_view2_element_width', 'Block Width', 'Block Width', '275'),
('ht_view2_element_height', 'Block Height', 'Block Height', '160'),


/*############################## VIEW 3 Fullwidth #####################################*/

('ht_view3_show_separator_lines', 'Show Separator Lines', 'Show Separator Lines','on'),
('ht_view3_linkbutton_text', 'Link Button Text', 'Link Button Text', 'View More'),
('ht_view3_show_linkbutton', 'Show Link Button', 'Show Link Button', 'on'),
('ht_view3_linkbutton_background_hover_color', 'Link Button Background Hover Color', 'Link Button Background Hover Color', '0074a2'),
('ht_view3_linkbutton_background_color', 'Link Button Background Color', 'Link Button Background Color', '2ea2cd'),
('ht_view3_linkbutton_font_hover_color', 'Link Button Font Hover Color', 'Link Button Font Hover Color', 'ffffff'),
('ht_view3_linkbutton_color', 'Link Button Font Color', 'Link Button Font Color', 'ffffff'),
('ht_view3_linkbutton_font_size', 'Link Button Font Size', 'Link Button Font Size', '14'),
('ht_view3_description_color', 'Description Font Color', 'Description Font Color', '555555'),
('ht_view3_description_font_size', 'Description Font Size', 'Description Font Size', '14'),
('ht_view3_show_description', 'Show Description', 'Show Description', 'on'),
('ht_view3_thumbs_width', 'Thumbnails Width', 'Thumbnails Width', '75'),
('ht_view3_thumbs_height', 'Thumbnails Height', 'Thumbnails Hight', '75'),
('ht_view3_show_thumbs', 'Show Thumbnails', 'Show Thumbnails', 'on'),
('ht_view3_title_font_size', 'Title Font Size', 'Title Font Size', '18'),
('ht_view3_title_font_color', 'Title Font Color', 'Title Font Color', '0074a2'),
('ht_view3_mainimage_width', 'Main Image Width', 'Main Image Width', '240'),
('ht_view3_element_border_width', 'Element Border Width', 'Element Border Width', '1'),
('ht_view3_element_border_color', 'Element Border Color', 'Element Border Color', 'dedede'),
('ht_view3_element_background_color', 'Element Background Color', 'Element Background Color', 'f9f9f9'),




/*############################## VIEW 4 FAQ #####################################*/

('ht_view4_togglebutton_style', 'Toggle Button Style', 'Toggle Button Style','dark'),
('ht_view4_show_separator_lines', 'Show Separator Lines', 'Show Separator Lines','on'),
('ht_view4_linkbutton_text', 'Link Button Text', 'Link Button Text', 'View More'),
('ht_view4_show_linkbutton', 'Show Link Button', 'Show Link Button', 'on'),
('ht_view4_linkbutton_background_hover_color', 'Link Button Background Hover Color', 'Link Button Background Hover Color', 'df2e1b'),
('ht_view4_linkbutton_background_color', 'Link Button Background Color', 'Link Button Background Color', 'e74c3c'),
('ht_view4_linkbutton_font_hover_color', 'Link Button Font Hover Color', 'Link Button Font Hover Color', 'ffffff'),
('ht_view4_linkbutton_color', 'Link Button Font Color', 'Link Button Font Color', 'ffffff'),
('ht_view4_linkbutton_font_size', 'Link Button Font Size', 'Link Button Font Size', '14'),
('ht_view4_description_color', 'Description Font Color', 'Description Font Color', '555555'),
('ht_view4_description_font_size', 'Description Font Size', 'Description Font Size', '14'),
('ht_view4_show_description', 'Show Description', 'Show Description', 'on'),
('ht_view4_title_font_size', 'Title Font Size', 'Title Font Size', '18'),
('ht_view4_title_font_color', 'Title Font Color', 'Title Font Color', 'E74C3C'),
('ht_view4_element_border_width', 'Element Border Width', 'Element Border Width', '1'),
('ht_view4_element_border_color', 'Element Border Color', 'Element Border Color', 'dedede'),
('ht_view4_element_background_color', 'Element Background Color', 'Element Background Color', 'f9f9f9'),
('ht_view4_block_width', 'Block Width', 'Block Width', '275'),

/*############################## VIEW 5 SLIDER #####################################*/

('ht_view5_icons_style', 'Icons Style', 'Icons Style','dark'),
('ht_view5_show_separator_lines', 'Show Separator Lines', 'Show Separator Lines','on'),
('ht_view5_linkbutton_text', 'Link Button Text', 'Link Button Text', 'View More'),
('ht_view5_show_linkbutton', 'Show Link Button', 'Show Link Button', 'on'),
('ht_view5_linkbutton_background_hover_color', 'Link Button Background Hover Color', 'Link Button Background Hover Color', '0074a2'),
('ht_view5_linkbutton_background_color', 'Link Button Background Color', 'Link Button Background Color', '2ea2cd'),
('ht_view5_linkbutton_font_hover_color', 'Link Button Font Hover Color', 'Link Button Font Hover Color', 'ffffff'),
('ht_view5_linkbutton_color', 'Link Button Font Color', 'Link Button Font Color', 'ffffff'),
('ht_view5_linkbutton_font_size', 'Link Button Font Size', 'Link Button Font Size', '14'),
('ht_view5_description_color', 'Description Font Color', 'Description Font Color', '555555'),
('ht_view5_description_font_size', 'Description Font Size', 'Description Font Size', '14'),
('ht_view5_show_description', 'Show Description', 'Show Description', 'on'),
('ht_view5_thumbs_width', 'Thumbnails Width', 'Thumbnails Width', '75'),
('ht_view5_thumbs_height', 'Thumbnails Height', 'Thumbnails Hight', '75'),
('ht_view5_show_thumbs', 'Show Thumbnails', 'Show Thumbnails', 'on'),
('ht_view5_title_font_size', 'Title Font Size', 'Title Font Size', '16'),
('ht_view5_title_font_color', 'Title Font Color', 'Title Font Color', '0074a2'),
('ht_view5_main_image_width', 'Main Image Width', 'Main Image Width', '275'),
('ht_view5_slider_tabs_font_color', 'Slider Tabs Font Color', 'Slider Tabs Font Color', 'd9d99'),
('ht_view5_slider_tabs_background_color', 'Slider Tabs Background Color', 'Slider Tabs Background Color', '555555'),
('ht_view5_slider_background_color', 'Slider Background Color', 'Slider Background Color', 'f9f9f9'),

/*############################## VIEW 6 Lightbox-gallery #####################################*/

('ht_view6_title_font_size', 'Title Font Size', 'Title Font Size', '16'),
('ht_view6_title_font_color', 'Title Font Color', 'Title Font Color', '0074A2'),
('ht_view6_title_font_hover_color', 'Title Font Hover Color', 'Title Font Hover Color', '2EA2CD'),
('ht_view6_title_background_color', 'Title Background Color', 'Title Background Color', '000000'),
('ht_view6_title_background_transparency', 'Title Background Transparency', 'Title Background Transparency', '80'),
('ht_view6_border_radius', 'Image Border Radius', 'Image Border Radius', '3'),
('ht_view6_border_width', 'Image Border Width', 'Image Border Width', '0'),
('ht_view6_border_color', 'Image Border Color', 'Image Border Color', 'eeeeee'),
('ht_view6_width', 'Image Width', 'Image Width', '275');


query1;

        $table_name = $wpdb->prefix . "huge_itportfolio_images";
        $sql_2 = "
INSERT INTO 

`" . $table_name . "` (`id`, `name`, `portfolio_id`, `description`, `image_url`, `sl_url`, `sl_type`, `link_target`, `ordering`, `published`, `published_in_sl_width`) VALUES
(1, 'Cutthroat & Cavalier', '1', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', '" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/1.jpg" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/1.1.jpg" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/1.2.jpg" . ";', 'http://huge-it.com/fields/order-website-maintenance/', 'image', 'on', 0, 1, NULL),
(2, 'Nespresso', '1', '<h6>Lorem Ipsum </h6><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><ul><li>lorem ipsum</li><li>dolor sit amet</li><li>lorem ipsum</li><li>dolor sit amet</li></ul>', '" . "https://vimeo.com/76602135" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/9.1.jpg" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/9.2.jpg" . ";', 'http://huge-it.com/fields/order-website-maintenance/', 'video', 'on', 1, 1, NULL),
(3, 'Nexus', '1', '<h6>Lorem Ipsum </h6><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrudexercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><ul><li>lorem ipsum</li><li>dolor sit amet</li><li>lorem ipsum</li><li>dolor sit amet</li></ul>', '" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/3.jpg" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/3.1.jpg" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/3.2.jpg" . ":" . "https://www.youtube.com/watch?v=YMQdfGFK5XQ" . ";', 'http://huge-it.com/fields/order-website-maintenance/', 'image', 'on', 2, 1, NULL),
(4, 'De7igner', '1', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p><h7>Dolor sit amet</h7><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', '" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/4.jpg" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/4.1.jpg" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/4.2.jpg" . ";', 'http://huge-it.com/fields/order-website-maintenance/', 'image', 'on', 3, 1, NULL),
(5, 'Autumn / Winter Collection', '1', '<h6>Lorem Ipsum</h6><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', '" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/2.jpg" . ";', 'http://huge-it.com/fields/order-website-maintenance/', 'image', 'on', 4, 1, NULL),
(6, 'Retro Headphones', '1', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', '" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/6.jpg" . ";" . "https://vimeo.com/80514062" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/6.1.jpg" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/6.2.jpg" . ";', 'http://huge-it.com/fields/order-website-maintenance/', 'image', 'on', 5, 1, NULL),
(7, 'Take Fight', '1', '<h6>Lorem Ipsum</h6><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p><p>Excepteur sint occaecat cupidatat non proident , sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', '" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/7.jpg" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/7.2.jpg" . ";" . "https://www.youtube.com/watch?v=SP3Dgr9S4pM" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/7.3.jpg" . ";', 'http://huge-it.com/fields/order-website-maintenance/', 'image', 'on', 6, 1, NULL),
(8, 'The Optic', '1', '<h6>Lorem Ipsum </h6><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><ul><li>lorem ipsum</li><li>dolor sit amet</li><li>lorem ipsum</li><li>dolor sit amet</li></ul>', '" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/8.jpg" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/8.1.jpg" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/8.3.jpg" . ";', 'http://huge-it.com/fields/order-website-maintenance/', 'image', 'on', 7, 1, NULL),
(9, 'Cone Music', '1', '<ul><li>lorem ipsumdolor sit amet</li><li>lorem ipsum dolor sit amet</li></ul><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', '" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/5.jpg" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/5.1.jpg" . ";" . Portfolio_Gallery()->plugin_url() . "/assets/images/Front_images/projects/5.2.jpg" . ";', 'http://huge-it.com/fields/order-website-maintenance/', 'image', 'on', 8, 1, NULL)";


        $table_name = $wpdb->prefix . "huge_itportfolio_portfolios";


        $sql_3 = "

INSERT INTO `$table_name` (`id`, `name`, `sl_height`, `sl_width`, `pause_on_hover`, `portfolio_list_effects_s`, `description`, `param`, `sl_position`, `ordering`, `published`) VALUES
(1, 'My First Portfolio', 375, 600, 'on', '2', '4000', '1000', 'center', 1, '300')";


        $wpdb->query($sql_huge_itportfolio_params);
        $wpdb->query($sql_huge_itportfolio_images);
        $wpdb->query($sql_huge_itportfolio_portfolios);


        if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "huge_itportfolio_params")) {
            $wpdb->query($sql_1);
        }
        if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "huge_itportfolio_images")) {
            $wpdb->query($sql_2);
        }
        if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "huge_itportfolio_portfolios")) {
            $wpdb->query($sql_3);
        }

        ///////////////////////////update////////////////////////////////////
        $table_name = $wpdb->prefix . "huge_itportfolio_params";
        $sql_update_p1 = <<<query1
INSERT INTO `$table_name` (`name`, `title`,`description`, `value`) VALUES
('light_box_size', 'Light box size', 'Light box size', '17'),
('light_box_width', 'Light Box width', 'Light Box width', '500'),
('light_box_transition', 'Light Box Transition', 'Light Box Transition', 'elastic'),
('light_box_speed', 'Light box speed', 'Light box speed', '800'),
('light_box_href', 'Light box href', 'Light box href', 'False'),
('light_box_title', 'Light box Title', 'Light box Title', 'false'),
('light_box_scalephotos', 'Light box scalePhotos', 'Light box scalePhotos', 'true'),
('light_box_rel', 'Light Box rel', 'Light Box rel', 'false'),
('light_box_scrolling', 'Light box Scrollin', 'Light box Scrollin', 'false'),
('light_box_opacity', 'Light box Opacity', 'Light box Opacity', '20'),
('light_box_open', 'Light box Open', 'Light box Open', 'false'),
('light_box_overlayclose', 'Light box overlayClose', 'Light box overlayClose', 'true'),
('light_box_esckey', 'Light box escKey', 'Light box escKey', 'false'),
('light_box_arrowkey', 'Light box arrowKey', 'Light box arrowKey', 'false'),
('light_box_loop', 'Light box loop', 'Light box loop', 'true'),
('light_box_data', 'Light box data', 'Light box data', 'false'),
('light_box_classname', 'Light box className', 'Light box className', 'false'),
('light_box_fadeout', 'Light box fadeOut', 'Light box fadeOut', '300'),
('light_box_closebutton', 'Light box closeButton', 'Light box closeButton', 'false'),
('light_box_current', 'Light box current', 'Light box current', 'image'),
('light_box_previous', 'Light box previous', 'Light box previous', 'previous'),
('light_box_next', 'Light box next', 'Light box next', 'next'),
('light_box_close', 'Light box close', 'Light box close', 'close'),
('light_box_iframe', 'Light box iframe', 'Light box iframe', 'false'),
('light_box_inline', 'Light box inline', 'Light box inline', 'false'),
('light_box_html', 'Light box html', 'Light box html', 'false'),
('light_box_photo', 'Light box photo', 'Light box photo', 'false'),
('light_box_height', 'Light box height', 'Light box height', '500'),
('light_box_innerwidth', 'Light box innerWidth', 'Light box innerWidth', 'false'),
('light_box_innerheight', 'Light box innerHeight', 'Light box innerHeight', 'false'),
('light_box_initialwidth', 'Light box initialWidth', 'Light box initialWidth', '300'),
('light_box_initialheight', 'Light box initialHeight', 'Light box initialHeight', '100'),
('light_box_maxwidth', 'Light box maxWidth', 'Light box maxWidth', '768'),
('light_box_maxheight', 'Light box maxHeight', 'Light box maxHeight', '500'),
('light_box_slideshow', 'Light box slideshow', 'Light box slideshow', 'false'),
('light_box_slideshowspeed', 'Light box slideshowSpeed', 'Light box slideshowSpeed', '2500'),
('light_box_slideshowauto', 'Light box slideshowAuto', 'Light box slideshowAuto', 'true'),
('light_box_slideshowstart', 'Light box slideshowStart', 'Light box slideshowStart', 'start slideshow'),
('light_box_slideshowstop', 'Light box slideshowStop', 'Light box slideshowStop', 'stop slideshow'),
('light_box_fixed', 'Light box fixed', 'Light box fixed', 'true'),
('light_box_top', 'Light box top', 'Light box top', 'false'),
('light_box_bottom', 'Light box bottom', 'Light box bottom', 'false'),
('light_box_left', 'Light box left', 'Light box left', 'false'),
('light_box_right', 'Light box right', 'Light box right', 'false'),
('light_box_reposition', 'Light box reposition', 'Light box reposition', 'false'),
('light_box_retinaimage', 'Light box retinaImage', 'Light box retinaImage', 'true'),
('light_box_retinaurl', 'Light box retinaUrl', 'Light box retinaUrl', 'false'),
('light_box_retinasuffix', 'Light box retinaSuffix', 'Light box retinaSuffix', '@2x.$1'),
('light_box_returnfocus', 'Light box returnFocus', 'Light box returnFocus', 'true'),
('light_box_trapfocus', 'Light box trapFocus', 'Light box trapFocus', 'true'),
('light_box_fastiframe', 'Light box fastIframe', 'Light box fastIframe', 'true'),
('light_box_preloading', 'Light box preloading', 'Light box preloading', 'true'),
('slider_title_position', 'Slider title position', 'Slider title position', '5'),
('light_box_style', 'Light Box style', 'Light Box style', '1'),
('light_box_size_fix', 'Light Box size fix style', 'Light Box size fix style', 'false');

query1;


        global $wpdb;
        $query = "SELECT name FROM " . $wpdb->prefix . "huge_itportfolio_params";
        $update_p1 = $wpdb->get_results($query);
        if (end($update_p1)->name == 'ht_view6_width') {
            $wpdb->query($sql_update_p1);
        }

        $table_name = $wpdb->prefix . "huge_itportfolio_params";
        $sql_update_p2 = <<<query2
INSERT INTO `$table_name` (`name`, `title`,`description`, `value`) VALUES
('ht_view0_show_sorting', 'Show Sorting', 'Show Sorting', 'on'),
('ht_view0_sortbutton_font_size', 'Sort Button Font Size', 'Sort Button Font Size', '14'),                   
('ht_view0_sortbutton_font_color', 'Sort Button Font Color', 'Sort Button Font Color', '555555'),
('ht_view0_sortbutton_hover_font_color', 'Sort Button Hover Font Color', 'Sort Button Hover Font Color', 'ffffff'),
('ht_view0_sortbutton_background_color', 'Sort Button Background Color', 'Sort Button Background Color', 'F7F7F7'),
('ht_view0_sortbutton_hover_background_color', 'Sort Button Hover Background Color', 'Sort Button Hover Background Color', 'FF3845'),
('ht_view0_sortbutton_border_radius', 'Sort Button Border Radius', 'Sort Button Border Radius', '0'),
('ht_view0_sortbutton_border_padding', 'Sort Button Padding', 'Sort Button Padding', '3'),
('ht_view0_sorting_float', 'Sorting Position', 'Sorting Position', 'top'),
('ht_view0_show_filtering', 'Show Filtering', 'Show Filtering', 'on'),
('ht_view0_filterbutton_font_size', 'Filter Button Font Size', 'Filter Button Font Size', '14'),
('ht_view0_filterbutton_font_color', 'Filter Button Font Color', 'Filter Button Font Color', '555555'),
('ht_view0_filterbutton_background_color', 'Filter Button Background Color', 'Filter Button Background Color', 'F7F7F7'),
('ht_view0_filterbutton_hover_font_color', 'Filter Button Hover Font Color', 'Filter Button Hover Font Color', 'ffffff'),
('ht_view0_filterbutton_hover_background_color', 'Filter Button Hover Background Color', 'Filter Button Hover Background Color', 'FF3845'),
('ht_view0_filterbutton_border_radius', 'Filter Button Border Radius', 'Filter Button Border Radius', '0'),
('ht_view0_filterbutton_border_padding', 'Filter Button Padding', 'Filter Button Padding', '3'),
('ht_view0_filtering_float', 'Filtering Position', 'Filtering Position', 'left'),
('ht_view1_show_sorting', 'Show Sorting', 'Show Sorting', 'on'),
('ht_view1_sortbutton_font_size', 'Sort Button Font Size', 'Sort Button Font Size', '14'),                   
('ht_view1_sortbutton_font_color', 'Sort Button Font Color', 'Sort Button Font Color', '555555'),
('ht_view1_sortbutton_hover_font_color', 'Sort Button Hover Font Color', 'Sort Button Hover Font Color', 'ffffff'),
('ht_view1_sortbutton_background_color', 'Sort Button Background Color', 'Sort Button Background Color', 'F7F7F7'),
('ht_view1_sortbutton_hover_background_color', 'Sort Button Hover Background Color', 'Sort Button Hover Background Color', 'FF3845'),
('ht_view1_sortbutton_border_radius', 'Sort Button Border Radius', 'Sort Button Border Radius', '0'),
('ht_view1_sortbutton_border_padding', 'Sort Button Padding', 'Sort Button Padding', '3'),
('ht_view1_sorting_float', 'Sorting Position', 'Sorting Position', 'top'),
('ht_view1_show_filtering', 'Show Filtering', 'Show Filtering', 'on'),
('ht_view1_filterbutton_font_size', 'Filter Button Font Size', 'Filter Button Font Size', '14'),
('ht_view1_filterbutton_font_color', 'Filter Button Font Color', 'Filter Button Font Color', '555555'),
('ht_view1_filterbutton_background_color', 'Filter Button Background Color', 'Filter Button Background Color', 'F7F7F7'),
('ht_view1_filterbutton_hover_font_color', 'Filter Button Hover Font Color', 'Filter Button Hover Font Color', 'ffffff'),
('ht_view1_filterbutton_hover_background_color', 'Filter Button Hover Background Color', 'Filter Button Hover Background Color', 'FF3845'),
('ht_view1_filterbutton_border_radius', 'Filter Button Border Radius', 'Filter Button Border Radius', '0'),
('ht_view1_filterbutton_border_padding', 'Filter Button Padding', 'Filter Button Padding', '3'),
('ht_view1_filtering_float', 'Filtering Position', 'Filtering Position', 'left'),
('ht_view2_show_sorting', 'Show Sorting', 'Show Sorting', 'on'),
('ht_view2_sortbutton_font_size', 'Sort Button Font Size', 'Sort Button Font Size', '14'),                   
('ht_view2_sortbutton_font_color', 'Sort Button Font Color', 'Sort Button Font Color', '555555'),
('ht_view2_sortbutton_hover_font_color', 'Sort Button Hover Font Color', 'Sort Button Hover Font Color', 'ffffff'),
('ht_view2_sortbutton_background_color', 'Sort Button Background Color', 'Sort Button Background Color', 'F7F7F7'),
('ht_view2_sortbutton_hover_background_color', 'Sort Button Hover Background Color', 'Sort Button Hover Background Color', 'FF3845'),
('ht_view2_sortbutton_border_radius', 'Sort Button Border Radius', 'Sort Button Border Radius', '0'),
('ht_view2_sortbutton_border_padding', 'Sort Button Padding', 'Sort Button Padding', '3'),
('ht_view2_sorting_float', 'Sorting Position', 'Sorting Position', 'top'),
('ht_view2_show_filtering', 'Show Filtering', 'Show Filtering', 'on'),
('ht_view2_filterbutton_font_size', 'Filter Button Font Size', 'Filter Button Font Size', '14'),
('ht_view2_filterbutton_font_color', 'Filter Button Font Color', 'Filter Button Font Color', '555555'),
('ht_view2_filterbutton_background_color', 'Filter Button Background Color', 'Filter Button Background Color', 'F7F7F7'),
('ht_view2_filterbutton_hover_font_color', 'Filter Button Hover Font Color', 'Filter Button Hover Font Color', 'ffffff'),
('ht_view2_filterbutton_hover_background_color', 'Filter Button Hover Background Color', 'Filter Button Hover Background Color', 'FF3845'),
('ht_view2_filterbutton_border_radius', 'Filter Button Border Radius', 'Filter Button Border Radius', '0'),
('ht_view2_filterbutton_border_padding', 'Filter Button Padding', 'Filter Button Padding', '3'),
('ht_view2_filtering_float', 'Filtering Position', 'Filtering Position', 'left'),
('ht_view3_show_sorting', 'Show Sorting', 'Show Sorting', 'on'),
('ht_view3_sortbutton_font_size', 'Sort Button Font Size', 'Sort Button Font Size', '14'),                   
('ht_view3_sortbutton_font_color', 'Sort Button Font Color', 'Sort Button Font Color', '555555'),
('ht_view3_sortbutton_hover_font_color', 'Sort Button Hover Font Color', 'Sort Button Hover Font Color', 'ffffff'),
('ht_view3_sortbutton_background_color', 'Sort Button Background Color', 'Sort Button Background Color', 'F7F7F7'),
('ht_view3_sortbutton_hover_background_color', 'Sort Button Hover Background Color', 'Sort Button Hover Background Color', 'FF3845'),
('ht_view3_sortbutton_border_radius', 'Sort Button Border Radius', 'Sort Button Border Radius', '0'),
('ht_view3_sortbutton_border_padding', 'Sort Button Padding', 'Sort Button Padding', '3'),
('ht_view3_sorting_float', 'Sorting Position', 'Sorting Position', 'top'),
('ht_view3_show_filtering', 'Show Filtering', 'Show Filtering', 'on'),
('ht_view3_filterbutton_font_size', 'Filter Button Font Size', 'Filter Button Font Size', '14'),
('ht_view3_filterbutton_font_color', 'Filter Button Font Color', 'Filter Button Font Color', '555555'),
('ht_view3_filterbutton_background_color', 'Filter Button Background Color', 'Filter Button Background Color', 'F7F7F7'),
('ht_view3_filterbutton_hover_font_color', 'Filter Button Hover Font Color', 'Filter Button Hover Font Color', 'ffffff'),
('ht_view3_filterbutton_hover_background_color', 'Filter Button Hover Background Color', 'Filter Button Hover Background Color', 'FF3845'),
('ht_view3_filterbutton_border_radius', 'Filter Button Border Radius', 'Filter Button Border Radius', '0'),
('ht_view3_filterbutton_border_padding', 'Filter Button Padding', 'Filter Button Padding', '3'),
('ht_view3_filtering_float', 'Filtering Position', 'Filtering Position', 'left'),
('ht_view4_show_sorting', 'Show Sorting', 'Show Sorting', 'on'),
('ht_view4_sortbutton_font_size', 'Sort Button Font Size', 'Sort Button Font Size', '14'),                   
('ht_view4_sortbutton_font_color', 'Sort Button Font Color', 'Sort Button Font Color', '555555'),
('ht_view4_sortbutton_hover_font_color', 'Sort Button Hover Font Color', 'Sort Button Hover Font Color', 'ffffff'),
('ht_view4_sortbutton_background_color', 'Sort Button Background Color', 'Sort Button Background Color', 'F7F7F7'),
('ht_view4_sortbutton_hover_background_color', 'Sort Button Hover Background Color', 'Sort Button Hover Background Color', 'FF3845'),
('ht_view4_sortbutton_border_radius', 'Sort Button Border Radius', 'Sort Button Border Radius', '0'),
('ht_view4_sortbutton_border_padding', 'Sort Button Padding', 'Sort Button Padding', '3'),
('ht_view4_sorting_float', 'Sorting Position', 'Sorting Position', 'top'),
('ht_view4_show_filtering', 'Show Filtering', 'Show Filtering', 'on'),
('ht_view4_filterbutton_font_size', 'Filter Button Font Size', 'Filter Button Font Size', '14'),
('ht_view4_filterbutton_font_color', 'Filter Button Font Color', 'Filter Button Font Color', '555555'),
('ht_view4_filterbutton_background_color', 'Filter Button Background Color', 'Filter Button Background Color', 'F7F7F7'),
('ht_view4_filterbutton_hover_font_color', 'Filter Button Hover Font Color', 'Filter Button Hover Font Color', 'ffffff'),
('ht_view4_filterbutton_hover_background_color', 'Filter Button Hover Background Color', 'Filter Button Hover Background Color', 'FF3845'),
('ht_view4_filterbutton_border_radius', 'Filter Button Border Radius', 'Filter Button Border Radius', '0'),
('ht_view4_filterbutton_border_padding', 'Filter Button Padding', 'Filter Button Padding', '3'),
('ht_view4_filtering_float', 'Filtering Position', 'Filtering Position', 'left'),
('ht_view6_show_sorting', 'Show Sorting', 'Show Sorting', 'on'),
('ht_view6_sortbutton_font_size', 'Sort Button Font Size', 'Sort Button Font Size', '14'),                   
('ht_view6_sortbutton_font_color', 'Sort Button Font Color', 'Sort Button Font Color', '555555'),
('ht_view6_sortbutton_hover_font_color', 'Sort Button Hover Font Color', 'Sort Button Hover Font Color', 'ffffff'),
('ht_view6_sortbutton_background_color', 'Sort Button Background Color', 'Sort Button Background Color', 'F7F7F7'),
('ht_view6_sortbutton_hover_background_color', 'Sort Button Hover Background Color', 'Sort Button Hover Background Color', 'FF3845'),
('ht_view6_sortbutton_border_radius', 'Sort Button Border Radius', 'Sort Button Border Radius', '0'),
('ht_view6_sortbutton_border_padding', 'Sort Button Padding', 'Sort Button Padding', '3'),
('ht_view6_sorting_float', 'Sorting Position', 'Sorting Position', 'top'),
('ht_view6_show_filtering', 'Show Filtering', 'Show Filtering', 'on'),
('ht_view6_filterbutton_font_size', 'Filter Button Font Size', 'Filter Button Font Size', '14'),
('ht_view6_filterbutton_font_color', 'Filter Button Font Color', 'Filter Button Font Color', '555555'),
('ht_view6_filterbutton_background_color', 'Filter Button Background Color', 'Filter Button Background Color', 'F7F7F7'),
('ht_view6_filterbutton_hover_font_color', 'Filter Button Hover Font Color', 'Filter Button Hover Font Color', 'ffffff'),
('ht_view6_filterbutton_hover_background_color', 'Filter Button Hover Background Color', 'Filter Button Hover Background Color', 'FF3845'),
('ht_view6_filterbutton_border_radius', 'Filter Button Border Radius', 'Filter Button Border Radius', '0'),
('ht_view6_filterbutton_border_padding', 'Filter Button Padding', 'Filter Button Padding', '3'),
('ht_view6_filtering_float', 'Filtering Position', 'Filtering Position', 'left'),
('ht_view0_sorting_name_by_default', 'Default Sorting', 'Default Sorting', 'Default'),
('ht_view0_sorting_name_by_id', 'Sorting by ID', 'Default Sorting', 'Date'),
('ht_view0_sorting_name_by_name', 'Sorting by Name', 'Sorting by Name', 'Title'),
('ht_view0_sorting_name_by_random', 'Sorting by Random', 'Sorting by Random', 'Random'),
('ht_view0_sorting_name_by_asc', 'Ascending Sorting', 'Ascending Sorting', 'Ascending'),
('ht_view0_sorting_name_by_desc', 'Descending Sorting', 'Descending Sorting', 'Descending'),
('ht_view1_sorting_name_by_default', 'Default Sorting', 'Default Sorting', 'Default'),
('ht_view1_sorting_name_by_id', 'Sorting by ID', 'Default Sorting', 'Date'),
('ht_view1_sorting_name_by_name', 'Sorting by Name', 'Sorting by Name', 'Title'),
('ht_view1_sorting_name_by_random', 'Sorting by Random', 'Sorting by Random', 'Random'),
('ht_view1_sorting_name_by_asc', 'Ascending Sorting', 'Ascending Sorting', 'Ascending'),
('ht_view1_sorting_name_by_desc', 'Descending Sorting', 'Descending Sorting', 'Descending'),
('ht_view2_popup_full_width', 'Popup Image Full Width', 'Popup Image Full Width','on'),
('ht_view2_sorting_name_by_default', 'Default Sorting', 'Default Sorting', 'Default'),
('ht_view2_sorting_name_by_id', 'Sorting by ID', 'Default Sorting', 'Date'),
('ht_view2_sorting_name_by_name', 'Sorting by Name', 'Sorting by Name', 'Title'),
('ht_view2_sorting_name_by_random', 'Sorting by Random', 'Sorting by Random', 'Random'),
('ht_view2_sorting_name_by_asc', 'Ascending Sorting', 'Ascending Sorting', 'Ascending'),
('ht_view2_sorting_name_by_desc', 'Descending Sorting', 'Descending Sorting', 'Descending'),
('ht_view3_sorting_name_by_default', 'Default Sorting', 'Default Sorting', 'Default'),
('ht_view3_sorting_name_by_id', 'Sorting by ID', 'Default Sorting', 'Date'),
('ht_view3_sorting_name_by_name', 'Sorting by Name', 'Sorting by Name', 'Title'),
('ht_view3_sorting_name_by_random', 'Sorting by Random', 'Sorting by Random', 'Random'),
('ht_view3_sorting_name_by_asc', 'Ascending Sorting', 'Ascending Sorting', 'Ascending'),
('ht_view3_sorting_name_by_desc', 'Descending Sorting', 'Descending Sorting', 'Descending'),
('ht_view4_sorting_name_by_default', 'Default Sorting', 'Default Sorting', 'Default'),
('ht_view4_sorting_name_by_id', 'Sorting by ID', 'Default Sorting', 'Date'),
('ht_view4_sorting_name_by_name', 'Sorting by Name', 'Sorting by Name', 'Title'),
('ht_view4_sorting_name_by_random', 'Sorting by Random', 'Sorting by Random', 'Random'),
('ht_view4_sorting_name_by_asc', 'Ascending Sorting', 'Ascending Sorting', 'Ascending'),
('ht_view4_sorting_name_by_desc', 'Descending Sorting', 'Descending Sorting', 'Descending'),
('ht_view5_sorting_name_by_default', 'Default Sorting', 'Default Sorting', 'Default'),
('ht_view5_sorting_name_by_id', 'Sorting by ID', 'Default Sorting', 'Date'),
('ht_view5_sorting_name_by_name', 'Sorting by Name', 'Sorting by Name', 'Title'),
('ht_view5_sorting_name_by_random', 'Sorting by Random', 'Sorting by Random', 'Random'),
('ht_view5_sorting_name_by_asc', 'Ascending Sorting', 'Ascending Sorting', 'Ascending'),
('ht_view5_sorting_name_by_desc', 'Descending Sorting', 'Descending Sorting', 'Descending'),
('ht_view6_sorting_name_by_default', 'Default Sorting', 'Default Sorting', 'Default'),
('ht_view6_sorting_name_by_id', 'Sorting by date', 'date Sorting', 'Date'),
('ht_view6_sorting_name_by_name', 'Sorting by Name', 'Sorting by Name', 'Title'),
('ht_view6_sorting_name_by_random', 'Sorting by Random', 'Sorting by Random', 'Random'),
('ht_view6_sorting_name_by_asc', 'Ascending Sorting', 'Ascending Sorting', 'Ascending'),
('ht_view6_sorting_name_by_desc', 'Descending Sorting', 'Descending Sorting', 'Descending');

query2;


        global $wpdb;
        $query = "SELECT name FROM " . $wpdb->prefix . "huge_itportfolio_params";
        $update_p2 = $wpdb->get_results($query);
        if (end($update_p2)->name == 'light_box_size_fix') {
            $wpdb->query($sql_update_p2);
        }
        $table_name = $wpdb->prefix . "huge_itportfolio_params";
        $sql_update_p3 = "INSERT INTO `$table_name` (`name`, `title`,`description`, `value`) VALUES
('ht_view0_cat_all', 'Show All', 'Show All', 'all'),('ht_view1_cat_all', 'Show All', 'Show All', 'all'),('ht_view2_cat_all', 'Show All', 'Show All', 'all'),
('ht_view3_cat_all', 'Show All', 'Show All', 'all'),('ht_view4_cat_all', 'Show All', 'Show All', 'all'),('ht_view5_cat_all', 'Show All', 'Show All', 'all'),
('ht_view6_cat_all', 'Show All', 'Show All', 'all')";
        $query = "SELECT name FROM " . $wpdb->prefix . "huge_itportfolio_params";
        $update_p3 = $wpdb->get_results($query);
        if (end($update_p3)->name == 'ht_view6_sorting_name_by_desc') {
            $wpdb->query($sql_update_p3);
        };
        $imagesAllFieldsInArray = $wpdb->get_results("DESCRIBE " . $wpdb->prefix . "huge_itportfolio_images", ARRAY_A);
        $forUpdate = 0;
        foreach ($imagesAllFieldsInArray as $portfoliosField) {
            if ($portfoliosField['Field'] == 'category') {
                // "ka category field.<br>";
                $forUpdate = 1;
                $catValues = $wpdb->get_results("SELECT category FROM " . $wpdb->prefix . "huge_itportfolio_images");
                $needToUpdate = 0;
                foreach ($catValues as $catValue) {
                    if ($catValue->category !== '') {
                        $needToUpdate = 1;
                        //echo "category field - y datark chi.<br>";
                    }
                }
                if ($needToUpdate == 0) {
                    $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My First Category,My Third Category,' WHERE id='1'");
                    $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My Second Category,' WHERE id='2'");
                    $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My Third Category,' WHERE id='3'");
                    $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My First Category,My Second Category,' WHERE id='4'");
                    $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My Second Category,My Third Category,' WHERE id='5'");
                    $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My Third Category,' WHERE id='6'");
                    $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My Second Category,' WHERE id='7'");
                    $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My First Category,' WHERE id='8'");
                }

                break;
            }
        }
        if ($forUpdate == '0') {
            $wpdb->query("ALTER TABLE " . $wpdb->prefix . "huge_itportfolio_images ADD category text");
            $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My First Category,My Third Category,' WHERE id='1'");
            $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My Second Category,' WHERE id='2'");
            $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My Third Category,' WHERE id='3'");
            $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My First Category,My Second Category,' WHERE id='4'");
            $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My Second Category,My Third Category,' WHERE id='5'");
            $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My Third Category,' WHERE id='6'");
            $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My Second Category,' WHERE id='7'");
            $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET category = 'My First Category,' WHERE id='8'");
        }

        $productPortfolio = $wpdb->get_results("DESCRIBE " . $wpdb->prefix . "huge_itportfolio_portfolios", ARRAY_A);
        $isUpdate = 0;
        foreach ($productPortfolio as $prodPortfolio) {
            if ($prodPortfolio['Field'] == 'categories' && $prodPortfolio['Type'] == 'text') {
                $isUpdate = 1;

                $allCats = $wpdb->get_results("SELECT categories FROM " . $wpdb->prefix . "huge_itportfolio_portfolios");
                $needToUpdateAllCats = 0;
                foreach ($allCats as $AllCatsVal) {
                    if ($AllCatsVal->categories !== '') {
                        $needToUpdateAllCats = 1;
                    }
                }
                if ($needToUpdateAllCats == 0) {
                    $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET categories = 'My First Category,My Second Category,My Third Category,' ");
                    $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET ht_show_sorting = 'off' ");
                    $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET ht_show_filtering = 'off' ");
                }

                break;
            }
        }
        if ($isUpdate == '0') {
            $wpdb->query("ALTER TABLE " . $wpdb->prefix . "huge_itportfolio_portfolios ADD categories text");
            $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET categories = 'My First Category,My Second Category,My Third Category,'");

            $wpdb->query("ALTER TABLE " . $wpdb->prefix . "huge_itportfolio_portfolios ADD ht_show_sorting text");
            $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET ht_show_sorting = 'off'");

            $wpdb->query("ALTER TABLE " . $wpdb->prefix . "huge_itportfolio_portfolios ADD ht_show_filtering text");
            $wpdb->query("UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET ht_show_filtering = 'off'");
        }
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////
        $table_name = $wpdb->prefix . "huge_itportfolio_params";
        $sql_update_g4 = <<<query4
INSERT INTO `$table_name` (`name`, `title`,`description`, `value`) VALUES
('port_natural_size_thumbnail', 'Image Resize-Natural', 'Image Resize-Natural', 'resize'),
('port_natural_size_contentpopup', 'Image Resize-Natural', 'Image Resize-Natural', 'resize');
                
query4;


        $query4 = "SELECT name FROM " . $wpdb->prefix . "huge_itportfolio_params";
        $update_p4 = $wpdb->get_results($query4);
        if (end($update_p4)->name == 'ht_view6_cat_all') {
            $wpdb->query($sql_update_g4);
        }


        $table_name = $wpdb->prefix . "huge_itportfolio_params";
        $sql_update_g5 = <<<query5
INSERT INTO `$table_name` (`name`, `title`,`description`, `value`) VALUES
('ht_view0_elements_in_center', 'Show All Elements In Center', 'Show All Elements In Center', 'on');
            
query5;

        $query5 = "SELECT name FROM " . $wpdb->prefix . "huge_itportfolio_params";
        $update_p5 = $wpdb->get_results($query5);
        if (end($update_p5)->name == 'port_natural_size_contentpopup') {
            $wpdb->query($sql_update_g5);
        }

        ////////////////////////////////////////

        $table_name = $wpdb->prefix . "huge_itportfolio_params";
        $sql_update_g7 = <<<query7
INSERT INTO `$table_name` (`name`, `title`,`description`, `value`) VALUES
('ht_view0_filterbutton_width', 'Filter Button Width', 'Filter Button Width', '180'),
('ht_view1_filterbutton_width', 'Filter Button Width', 'Filter Button Width', '180'),
('ht_view2_filterbutton_width', 'Filter Button Width', 'Filter Button Width', '180'),
('ht_view3_filterbutton_width', 'Filter Button Width', 'Filter Button Width', '180'),
('ht_view4_filterbutton_width', 'Filter Button Width', 'Filter Button Width', '180'),
('ht_view6_filterbutton_width', 'Filter Button Width', 'Filter Button Width', '180');
            
query7;

        $query7 = "SELECT name FROM " . $wpdb->prefix . "huge_itportfolio_params";
        $update_p7 = $wpdb->get_results($query7);
        if (end($update_p7)->name == 'ht_view0_elements_in_center') {
            $wpdb->query($sql_update_g7);
        }

        $portfoliosAllFieldsInArray1 = $wpdb->get_results("DESCRIBE " . $wpdb->prefix . "huge_itportfolio_portfolios", ARRAY_A);
        $fornewUpdate1 = 0;
        foreach ($portfoliosAllFieldsInArray1 as $portfoliosField) {
            if ($portfoliosField['Field'] == 'show_loading') {
                $fornewUpdate1 = 1;
            }
        }
        if ($fornewUpdate1 != 1) {
            $wpdb->query("ALTER TABLE " . $wpdb->prefix . "huge_itportfolio_portfolios  ADD `show_loading` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'on'");
            $wpdb->query("ALTER TABLE " . $wpdb->prefix . "huge_itportfolio_portfolios  ADD `loading_icon_type` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '1'");
        }

        $query = "SELECT name FROM " . $wpdb->prefix . "huge_itportfolio_params";
        $param_names = $wpdb->get_col($query);
        if (!in_array('port_natural_size_toggle', $param_names)) {
            $wpdb->query("INSERT INTO `" . $wpdb->prefix . "huge_itportfolio_params` (`name`, `value`) VALUES
('port_natural_size_toggle', 'crop')");
        }

        $portfoliosAllFieldsInArray2 = $wpdb->get_results("DESCRIBE " . $wpdb->prefix . "huge_itportfolio_portfolios", ARRAY_A);
        $fornewUpdate2 = 0;
        foreach ($portfoliosAllFieldsInArray2 as $portfoliosField2) {
            if ($portfoliosField2['Field'] == 'autoslide') {
                $fornewUpdate2 = 1;
            }
        }
        if ($fornewUpdate2 != 1) {
            $wpdb->query("ALTER TABLE " . $wpdb->prefix . "huge_itportfolio_portfolios  ADD `autoslide` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'on'");
        }
    }
}