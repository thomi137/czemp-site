<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Returns a Portfolio images, other data, and options
 *
 * @param $id
 *
 * @return a Portfolio All data
 */
function portfolio_gallery_showPublishedportfolios_1($id)
{
    global $wpdb;

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_images where portfolio_id = '%d' order by ordering ASC", $id);

    $images = $wpdb->get_results($query);
    /***<title display>***/
    $title = array();
    $number = 0;
    foreach ($images as $key => $row) {
        $imagesuploader = explode(";", $row->image_url);
        array_pop($imagesuploader);
        $count = count($imagesuploader);
        for ($i = 0; $i < $count; $i++) {
            $pathinfo = pathinfo($imagesuploader[$i]);
            $filename = $pathinfo["filename"];
            $filename = strtolower($filename);
            $query = $wpdb->prepare("SELECT post_title FROM " . $wpdb->prefix . "posts where post_name = '%s'", $filename);
            $post_result = $wpdb->get_var($query);
            $concat = $post_result . "_-_-_" . $imagesuploader[$i];
            if (in_array($concat, $title)) {
                continue;
            }
            $title[$number] = $concat;
            $number++;
        }
    }
    /***</title display>***/

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios where id = '%d' order by id ASC", $id);

    $portfolio = $wpdb->get_results($query);

    $query = "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_params";


    $rowspar = $wpdb->get_results($query);

    $paramssld = array();
    foreach ($rowspar as $rowpar) {
        $key = $rowpar->name;
        $value = $rowpar->value;
        $paramssld[$key] = $value;
    }

    return front_end_portfolio($images, $paramssld, $portfolio, $title);

}

if (!function_exists('huge_it_title_img_display')) {
    function huge_it_title_img_display($image_name, $title)
    {
        for ($i = 0; $i < count($title); $i++) {
            $title_explode = explode("_-_-_", $title[$i]);
            if ($title_explode[1] == $image_name) {
                echo $title_explode[0];
            } else {
                echo "";
            }
        }
    }
}

/**
 * Get all general options parameters in a single array
 *
 * @todo: use wp_options instead
 *
 * @return array Array of all general options
 */
function portfolio_gallery_get_general_options()
{
    global $wpdb;

    $query = "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_params";

    $param_rows = $wpdb->get_results($query);

    $params = array();
    foreach ($param_rows as $param_row) {
        $key = $param_row->name;
        $value = $param_row->value;
        $params[$key] = $value;
    }

    return $params;
}

function portfolio_gallery_get_view_slag_by_id($id)
{
    global $wpdb;
    $query = $wpdb->prepare("SELECT portfolio_list_effects_s from " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE id=%d", $id);
    $view = $wpdb->get_var($query);
    switch ($view) {
        case 0:
            $slug = 'toggle-up-down';
            break;
        case 1:
            $slug = 'full-height';
            break;
        case 2:
            $slug = 'content-popup';
            break;
        case 3:
            $slug = 'full-width';
            break;
        case 4:
            $slug = 'faq';
            break;
        case 5:
            $slug = 'content-slider';
            break;
        case 6:
            $slug = 'lightbox-gallery';
            break;
    }
    return $slug;
}

/**
 * Get attachment ID by image src
 *
 * @param $image_url
 * @return mixed
 */
function portfolio_gallery_get_image_id($image_url)
{
    global $wpdb;
    $attachment = $wpdb->get_var($wpdb->prepare("SELECT ID FROM " . $wpdb->prefix . "posts WHERE guid=%s", $image_url));
    return $attachment;
}

/**
 * Get image url by image src, width, height
 *
 * @param $image_src
 * @param $image_width
 * @param $image_height
 * @param bool $is_attachment
 * @return false|string
 */
function portfolio_gallery_get_image_by_sizes_and_src($image_src, $image_sizes, $is_thumbnail)
{
    $is_attachment = portfolio_gallery_get_image_id($image_src);

    if (is_string($image_sizes)) {
        $image_sizes = $image_sizes;
        $img_width = intval($image_sizes);
    }
    if (is_object($image_sizes)) {
        // Closures are currently implemented as objects
        $image_sizes = array($image_sizes, '');
    } /*else {
        $image_sizes = (array) $image_sizes;
    }*/
    if (!$is_attachment) {
        $image_url = $image_src;
    } else {
        $attachment_id = portfolio_gallery_get_image_id($image_src);
        $natural_img_width = explode(',', wp_get_attachment_image_sizes($attachment_id, 'full'));
        $natural_img_width = $natural_img_width[1];
        $natural_img_width = str_replace(' ', '', $natural_img_width);
        $natural_img_width = intval(str_replace('px', '', $natural_img_width));
        if (isset($img_width)) {
            if ($img_width <= 150 && !$is_thumbnail)
                $image_url = wp_get_attachment_image_url($attachment_id, 'medium');
            elseif ($img_width >= $natural_img_width && !$is_thumbnail)
                $image_url = wp_get_attachment_image_url($attachment_id, 'full');
            else
                $image_url = wp_get_attachment_image_url($attachment_id, $img_width);
        } else
            $image_url = wp_get_attachment_image_url($attachment_id, $image_sizes);
    }
    return $image_url;
}
