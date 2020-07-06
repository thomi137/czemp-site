<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//@todo: prefixes
/**
 * Insert posts
 *
 * @param $id
 *
 * @return string
 */
function portfolio_gallery_popup_posts( $id ) {
	global $wpdb;

	if ( isset( $_GET["removeslide"] ) ) {
		if ( $_GET["removeslide"] != '' ) {


			$wpdb->query( "DELETE FROM " . $wpdb->prefix . "huge_itportfolio_images  WHERE id = " . $_GET["removeslide"] . " " );


		}
	}

	$query = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE id= %d", $id );
	$row   = $wpdb->get_row( $query );
	if ( ! isset( $row->portfolio_list_effects_s ) ) {
		return 'id not found';
	}
	$images    = explode( ";;;", $row->portfolio_list_effects_s );
	$par       = explode( '	', $row->param );
	$count_ord = count( $images );
	$cat_row   = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE id!=" . $id . " and sl_width=0" );
	$cat_row   = portfolio_gallery_open_cat_in_tree( $cat_row );
	$query     = $wpdb->prepare( "SELECT name,ordering FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE sl_width=%d  ORDER BY `ordering` ", $row->sl_width );
	$ord_elem  = $wpdb->get_results( $query );

	$query = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_images where portfolio_id = %d order by id ASC  ", $row->id );
	$rowim = $wpdb->get_results( $query );

	if ( isset( $_GET["addslide"] ) ) {
		if ( $_GET["addslide"] == 1 ) {

			$table_name = $wpdb->prefix . "huge_itportfolio_images";
			$sql_2      = "
INSERT INTO 

`" . $table_name . "` ( `name`, `portfolio_id`, `description`, `image_url`, `sl_url`, `ordering`, `published`, `published_in_sl_width`) VALUES
( '', '" . $row->id . "', '', '', '', 'par_TV', 2, '1' )";

			$wpdb->query( $sql_huge_itportfolio_images );


			$wpdb->query( $sql_2 );

		}
	}


	$query  = "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios order by id ASC";
	$rowsld = $wpdb->get_results( $query );

	$query = "SELECT *  from " . $wpdb->prefix . "huge_itportfolio_params ";

	$rowspar = $wpdb->get_results( $query );

	$paramssld = array();
	foreach ( $rowspar as $rowpar ) {
		$key               = $rowpar->name;
		$value             = $rowpar->value;
		$paramssld[ $key ] = $value;
	}

	$query     = "SELECT * FROM " . $wpdb->prefix . "posts where post_type = 'post' and post_status = 'publish' order by id ASC";
	$rowsposts = $wpdb->get_results( $query );

	$categories = get_categories();
	if ( isset( $_POST["iframecatid"] ) ) {
		$iframecatid = $_POST["iframecatid"];
	} else {
		$iframecatid = $categories[0]->cat_ID;
	}

	$query      = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "term_relationships where term_taxonomy_id = %d order by object_id ASC", $iframecatid );
	$rowsposts8 = $wpdb->get_results( $query );


	foreach ( $rowsposts8 as $rowsposts13 ) {
		$query      = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "posts where post_type = 'post' and post_status = 'publish' and ID = %d  order by ID ASC", $rowsposts13->object_id );
		$rowsposts1 = $wpdb->get_results( $query );

		$postsbycat = $rowsposts1;

	}

	if ( isset( $_GET["closepop"] ) ) {
		if ( $_GET["closepop"] == 1 ) {

			if ( $_POST["popupposts"] != 'none' and $_POST["popupposts"] != '' ) {

				/*  	echo $_GET["closepop"].'sdasdasdsad';
			echo $_POST["popupposts"].'dddddddd';*/

				$popuppostsposts = explode( ";", $_POST["popupposts"] );

				array_pop( $popuppostsposts );

				foreach ( $popuppostsposts as $popuppostsposts1 ) {
					$my_id = $popuppostsposts1;

					$post_id_1 = get_post( $my_id );


					$post_image = wp_get_attachment_url( get_post_thumbnail_id( $popuppostsposts1 ) );
					$posturl    = get_permalink( $popuppostsposts1 );
					$table_name = $wpdb->prefix . "huge_itportfolio_images";

					$descnohtmlnoq  = strip_tags( $post_id_1->post_content );
					$descnohtmlnoq1 = html_entity_decode( $descnohtmlnoq );
					$descnohtmlnoq1 = htmlentities( $descnohtmlnoq1, ENT_QUOTES, "UTF-8" );


					$sql_posts = "
INSERT INTO 

`" . $table_name . "` ( `name`, `portfolio_id`, `description`, `image_url`, `sl_url`, `sl_type`, `link_target`, `ordering`, `published`, `published_in_sl_width`) VALUES
( '" . $post_id_1->post_title . "', '" . $row->id . "', '" . $descnohtmlnoq1 . "', '" . $post_image . "', '" . $posturl . "', 'image', 'on', '0', '2', '1' )";


					$wpdb->query( $sql_posts );


				}

			}
			if ( ! ( $_POST["lastposts"] ) ) {
				$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET published = '" . $_POST["posthuge-it-description-length"] . "' WHERE id = '" . $_GET['id'] . "' " );
			}
		}
	}

	if ( isset( $_POST["lastposts"] ) ) {
		$query         = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "posts where post_type = 'post' and post_status = 'publish' order by id DESC LIMIT 0, " . $_POST["lastposts"] . "" );
		$rowspostslast = $wpdb->get_results( $query );
		foreach ( $rowspostslast as $rowspostslastfor ) {

			$my_id     = $rowspostslastfor;
			$post_id_1 = get_post( $my_id );


			$post_image     = wp_get_attachment_url( get_post_thumbnail_id( $rowspostslastfor ) );
			$posturl        = get_permalink( $rowspostslastfor );
			$table_name     = $wpdb->prefix . "huge_itportfolio_images";
			$descnohtmlno   = strip_tags( $post_id_1->post_content );
			$descnohtmlno1  = html_entity_decode( $descnohtmlno );
			$lengthtextpost = '300';
			$descnohtmlno2  = substr_replace( $descnohtmlno1, "", $lengthtextpost );
			$descnohtmlno3  = htmlentities( $descnohtmlno2, ENT_QUOTES, "UTF-8" );
			$posttitle      = htmlentities( $post_id_1->post_title, ENT_QUOTES, "UTF-8" );
			$posturl2       = htmlentities( $posturl, ENT_QUOTES, "UTF-8" );


			$sql_lastposts = "INSERT INTO 
`" . $table_name . "` ( `name`, `portfolio_id`, `description`, `image_url`, `sl_url`, `ordering`, `published`, `published_in_sl_width`) VALUES
( '" . $posttitle . "', '" . $row->id . "', '" . $descnohtmlno3 . "', '" . $post_image . "', '" . $posturl . "', 'par_TV', 2, '1' )";

			$wpdb->query( $sql_huge_itportfolio_images );

			$wpdb->query( $sql_lastposts );
		}
	}

	Html_portfolio_gallery_popup_posts( $ord_elem, $count_ord, $images, $row, $cat_row, $rowim, $rowsld, $paramssld, $rowsposts, $rowsposts8, $postsbycat );
}

