<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class Portfolio_Gallery_Portfolios {

	public function __construct() {

	}

	/**
	 * Load Portfolios admin page
	 */
	public function load_portfolio_page() {
		global $wpdb;
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'portfolios_huge_it_portfolio' ) {
			$task = portfolio_gallery_get_portfolio_task();
			if ( $task == 'edit_cat' ) {
				$this->edit_portfolio( portfolio_gallery_get_portfolio_id() );
			}
			$id   = portfolio_gallery_get_portfolio_id();
		}
		switch ( $task ) {
			case 'portfolio_video':
				if ( $id ) {
					$this->insert_portfolio_gallery_video( $id );
				} else {
					$id = $wpdb->get_var( "SELECT MAX( id ) FROM " . $wpdb->prefix . "huge_itportfolio_portfolios" );
					$this->insert_portfolio_gallery_video( $id );
				}
				break;
			case 'portfolio_video_edit':
				if ( $id ) {
					$this->edit_video( $id );
				} else {
					$id = $wpdb->get_var( "SELECT MAX( id ) FROM " . $wpdb->prefix . "huge_itportfolio_portfolios" );
					$this->edit_video( $id );
				}
				break;
			case 'edit_cat':
				if ( $id ) {
					$this->edit_portfolio( $id );
				} else {
					$id = $wpdb->get_var( "SELECT MAX( id ) FROM " . $wpdb->prefix . "huge_itportfolio_portfolios" );
					$this->edit_portfolio( $id );
				}
				break;
			case 'save':
				if ( $id ) {
					$this->save_portfolio_data( $id );
				}
				break;
			case 'apply':
				if ( $id ) {
					$this->save_portfolio_data( $id );
					$this->edit_portfolio( $id );
				}
				break;
			case 'remove_cat':
				$this->remove_portfolio( $id );
				$this->show_portfolios_page();
				break;
			default:
				$this->show_portfolios_page();
				break;
		}
	}

	/**
	 * Shows Portfolio Main Page
	 */
	public function show_portfolios_page() {

		global $wpdb;

		if ( isset( $_POST['search_events_by_title'] ) ) {
			$_POST['search_events_by_title'] = esc_html( stripslashes( $_POST['search_events_by_title'] ) );
		}
		if ( isset( $_POST['asc_or_desc'] ) ) {
			$_POST['asc_or_desc'] = esc_js( $_POST['asc_or_desc'] );
		}
		if ( isset( $_POST['order_by'] ) ) {
			$_POST['order_by'] = esc_js( $_POST['order_by'] );
		}
		$where                 = '';
		$sort["custom_style"]  = "manage-column column-autor sortable desc";
		$sort["default_style"] = "manage-column column-autor sortable desc";
		$sort["sortid_by"]     = 'id';
		$sort["1_or_2"]        = 1;
		$order                 = '';

		if ( isset( $_POST['page_number'] ) ) {

			if ( $_POST['asc_or_desc'] ) {
				$sort["sortid_by"] = $_POST['order_by'];
				if ( $_POST['asc_or_desc'] == 1 ) {
					$sort["custom_style"] = "manage-column column-title sorted asc";
					$sort["1_or_2"]       = "2";
					$order                = "ORDER BY " . $sort["sortid_by"] . " ASC";
				} else {
					$sort["custom_style"] = "manage-column column-title sorted desc";
					$sort["1_or_2"]       = "1";
					$order                = "ORDER BY " . $sort["sortid_by"] . " DESC";
				}
			}
			if ( $_POST['page_number'] ) {
				$limit = ( $_POST['page_number'] - 1 ) * 20;
			} else {
				$limit = 0;
			}
		} else {
			$limit = 0;
		}
		if ( isset( $_POST['search_events_by_title'] ) ) {
			$search_tag = esc_html( stripslashes( $_POST['search_events_by_title'] ) );
		} else {
			$search_tag = "";
		}

		if ( isset( $_GET["catid"] ) ) {
			$cat_id = $_GET["catid"];
		} else {
			if ( isset( $_POST['cat_search'] ) ) {
				$cat_id = $_POST['cat_search'];
			} else {

				$cat_id = 0;
			}
		}

		if ( $search_tag ) {
			$where = " WHERE name LIKE '%" . $search_tag . "%' ";
		}
		if ( $where ) {
			if ( $cat_id ) {
				$where .= " AND sl_width=" . $cat_id;
			}

		} else {
			if ( $cat_id ) {
				$where .= " WHERE sl_width=" . $cat_id;
			}

		}

		$cat_row_query = "SELECT id,name FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE sl_width=0";

		// get the total number of records
		$query = "SELECT COUNT(*) FROM " . $wpdb->prefix . "huge_itportfolio_portfolios" . $where;

		$total            = $wpdb->get_var( $query );
		$pageNav['total'] = $total;
		$pageNav['limit'] = $limit / 20 + 1;

		if ( $cat_id ) {
			$query = "SELECT  a.* ,  COUNT(b.id) AS count, g.par_name AS par_name FROM " . $wpdb->prefix . "huge_itportfolio_portfolios  AS a LEFT JOIN " . $wpdb->prefix . "huge_itportfolio_portfolios AS b ON a.id = b.sl_width LEFT JOIN (SELECT  " . $wpdb->prefix . "huge_itportfolio_portfolios.ordering as ordering," . $wpdb->prefix . "huge_itportfolio_portfolios.id AS id, COUNT( " . $wpdb->prefix . "huge_itportfolio_images.portfolio_id ) AS prod_count
FROM " . $wpdb->prefix . "huge_itportfolio_images, " . $wpdb->prefix . "huge_itportfolio_portfolios
WHERE " . $wpdb->prefix . "huge_itportfolio_images.portfolio_id = " . $wpdb->prefix . "huge_itportfolio_portfolios.id
GROUP BY " . $wpdb->prefix . "huge_itportfolio_images.portfolio_id) AS c ON c.id = a.id LEFT JOIN
(SELECT " . $wpdb->prefix . "huge_itportfolio_portfolios.name AS par_name," . $wpdb->prefix . "huge_itportfolio_portfolios.id FROM " . $wpdb->prefix . "huge_itportfolio_portfolios) AS g
 ON a.sl_width=g.id WHERE  a.name LIKE '%" . $search_tag . "%' group by a.id " . $order . " " . " LIMIT " . $limit . ",20";

		} else {
			$query = "SELECT  a.* ,  COUNT(b.id) AS count, g.par_name AS par_name FROM " . $wpdb->prefix . "huge_itportfolio_portfolios  AS a LEFT JOIN " . $wpdb->prefix . "huge_itportfolio_portfolios AS b ON a.id = b.sl_width LEFT JOIN (SELECT  " . $wpdb->prefix . "huge_itportfolio_portfolios.ordering as ordering," . $wpdb->prefix . "huge_itportfolio_portfolios.id AS id, COUNT( " . $wpdb->prefix . "huge_itportfolio_images.portfolio_id ) AS prod_count
FROM " . $wpdb->prefix . "huge_itportfolio_images, " . $wpdb->prefix . "huge_itportfolio_portfolios
WHERE " . $wpdb->prefix . "huge_itportfolio_images.portfolio_id = " . $wpdb->prefix . "huge_itportfolio_portfolios.id
GROUP BY " . $wpdb->prefix . "huge_itportfolio_images.portfolio_id) AS c ON c.id = a.id LEFT JOIN
(SELECT " . $wpdb->prefix . "huge_itportfolio_portfolios.name AS par_name," . $wpdb->prefix . "huge_itportfolio_portfolios.id FROM " . $wpdb->prefix . "huge_itportfolio_portfolios) AS g
 ON a.sl_width=g.id WHERE a.name LIKE '%" . $search_tag . "%'  group by a.id " . $order . " " . " LIMIT " . $limit . ",20";
		}

		$rows = $wpdb->get_results( $query );
		global $glob_ordering_in_cat;
		if ( isset( $sort["sortid_by"] ) ) {
			if ( $sort["sortid_by"] == 'ordering' ) {
				if ( $_POST['asc_or_desc'] == 1 ) {
					$glob_ordering_in_cat = " ORDER BY ordering ASC";
				} else {
					$glob_ordering_in_cat = " ORDER BY ordering DESC";
				}
			}
		}
		$rows      = portfolio_gallery_open_cat_in_tree( $rows );
		$query     = "SELECT  " . $wpdb->prefix . "huge_itportfolio_portfolios.ordering," . $wpdb->prefix . "huge_itportfolio_portfolios.id, COUNT( " . $wpdb->prefix . "huge_itportfolio_images.portfolio_id ) AS prod_count
