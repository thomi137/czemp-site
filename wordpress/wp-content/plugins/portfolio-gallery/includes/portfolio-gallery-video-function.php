<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'youtube_or_vimeo_portfolio' ) ) {

	/**
	 * Detecting youtube or video link
	 * 
	 * @param $url
	 *
	 * @return string
	 */
	function portfolio_gallery_youtube_or_vimeo_portfolio ($url ) {
		if ( strpos( $url, 'youtube' ) !== false || strpos( $url, 'youtu' ) !== false ) {
			if ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match ) ) {
				return 'youtube';
			}
		} elseif ( strpos( $url, 'vimeo' ) !== false ) {
			$explode = explode( "/", $url );
			$end     = end( $explode );
			if ( strlen( $end ) == 8 || strlen( $end ) == 9 ) {
				return 'vimeo';
			}
		}

		return 'image';
	}

}

if ( ! function_exists( 'portfolio_gallery_get_video_id_from_url_portfolio' ) ) {
	/**
	 * Returns Youtube or Vimeo URL ID
	 *
	 * @param $url
	 *
	 * @return array
	 */
	function portfolio_gallery_get_video_id_from_url_portfolio( $url ) {
		if ( strpos( $url, 'youtube' ) !== false || strpos( $url, 'youtu' ) !== false ) {
			if ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match ) ) {
				return array( $match[1], 'youtube' );
			}
		} else {
			$vimeoid = explode( "/", $url );
			$vimeoid = end( $vimeoid );

			return array( $vimeoid, 'vimeo' );
		}
	}
}