/**
 * Prints Admin Portfolios list pagination HTML
 *
 * @param $count_items
 * @param $page_number
 * @param string $serch_fields
 */
function portfolio_gallery_print_html_nav( $count_items, $page_number, $serch_fields = "" ) {
	?>
	<script type="text/javascript">
		function clear_serch_texts() {
			document.getElementById("serch_or_not").value = '';
		}
		function submit_href(x, y) {
			var items_county =<?php if ( $count_items ) {
				if ( $count_items % 20 ) {
					echo ( $count_items - $count_items % 20 ) / 20 + 1;
				} else {
					echo ( $count_items - $count_items % 20 ) / 20;
				}
			} else {
				echo 1;
			}?>;
			if (document.getElementById("serch_or_not").value != "search") {
				clear_serch_texts();
			}
			switch (y) {
				case 1:
					if (x >= items_county) document.getElementById('page_number').value = items_county;

					else
						document.getElementById('page_number').value = x + 1
					break;
				case 2:
					document.getElementById('page_number').value = items_county;
					break;
				case -1:
					if (x == 1) document.getElementById('page_number').value = 1;

					else
						document.getElementById('page_number').value = x - 1;
					break;
				case -2:
					document.getElementById('page_number').value = 1;
					break;
				default:
					document.getElementById('page_number').value = 1;
			}
			document.getElementById('admin_form').submit();

		}

	</script>
	<div class="tablenav top" style="width:95%">
		<?php if ( $serch_fields != "" ) {
			echo $serch_fields;
		}
		?>
		<div class="tablenav-pages">
			<span class="displaying-num"><?php echo $count_items; ?> items</span>
			<?php if ( $count_items > 20 ) {

			if ( $page_number == 1 ) {
				$first_page = "first-page disabled";
				$prev_page  = "prev-page disabled";
				$next_page  = "next-page";
				$last_page  = "last-page";
			}
			if ( $page_number >= ( 1 + ( $count_items - $count_items % 20 ) / 20 ) ) {
				$first_page = "first-page ";
				$prev_page  = "prev-page";
				$next_page  = "next-page disabled";
				$last_page  = "last-page disabled";
			}

			?>
			<span class="pagination-links">
	<a class="<?php echo $first_page; ?>" title="Go to the first page"
	   href="javascript:submit_href(<?php echo $page_number; ?>,-2);">«</a>
	<a class="<?php echo $prev_page; ?>" title="Go to the previous page"
	   href="javascript:submit_href(<?php echo $page_number; ?>,-1);">‹</a>
	<span class="paging-input">
	<span class="total-pages"><?php echo $page_number; ?></span>
	of <span class="total-pages">
	<?php echo ( $count_items - $count_items % 20 ) / 20 + 1; ?>
	</span>
	</span>
	<a class="<?php echo $next_page ?>" title="Go to the next page"
	   href="javascript:submit_href(<?php echo $page_number; ?>,1);">›</a>
	<a class="<?php echo $last_page ?>" title="Go to the last page"
	   href="javascript:submit_href(<?php echo $page_number; ?>,2);">»</a>
				<?php }
				?>
	</span>
		</div>
	</div>
	<input type="hidden" id="page_number" name="page_number" value="<?php if ( isset( $_POST['page_number'] ) ) {
		echo $_POST['page_number'];
	} else {
		echo '1';
	} ?>"/>

	<input type="hidden" id="serch_or_not" name="serch_or_not" value="<?php if ( isset( $_POST["serch_or_not"] ) ) {
		echo $_POST["serch_or_not"];
	} ?>"/>
	<?php

}

