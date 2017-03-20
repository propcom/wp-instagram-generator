<?php

class Setup {

	public $icon;

	public function __construct() {

		if ( is_admin() ) {

			// add Propeller icon
			$this->icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjIuMSwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zOnNrZXRjaD0iaHR0cDovL3d3dy5ib2hlbWlhbmNvZGluZy5jb20vc2tldGNoL25zIgoJIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgMzggMzgiCgkgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMzggMzg7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4KCS5zdDB7ZmlsbDojMjcyNzI1O30KPC9zdHlsZT4KPHRpdGxlPlNsaWNlIDE8L3RpdGxlPgo8ZGVzYz5DcmVhdGVkIHdpdGggU2tldGNoLjwvZGVzYz4KPGcgaWQ9IlBhZ2UtMV8xXyIgc2tldGNoOnR5cGU9Ik1TUGFnZSI+Cgk8ZyBpZD0icHJvcGVsbGVyIiBza2V0Y2g6dHlwZT0iTVNMYXllckdyb3VwIj4KCQk8ZyBpZD0iUGFnZS0xIiBza2V0Y2g6dHlwZT0iTVNTaGFwZUdyb3VwIj4KCQkJPGcgaWQ9IlByb3BlbGxlciI+CgkJCQk8cGF0aCBpZD0iU2hhcGUiIGNsYXNzPSJzdDAiIGQ9Ik0yMi42LDIuOGwtMy4zLDBsMCwwaC00LjFMOSwzNi4zbDAsMEg5bDAsMGMzLjIsMCw2LjMtMi41LDYuOS01LjdsMCwwbDAuMS0wLjhoMGwwLjItMS4ybDMsMAoJCQkJCWMzLjIsMCw2LjUtMi4zLDctNS4zbDAsMGwyLjUtMTMuNWwwLDBjMC4xLTAuNCwwLjEtMC45LDAuMS0wLjlDMjguOSw1LjUsMjYsMi44LDIyLjYsMi44TDIyLjYsMi44TDIyLjYsMi44eiBNMjAuMiwyNC41CgkJCQkJTDIwLjIsMjQuNWMwLDAsMCwwLjItMC4xLDAuM2MtMC4xLDAuOC0wLjksMS4zLTEuNywxLjNoLTEuN2wzLjgtMjAuOWgxLjFjMC45LDAsMS43LDAuNywxLjcsMS42TDIwLjIsMjQuNUwyMC4yLDI0LjV6Ii8+CgkJCTwvZz4KCQk8L2c+Cgk8L2c+CjwvZz4KPC9zdmc+Cg==';

			// loading in styles and scripts
			add_action( 'admin_head', [ $this, 'enqueue_styles' ] );
			add_action( 'admin_head', [ $this, 'enqueue_scripts' ] );

			// registering thumbnail support
			add_theme_support( 'post-thumbnails' );
			add_image_size( 'admin_thumb', 60, 60 );

			// adding featured image column to instagram post type
			add_filter( 'manage_instagram_posts_columns', [ $this, 'add_img_column' ] );
			add_filter( 'manage_instagram_posts_custom_column', [ $this, 'manage_img_column' ], 10, 2 );

			// adds menu to sidebar
			add_action( 'admin_menu', [ $this, 'create_admin_menu' ] );

			// creates instagram post type if it does not exist
			if ( ! post_type_exists( 'instagram' ) ) {
				add_action( 'init', [ $this, 'create_post_type' ] );
			}

		}

	}

	// load styles
	function enqueue_styles() {
		wp_enqueue_style( 'prop-instagram-generator', plugin_dir_url( __FILE__ ) . 'style.css', array(), '1.0', 'all' );
	}

	// loads scripts
	function enqueue_scripts() {
		wp_enqueue_script( 'prop-instagram-generator', plugin_dir_url( __FILE__ ) . 'script.js', array(), '1.0', 'all' );
	}


	// add image column to posts
	public function add_img_column( $columns ) {
		$columns['img'] = 'Featured Image';

		return $columns;
	}

	// populate new column with featured image
	public function manage_img_column( $column_name, $post_id ) {
		if ( $column_name == 'img' ) {
			echo the_post_thumbnail( 'admin_thumb' );

			return get_the_post_thumbnail( $post_id, 'thumbnail' );
		}
	}

	// adds menu to sidebar
	public function create_admin_menu() {
		add_menu_page(
			'Instagram Generator',
			'InstaProp',
			'administrator',
			'instagram-generator',
			[ $this, 'render_generator_page' ],
			$this->icon, 2
		);
	}

	// create post apply args
	public function create_post_type() {

		register_post_type( 'instagram',

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
				'rewrite'               => array( 'slug' => 'instagram' ),
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

				// Which Taxonomies will be applicable?
				'taxonomies'            => array(
					'instagram'
				),

			)

		);

	}

	// now everything is set up render the form page
	public function render_generator_page() {
		new Render;
	}


}
