<?php

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