/**
 * Get Prtfolio id
 *
 * @return int
 */
function portfolio_gallery_get_portfolio_id() {
	if ( isset( $_GET["id"] ) ) {
		$id = $_GET["id"];
	} else {
		$id = 0;
	}

	return $id;
}

/**
 * Get $_GET['task']
 *
 * @return string
 */
function portfolio_gallery_get_portfolio_task() {
	if ( isset( $_GET["task"] ) ) {
		$task = $_GET["task"];
	} else {
		$task = '';
	}

	return $task;
}

/**
 * @param $catt
 * @param string $tree_problem
 * @param int $hihiih
 *
 * @return array
 */
function portfolio_gallery_open_cat_in_tree( $catt, $tree_problem = '', $hihiih = 1 ) {

	global $wpdb;
	global $glob_ordering_in_cat;
	static $trr_cat = array();
	if ( ! isset( $search_tag ) ) {
		$search_tag = '';
	}
	if ( $hihiih ) {
		$trr_cat = array();
	}
	foreach ( $catt as $local_cat ) {
		$local_cat->name = $tree_problem . $local_cat->name;
		array_push( $trr_cat, $local_cat );
		$new_cat_query = "SELECT  a.* ,  COUNT(b.id) AS count, g.par_name AS par_name FROM " . $wpdb->prefix . "huge_itportfolio_portfolios  AS a LEFT JOIN " . $wpdb->prefix . "huge_itportfolio_portfolios AS b ON a.id = b.sl_width LEFT JOIN (SELECT  " . $wpdb->prefix . "huge_itportfolio_portfolios.ordering as ordering," . $wpdb->prefix . "huge_itportfolio_portfolios.id AS id, COUNT( " . $wpdb->prefix . "huge_itportfolio_images.portfolio_id ) AS prod_count
FROM " . $wpdb->prefix . "huge_itportfolio_images, " . $wpdb->prefix . "huge_itportfolio_portfolios
WHERE " . $wpdb->prefix . "huge_itportfolio_images.portfolio_id = " . $wpdb->prefix . "huge_itportfolio_portfolios.id
GROUP BY " . $wpdb->prefix . "huge_itportfolio_images.portfolio_id) AS c ON c.id = a.id LEFT JOIN
(SELECT " . $wpdb->prefix . "huge_itportfolio_portfolios.name AS par_name," . $wpdb->prefix . "huge_itportfolio_portfolios.id FROM " . $wpdb->prefix . "huge_itportfolio_portfolios) AS g
 ON a.sl_width=g.id WHERE a.name LIKE '%" . $search_tag . "%' AND a.sl_width=" . $local_cat->id . " group by a.id  " . $glob_ordering_in_cat;
		$new_cat       = $wpdb->get_results( $new_cat_query );
		portfolio_gallery_open_cat_in_tree( $new_cat, $tree_problem . "— ", 0 );
	}

	return $trr_cat;
}