FROM " . $wpdb->prefix . "huge_itportfolio_images, " . $wpdb->prefix . "huge_itportfolio_portfolios
WHERE " . $wpdb->prefix . "huge_itportfolio_images.portfolio_id = " . $wpdb->prefix . "huge_itportfolio_portfolios.id
GROUP BY " . $wpdb->prefix . "huge_itportfolio_images.portfolio_id ";
		$prod_rows = $wpdb->get_results( $query );

		foreach ( $rows as $row ) {
			foreach ( $prod_rows as $row_1 ) {
				if ( $row->id == $row_1->id ) {
					$row->ordering   = $row_1->ordering;
					$row->prod_count = $row_1->prod_count;
				}
			}

		}

		require_once( PORTFOLIO_GALLERY_TEMPLATES_PATH . DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'portfolio-gallery-admin-portfolios-list.php' );

	}

	/**
	 * Prints Portfolio images after edit data
	 * 
	 * @param $id
	 *
	 * @return string
	 */
	public function edit_portfolio( $id ) {
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

		$query = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_images where portfolio_id = %d order by ordering ASC  ", $row->id );
		$rowim = $wpdb->get_results( $query );

		if ( isset( $_GET["addslide"] ) ) {
			if ( $_GET["addslide"] == 1 ) {

				$table_name = $wpdb->prefix . "huge_itportfolio_images";
				$sql_2      = "
INSERT INTO 

`" . $table_name . "` ( `name`, `portfolio_id`, `description`, `image_url`, `sl_url`, `ordering`, `published`, `published_in_sl_width`,) VALUES
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

		$rowsposts8 = '';
		$postsbycat = '';
		if ( isset( $_POST["iframecatid"] ) ) {
			$query      = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "term_relationships where term_taxonomy_id = %d order by object_id ASC", $_POST["iframecatid"] );
			$rowsposts8 = $wpdb->get_results( $query );


			foreach ( $rowsposts8 as $rowsposts13 ) {
				$query      = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "posts where post_type = 'post' and post_status = 'publish' and ID = %d  order by ID ASC", $rowsposts13->object_id );
				$rowsposts1 = $wpdb->get_results( $query );
				$postsbycat = $rowsposts1;

			}
		}
		require_once( PORTFOLIO_GALLERY_TEMPLATES_PATH . DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'portfolio-gallery-admin-portfolio-images-list-html.php' );
	}

	/**
	 * Edit portfolio images and data
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	function save_portfolio_data( $id ) {
		global $wpdb;
		if ( ! is_numeric( $id ) ) {
			echo 'insert numerc id';

			return '';
		}
		if ( ! ( isset( $_POST['sl_width'] ) && isset( $_POST["name"] ) ) ) {
			return '';
		}
		$cat_row    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE id!= %d ", $id ) );
		$corent_ord = $wpdb->get_var( $wpdb->prepare( 'SELECT `ordering` FROM ' . $wpdb->prefix . 'huge_itportfolio_portfolios WHERE id = %d AND sl_width=%d', $id, $_POST['sl_width'] ) );
		$max_ord    = $wpdb->get_var( 'SELECT MAX(ordering) FROM ' . $wpdb->prefix . 'huge_itportfolio_portfolios' );

		$query  = $wpdb->prepare( "SELECT sl_width FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE id = %d", $id );
		$id_bef = $wpdb->get_var( $query );

		if ( isset( $_POST["content"] ) ) {
			$script_cat = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', stripslashes( $_POST["content"] ) );
		}
		$name = $_POST["allCategories"];
		$name = str_replace( ' ', '_', $name );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  name = '" . wp_unslash( $_POST["name"] ) . "'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  sl_width = '" . $_POST["sl_width"] . "'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  sl_height = '" . $_POST["sl_height"] . "'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  pause_on_hover = '" . $_POST["pause_on_hover"] . "'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  autoslide = '" . $_POST["autoslide"] . "'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  portfolio_list_effects_s = '" . $_POST["portfolio_effects_list"] . "'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  sl_position = '" . $_POST["sl_position"] . "'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  description = '" . $_POST["sl_pausetime"] . "'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  param = '" . $_POST["sl_changespeed"] . "'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  ordering = '1'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  categories = '" . $name . "'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  ht_show_sorting = '" . $_POST["ht_show_sorting"] . "'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  ht_show_filtering = '" . $_POST["ht_show_filtering"] . "'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  show_loading = '" . $_POST["show_loading"] . "'  WHERE id = '" . $id . "' " );
		$wpdb->query( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  loading_icon_type = '" . $_POST["loading_icon_type"] . "'  WHERE id = '" . $id . "' " );


		$query = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE id = %d", $id );
		$row   = $wpdb->get_row( $query );


		/***<image optimize>***/

		$query = "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_params";

		$rowspar   = $wpdb->get_results( $query );
		$paramssld = array();
		foreach ( $rowspar as $rowpar ) {
			$key               = $rowpar->name;
			$value             = $rowpar->value;
			$paramssld[ $key ] = $value;
		}
		if ( isset( $_POST['changedvalues'] ) && $_POST['changedvalues'] != '' ) {
			$changedValues = preg_replace( '#[^0-9,]+#', '', $_POST['changedvalues'] );
			$query         = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_images where portfolio_id = %d AND id in (" . $changedValues . ") order by id ASC", $row->id );
			$rowim         = $wpdb->get_results( $query );
			foreach ( $rowim as $key => $rowimages ) {

				$imgDescription = wp_unslash( $_POST[ "im_description" . $rowimages->id . "" ] );
				$imgTitle       = wp_unslash( $_POST[ "titleimage" . $rowimages->id . "" ] );
				//var_dump($imgDescription);
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET  ordering = %d  WHERE ID = %d ", $_POST[ "order_by_" . $rowimages->id . "" ], $rowimages->id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET  link_target = '%s'  WHERE ID = %d ", $_POST[ "sl_link_target" . $rowimages->id . "" ], $rowimages->id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET  sl_url = '%s' WHERE ID = %d ", $_POST[ "sl_url" . $rowimages->id . "" ], $rowimages->id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET  name = '%s'  WHERE ID = %d ", $imgTitle, $rowimages->id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET  description = '%s'  WHERE ID = %d ", $imgDescription, $rowimages->id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET  image_url = '%s'  WHERE ID = %d ", $_POST[ "imagess" . $rowimages->id . "" ], $rowimages->id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET  category = '%s'  WHERE ID = %d ", $_POST[ "category" . $rowimages->id . "" ], $rowimages->id ) );
			}
		}

		if ( isset( $_POST['params'] ) ) {
			$params = $_POST['params'];
			foreach ( $params as $key => $value ) {
				$wpdb->update( $wpdb->prefix . 'huge_itportfolio_params',
					array( 'value' => $value ),
					array( 'name' => $key ),
					array( '%s' )
				);
			}

		}

		if ( $_POST["imagess"] != '' ) {
			$query = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_images where portfolio_id = %d order by id ASC", $row->id );
			$rowim = $wpdb->get_results( $query );
			foreach ( $rowim as $key => $rowimages ) {
				$orderingplus = $rowimages->ordering + 1;
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET  ordering = %d  WHERE ID = %d ", $orderingplus, $rowimages->id ) );
			}

			$table_name        = $wpdb->prefix . "huge_itportfolio_images";
			$imagesnewuploader = explode( ";;;", $_POST["imagess"] );

			array_pop( $imagesnewuploader );

			foreach ( $imagesnewuploader as $imagesnewupload ) {
				$sql_2 = "
INSERT INTO 

`" . $table_name . "` ( `name`, `portfolio_id`, `description`, `image_url`, `sl_url`, `ordering`, `published`, `published_in_sl_width`) VALUES
( '', '" . $row->id . "', '', '" . $imagesnewupload . ";', '', 'par_TV', 2, '1' )";


				$wpdb->query( $sql_2 );
			}
		}

		if ( isset( $_POST["posthuge-it-description-length"] ) ) {
			$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  published = %d WHERE id = %d ", $_POST["posthuge-it-description-length"], $_GET['id'] ) );
		}
		?>
		<div class="updated"><p><strong><?php _e( 'Item Saved' ); ?></strong></p></div>
		<?php

		return true;

	}

	/**
	 * Insert portfolio video
	 *
	 * @param $id
	 */
	function insert_portfolio_gallery_video( $id ) {
		global $wpdb;


		if ( isset( $_POST["huge_it_add_video_input"] ) && $_POST["huge_it_add_video_input"] != '' ) {
			if ( ! isset( $_GET['thumb_parent'] ) || $_GET['thumb_parent'] == null ) {

				$table_name   = $wpdb->prefix . "huge_itportfolio_images";
				$query        = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE id= %d", $id );
				$row          = $wpdb->get_row( $query );
				$query        = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_images where portfolio_id = %s ", $row->id );
				$rowplusorder = $wpdb->get_results( $query );

				foreach ( $rowplusorder as $key => $rowplusorders ) {

					if ( $rowplusorders->ordering == 0 ) {
						$rowplusorderspl = 1;
						$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET ordering = %d WHERE id = %s ", $rowplusorderspl, $rowplusorders->id ) );
					} else {
						$rowplusorderspl = $rowplusorders->ordering + 1;
						$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET ordering = %d WHERE id = %s ", $rowplusorderspl, $rowplusorders->id ) );
					}

				}
				$_POST["huge_it_add_video_input"] .= ";";
				$sql_video = "INSERT INTO 
			`" . $table_name . "` ( `name`, `portfolio_id`, `description`, `image_url`, `sl_url`, `sl_type`, `link_target`, `ordering`, `published`, `published_in_sl_width`,`category`) VALUES 
			( '" . $_POST["show_title"] . "', '" . $id . "', '" . $_POST["show_description"] . "', '" . $_POST["huge_it_add_video_input"] . "', '" . $_POST["show_url"] . "', 'video', 'on', '0', '1', '1','' )";
				$wpdb->query( $sql_video );
			} else {
				$query          = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE id= %d", $id );
				$row            = $wpdb->get_row( $query );
				$query          = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_images where portfolio_id = %s and id = %d", $row->id, $_GET['thumb_parent'] );
				$get_proj_image = $wpdb->get_row( $query );
				$get_proj_image->image_url .= $_POST["huge_it_add_video_input"] . ";";
				//$get_proj_image->image_url .= ";";
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET image_url = '%s' where portfolio_id = %s and id = %d", $get_proj_image->image_url, $row->id, $_GET['thumb_parent'] ) );
			}

		}
		require_once( PORTFOLIO_GALLERY_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'admin'.DIRECTORY_SEPARATOR.'portfolio-gallery-admin-video-add-html.php' );
	}

	/**
	 * Removes portfolio
	 *
	 * @param $id
	 */
	function remove_portfolio( $id ) {

		global $wpdb;
		$sql_remov_tag   = $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE id = %d", $id );
		$sql_remov_image = $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itportfolio_images WHERE portfolio_id = %d", $id );
		if ( ! $wpdb->query( $sql_remov_tag ) ) {
			?>
			<div id="message" class="error"><p>portfolio Not Deleted</p></div>
			<?php

		} else {
			$wpdb->query( $sql_remov_image );
			?>
			<div class="updated"><p><strong><?php _e( 'Item Deleted.' ); ?></strong></p></div>
			<?php
		}
	}

	/**
	 *Edit portfolio video
	 *
	 * @param $id
	 */
	function edit_video( $id ) {
		global $wpdb;
		$thumb                  = $_GET["thumb"];
		$portfolio_id           = $_GET["portfolio_id"];
		$id                     = $_GET["id"];
		$query                  = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_images where portfolio_id = %s and id = %d", $portfolio_id, $id );
		$get_proj_image         = $wpdb->get_row( $query );
		$input_edit_video       = explode( ";", $get_proj_image->image_url );//var_dump($input_edit_video );exit;
		$input_edit_video_thumb = $input_edit_video[ $thumb ];
		$video                  = portfolio_gallery_youtube_or_vimeo_portfolio( $input_edit_video_thumb );

		if ( isset( $_POST["huge_it_add_video_input"] ) && $_POST["huge_it_add_video_input"] != '' ) {
			$input_edit_video[ $thumb ] = $_POST["huge_it_add_video_input"];
			$new_url                    = implode( ";", $input_edit_video );
			$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_images SET image_url = '%s' where portfolio_id = %s and id = %d", $new_url, $portfolio_id, $id ) );
		}

		if ( isset( $_POST["huge_it_edit_video_input"] ) && $_POST["huge_it_edit_video_input"] != '' ) {
			$edit = $_POST["huge_it_edit_video_input"];
		} else {
			$edit = '';
		}
		$video_id = portfolio_gallery_get_video_id_from_url_portfolio($input_edit_video_thumb);

		require_once( PORTFOLIO_GALLERY_TEMPLATES_PATH. DIRECTORY_SEPARATOR .'admin'.DIRECTORY_SEPARATOR.'portfolio-gallery-admin-video-edit-html.php' );

	}
}

