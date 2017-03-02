<?php

/*
 * Plugin Name: Propeller Instagram Generator
 * Version: 1.0.0
 * Author: Propeller
 * Description: Generate posts to be used with a relationship field based on selected Instagram posts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} else {
	new AdminArea();
}


class DataHandling {
	public static function fetch( $url ) {

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
		$result = curl_exec( $ch );
		curl_close( $ch );

		return $result;

	}
}

class InstagramUsers {

	public $result;
	public $error = false;
	private $access_token = '414143281.e2a9043.6d4acb839c38488f831d826bf29d32fe';


	public function __construct( $count = 4 ) {

		$this->count = $count;

		try {

			$this->result = json_decode( DataHandling::fetch( 'https://api.instagram.com/v1/users/search?q=' . $_POST['user_id'] . '&access_token=' . $this->access_token . '&count=' . $this->count ) );

			if ( isset( $this->result->meta->error_message ) ) {
				$this->error = $this->result->meta->error_message;
			} else {
				$this->result = $this->result->data;
			}

		} catch ( Exception $e ) {
			$this->error = $e->getMessage();
		}

	}

}

class InstagramPosts {

	public $result;
	public $error = false;

	private $access_token = '414143281.e2a9043.6d4acb839c38488f831d826bf29d32fe';


	public function __construct( $count = 30 ) {

		$this->count = $count;

		try {

			$this->result = json_decode( DataHandling::fetch( 'https://api.instagram.com/v1/users/' . $_POST['chosen_user'] . '/media/recent?count=' . $this->count . '&access_token=' . $this->access_token ) );

			if ( isset( $this->result->meta->error_message ) ) {
				$this->error = $this->result->meta->error_message;
			} else {
				$this->result = $this->result->data;
			}

		} catch ( Exception $e ) {
			$this->error = $e->getMessage();
		}

	}

}


class AdminArea {

	public $icon;
	public $options;

	public static function get_option( $set, $property ) {

		try {

			$option = get_option( 'prop_' . $set );

			if ( isset( $option[ $property ] ) && $option[ $property ] !== '' ) {

				return $option[ $property ];

			} else {

				return false;

			}

		} catch ( Exception $e ) {

			print $e->getMessage();

		}

	}

	public function __construct() {

		if ( is_admin() ) {

			$this->icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjIuMSwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zOnNrZXRjaD0iaHR0cDovL3d3dy5ib2hlbWlhbmNvZGluZy5jb20vc2tldGNoL25zIgoJIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgMzggMzgiCgkgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMzggMzg7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4KCS5zdDB7ZmlsbDojMjcyNzI1O30KPC9zdHlsZT4KPHRpdGxlPlNsaWNlIDE8L3RpdGxlPgo8ZGVzYz5DcmVhdGVkIHdpdGggU2tldGNoLjwvZGVzYz4KPGcgaWQ9IlBhZ2UtMV8xXyIgc2tldGNoOnR5cGU9Ik1TUGFnZSI+Cgk8ZyBpZD0icHJvcGVsbGVyIiBza2V0Y2g6dHlwZT0iTVNMYXllckdyb3VwIj4KCQk8ZyBpZD0iUGFnZS0xIiBza2V0Y2g6dHlwZT0iTVNTaGFwZUdyb3VwIj4KCQkJPGcgaWQ9IlByb3BlbGxlciI+CgkJCQk8cGF0aCBpZD0iU2hhcGUiIGNsYXNzPSJzdDAiIGQ9Ik0yMi42LDIuOGwtMy4zLDBsMCwwaC00LjFMOSwzNi4zbDAsMEg5bDAsMGMzLjIsMCw2LjMtMi41LDYuOS01LjdsMCwwbDAuMS0wLjhoMGwwLjItMS4ybDMsMAoJCQkJCWMzLjIsMCw2LjUtMi4zLDctNS4zbDAsMGwyLjUtMTMuNWwwLDBjMC4xLTAuNCwwLjEtMC45LDAuMS0wLjlDMjguOSw1LjUsMjYsMi44LDIyLjYsMi44TDIyLjYsMi44TDIyLjYsMi44eiBNMjAuMiwyNC41CgkJCQkJTDIwLjIsMjQuNWMwLDAsMCwwLjItMC4xLDAuM2MtMC4xLDAuOC0wLjksMS4zLTEuNywxLjNoLTEuN2wzLjgtMjAuOWgxLjFjMC45LDAsMS43LDAuNywxLjcsMS42TDIwLjIsMjQuNUwyMC4yLDI0LjV6Ii8+CgkJCTwvZz4KCQk8L2c+Cgk8L2c+CjwvZz4KPC9zdmc+Cg==';

			add_action( 'admin_menu', [ $this, 'create_admin_menu' ] );
			add_action( 'admin_init', [ $this, 'register_admin_settings' ] );
			add_action( 'admin_head', [ $this, 'instagram_styles' ] );

			add_theme_support( 'post-thumbnails' );
			add_image_size('admin_thumb', 60, 60);
			add_filter( 'manage_posts_columns', [ $this, 'add_img_column' ] );
			add_filter( 'manage_posts_custom_column', [ $this, 'manage_img_column' ], 10, 2 );

			if ( ! post_type_exists( 'instagram-posts' ) ) {
				add_action( 'init', [ $this, 'create_post_type' ] );
			}

		}

	}

	public function add_img_column( $columns ) {
		$columns['img'] = 'Featured Image';

		return $columns;
	}

	public function manage_img_column( $column_name, $post_id ) {
		if( $column_name == 'img' ) {
			echo the_post_thumbnail('admin_thumb');
			return get_the_post_thumbnail($post_id, 'thumbnail');
		}
	}

	public function create_admin_menu() {

		add_menu_page(

			'Instagram Generator',
			'Instagram Gen',
			'administrator',
			'instagram-generator',
			[ $this, 'render_generator_page' ],
			$this->icon, 2

		);

	}

	public function register_admin_settings() {

		register_setting(

			'propeller_generator',
			'prop_generator'

		);

	}

	public function create_post_type() {

		register_post_type( 'instagram-posts',

			array(

				//  What will the CPT be known as?
				'labels'                => array(
					'name'               => __( 'Instagram Posts', 'instagram posts' ),
					'singular_name'      => __( 'Instagram Post', 'instagram post' ),
					'menu_name'          => __( 'Instagram' ),
					'name_admin_bar'     => __( 'Instagram' ),
					'add_new'            => __( 'Add New' ),
					'add_new_item'       => __( 'Add New Post' ),
					'new_item'           => __( 'New Post' ),
					'edit_item'          => __( 'Edit Post' ),
					'view_item'          => __( 'View Post' ),
					'all_items'          => __( 'All Instagram Posts' ),
					'search_items'       => __( 'Search Instagram Posts' ),
					'parent_item_colon'  => __( 'Parent Instagram Posts:' ),
					'not_found'          => __( 'No instagram posts found.' ),
					'not_found_in_trash' => __( 'No instagram posts found in Trash.' )
				),

				//  Settings - how will the CPT behave?
				'with_front'            => true,
				'publicly_queryable'    => false,
				'public'                => true,
				'show_ui'               => true,
				'rewrite'               => array( 'slug' => 'instagram-posts' ),
				'exclude_from_search'   => false,
				'capability_type'       => 'post',
				'has_archive'           => true,
				'menu_icon'             => 'dashicons-format-chat',
				'menu_position'         => 19,
				'rest_controller_class' => 'WP_REST_Posts_Controller',

				//  What editable fields will the CPT support?
				'supports'              => array(
					'title',
					'editor',
					'thumbnail'
				),

				// Which Taxonomies will be applicable?  //
				'taxonomies'            => array(
					'instagram-posts'
				),

			)

		);

	}

	public function remove_admin_submenus() {

		remove_submenu_page( 'edit.php?post_type=prop-social-posts', 'edit-tags.php?taxonomy=prop-social-media-accounts&amp;post_type=prop-social-posts' );
		remove_submenu_page( 'edit.php?post_type=prop-social-posts', 'post-new.php?post_type=prop-social-posts' );

	}


	public function render_generator_page() {

		$this->options = get_option( 'prop_generator' ); ?>

		<div class="wrap">
		<h1>Propeller Instagram Generator</h1>
		<?php  //var_dump( $_POST ); ?>

			<?php if ( isset( $_POST['scraped_users'] ) ): ?>
				<?php $instagramUsers = new InstagramUsers; ?>
				<?php if ( isset( $instagramUsers->result ) ): ?>
					<h2>Please select an account to scrape images from</h2>
					<form method="post" action="" class="ig_generator">
						<table class="wp-list-table widefat fixed striped pages">
							<thead>
							<tr>
								<th scope="col" id="author">
									<a href="#"><span>User</span></a>
								</th>
								<th scope="col" id="date">
									<a href="#"><span>Fetch</span></a>
								</th>
							</tr>
							</thead>
							<tbody id="the-list">
							<?php foreach ( $instagramUsers->result as $scraped_user ): ?>

								<tr>
									<td data-colname="Author">
										<img src="<?= $scraped_user->profile_picture; ?>"
										     alt="<?= $scraped_user->username; ?>" width="50"/>
										<?= $scraped_user->username; ?>
									</td>
									<td>
										<?php wp_nonce_field( 'post_nonce', 'post_nonce_id' ); ?>
										<button type="submit" name="chosen_user" id="submit"
										        value="<?= $scraped_user->id; ?>"
										        class="button button-primary">Fetch
										</button>
									</td>
								</tr>

							<?php endforeach; ?>
							</tbody>
						</table>
					</form>
				<? endif; ?>

			<?php else: ?>

				<? if ( empty( $_POST['chosen_user'] ) && empty( $_POST['post_nonce_images'] ) ): ?>

					<h2>Please enter a username to search for</h2>

					<form method="post" action="">
						<table class="form-table">
							<tbody>
							<tr>
								<th scope="row"><label for="username">INSTAGRAM USERNAME</label></th>
								<td>
									<input type="text" name="user_id" required="" id="user_id" value=""
									       class="regular-text"
									       autocomplete="off">
								</td>
							</tr>
							</tbody>
						</table>
						<?php wp_nonce_field( 'post_nonce', 'post_nonce_user' ); ?>
						<input type="submit" name="scraped_users" id="submit" value="Scrape"
						       class="button button-primary">
					</form>

				<? endif; ?>

			<?php endif; ?>

			<?php if ( isset( $_POST['chosen_user'] ) && isset( $_POST['post_nonce_id'] ) ) : ?>

				<?php $instagram = new InstagramPosts; ?>

				<h3>Instagram</h3>
				<form name="get_images" method="post" action="" enctype="multipart/form-data">
					<ul class="ig_generator">
						<?php foreach ( $instagram->result as $photo ): ?>

							<li>
								<input type="checkbox" id="<?php echo $photo->id ?>" name="scraped_id[]"
								       value="<?php echo $photo->id ?>"/>

								<label for="<?php echo $photo->id ?>">
									<img src="<?php echo $photo->images->low_resolution->url ?>" width="125"
									     height"125"/>
								</label>
							</li>

						<?php endforeach; ?>
					</ul>
					<?php wp_nonce_field( 'post_nonce', 'post_nonce_images' ); ?>
					<p class="submit">
						<input type="hidden" name="chosen_user" value="<?= $_POST['chosen_user'] ?>"/>
						<input type="submit" name="chosen_posts" id="submit" value="Generate"
						       class="button button-primary">
					</p>
				</form>

			<?php endif; ?>

			<? if ( ! empty( $_POST['scraped_id'] ) && isset( $_POST['post_nonce_images'] ) ): ?>

				<?php $instagram = new InstagramPosts; ?>
				<?php $post_generator = new PostGenerator(); ?>

				<?php foreach ( $instagram->result as $unmatched_id ): ?>

					<? if ( in_array( $unmatched_id->id, $_POST['scraped_id'] ) ): ?>
						<?php $post_generator::populate_posts( $unmatched_id ); ?>
					<? endif; ?>

				<? endforeach; ?>
				<p style='color:green'>Generator Task Completed Successfully!</p>
				<a class="button button-primary" href="<?= admin_url( 'edit.php?post_type=instagram-posts') ?>">See Generated Links</a>
			<? endif; ?>

		<?php

	}

	public function instagram_styles() {
		echo '
	<style>
	    .ig_generator {
	        display: block;
	        width: 100%;
	        max-width: 800px;
	        list-style-type: none;
		}
	
		.ig_generator li {
		  display: inline-block;
		  font-size:0;
		}

		.ig_generator input[type="checkbox"] {
		  display: none;
		}
		
		.ig_generator label {
		  display: block;
		  position: relative;
		  margin: 7px;
		  box-shadow: 0 0 0 1px rgba(0,0,0,.1);
          background: #eee;
          cursor: pointer;
		}
		
		.ig_generator label:before {
		  background-color: white;
		  color: white;
		  content: " ";
		  display: block;
		  border: 1px solid #fff;
		  position: absolute;
		  top: -5px;
		  right: -5px;
		  width: 25px;
		  height: 25px;
		  text-align: center;
		  line-height: 28px;
		  transform: scale(0);
		}
		
		.ig_generator label img {
		display: block;
		  max-width: 135px;
		  width: 100%;
		}
		
		.ig_generator :checked + label {
		  border-color: #ddd;
		}
		
		.ig_generator :checked + label:before {
		  content: "✓";
		  font-size: 20px;
		  background-color: #0073aa;
		  transform: scale(1);
		}
		
		.ig_generator :checked + label img {
		  box-shadow: inset 0 0 0 5px #fff, inset 0 0 0 7px #ccc;
		  z-index: -1;
		}
    </style>';
	}

}

class PostGenerator {

	public function __construct() {
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . '/wp-admin/includes/post.php' );
		flush_rewrite_rules();

	}

	public static function get_photo_name( $data ) {
		$photo_id   = explode( '/p/', $data );
		$photo_name = trim( end( $photo_id ), '/' );

		return $photo_name;
	}

	public static function populate_posts( $selected_id ) {

		$title   = self::get_photo_name( $selected_id->link );
		$src     = $selected_id->images->standard_resolution->url;
		$content = $selected_id->caption->text;
		$date    = date( "Y-m-d H:i:s", $selected_id->created_time );

		// Create post object
		$myPost = array(

			'post_title'   => $title,
			'post_content' => $content,
			'post_date'    => $date,
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'instagram-posts'

		);


		if ( $postId = post_exists( $title ) != 0 ) {
			$myPost['post_id'] = $postId;
		}

		// Get the post id
		$postId = wp_insert_post( $myPost );


		// Insert the attachment
		if ( $attachId = post_exists( $title ) != 0 ) {

			media_sideload_image( $src, $postId, null, 'src' );

			// then find the last image added to the post attachments
			$attachments = get_posts( [
				'numberposts'    => '1',
				'post_parent'    => $postId,
				'post_type'      => 'attachment',
				'post_mime_type' => 'image/jpeg',
				'order'          => 'ASC'
			] );

			if ( sizeof( $attachments ) > 0 ) {
				// set image as the post thumbnail
				set_post_thumbnail( $postId, $attachments[0]->ID );
			}

		}


	}

}
