<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.
/**
 * Claudia Zemp functions and definitions.
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @author Thomas Prosser
 * @package czemp
 * @version 1.0
 * @since 1.0
 */

foreach ([
    'inc/setup.php',
    'inc/post-types.php',
    'inc/post-dates.php',
    'inc/rest-api.php',
    'inc/collection-thumbnail.php',
    'inc/maintenance.php',
    'inc/seo.php',
    'inc/admin.php',
    'inc/frontend.php',
    'inc/sortable.php',
] as $cz_include) {
    require_once get_stylesheet_directory() . '/' . $cz_include;
}
