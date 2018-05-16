<?php

class Render {

	public function __construct() {

		?>

		<section class="wrap">

			<h1>Propeller Instagram Generator</h1>
			<?php // var_dump( $_POST ); ?>

			<?php // search based on username gives the option to fetch a users posts ?>
			<?php if ( isset( $_POST['searchinput'] ) && $_POST['searchtype'] == 'users' && isset( $_POST['checktype'] ) && $_POST['checktype'] == 'users' ): ?>
				<?php $instagramUsers = new InstagramFetch; ?>
				<?php if ( isset( $instagramUsers->result ) ): ?>
					<h4>Please select an account to scrape images from</h4>
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
							<?php // format returned user results into table ?>
							<?php foreach ( $instagramUsers->result->data as $scraped_user ): ?>

								<tr>
									<td data-colname="Author">
										<img src="<?= $scraped_user->profile_picture; ?>"
										     class="ig_generator__userimg"
										     alt="<?= $scraped_user->username; ?>"
										     width="50"/>
										<?= $scraped_user->username; ?>
									</td>
									<td>
										<button type="submit" name="searchinput" id="submit"
										        value="<?= $scraped_user->id; ?>"
										        class="button button-primary">Fetch
										</button>
									</td>
								</tr>

							<?php endforeach; ?>
							</tbody>
						</table>
						<input type="hidden" name="checktype" value="user_id">
						<input type="hidden" name="searchtype" value="users">
					</form>
				<? endif; ?>

			<?php else: ?>
				<? // Start of the form choose which data to search for ?>
				<? if ( empty( $_POST['scraped_results'] ) && empty( $_POST['searchinput'] ) ): ?>

					<h4>Please select a search type</h4>


					<form method="post" action="">
						<ul class="list">
							<li>
								<input class="js-searchtype" type="radio" name="searchtype" value="owner">
								<strong>Owner</strong> will fetch posts from the owner of this account
							</li>
							<li>
								<input class="js-searchtype" type="radio" name="searchtype" value="users">
								<strong>Username</strong> will take a username and search for it
							</li>
							<li>
								<input class="js-searchtype" type="radio" name="searchtype" value="tags">
								<strong>Hashtag</strong> will fetch recent images based on hashtag
							</li>
							<li>
								<input class="js-searchtype" type="radio" name="searchtype" value="locations">
								<strong>Location</strong> will take a location ID number, you may need to search in the URL on <a
										href="https://www.instagram.com/explore/locations/227846917/rugby-warwickshire/"
										target="_blank">Instagram for
									it</a>
							</li>
						</ul>

						<table class="form-table">
							<tbody>
							<tr>
								<th scope="row"><label for="searchinput">Search Input</label></th>
								<td>
									<?php // set some limits for the input box to save js validation ?>
									<input id="searchinput"
									       value=""
									       class="regular-text"
									       pattern="^[A-Za-z0-9_]{1,32}$"
									       type="text"
									       name="searchinput"
									       placeholder="no need to specify @ or # symbols"
									       autocomplete="on">
								</td>
							</tr>
							</tbody>
						</table>
						<input class="js-checktype" type="hidden" name="checktype" value="users">
						<input type="submit" name="scraped_results" id="submit" value="Scrape"
						       class="button button-primary">
					</form>

				<? endif; ?>

			<?php endif; ?>

			<?php // error response ?>
			<?php if ( empty( $_POST['chosen_posts'] ) && isset( $_POST['searchtype'] ) && isset( $_POST['searchinput'] ) && isset( $_POST['checktype'] ) && $_POST['checktype'] != 'users' ) : ?>

				<?php $instagram = new InstagramFetch; ?>

				<?php if ( $instagram->error ) : ?>
					<h3 class="error">ERROR</h3>
					<h4>Sorry, InstaProp failed to fetch images</h4>
					<?php $error_msg = $instagram->error->getMessage();
					//var_dump($error_msg);
					if ( $error_msg == 'you cannot view this resource' ): ?>
						<p>This is usually due to trying to fetch a private users content</p>
					<? else: ?>
						<p><?= $error_msg ?></p>
					<? endif; ?>
					<a class="button button-primary"
					   href="<?= admin_url( 'admin.php?page=instagram-generator' ) ?>">Start Again</a>
				<? elseif($instagram->result == null): ?>
					<h3 class="error">Oops!</h3>
					<h4>Sorry, no images were found. Please try again.</h4>
					<a class="button button-primary"
					   href="<?= admin_url( 'admin.php?page=instagram-generator' ) ?>">Start Again</a>
				<? else: ?>
				<?php // render images ready for selection after fetch ?>
					<h3>Instagram</h3>
					<h4>Select which images you wish to import</h4>
					<p>Select images from a single page then click generate</p>
					<form name="get_images" method="post" action="" enctype="multipart/form-data">

						<ul class="ig_generator">
							<?php foreach ( $instagram->result->data as $photo ): ?>

								<li>
									<input class="js-checkbox" type="checkbox" id="<?php echo $photo->id ?>"
									       name="scraped_id[]"
									       value="<?php echo $photo->id ?>"/>

									<label for="<?php echo $photo->id ?>">
										<img src="<?php echo $photo->images->low_resolution->url ?>" width="125"
										     height"125"/>
									</label>
								</li>

							<?php endforeach; ?>
						</ul>

						<div class="ig_generator">
							<?php // Generate the selected images ?>
							<p class="submit">
								<input type="hidden" name="searchtype" value="<?= $_POST['searchtype'] ?>"/>
								<input type="hidden" name="searchinput" value="<?= $_POST['searchinput'] ?>"/>
								<input type="hidden" name="checktype" value="<?= $_POST['checktype'] ?>">

								<?php //pass the next_max_id so it fetches correct page on post ?>
								<? if ( isset( $instagram->result->pagination->next_max_id ) ): ?>
									<? if ( isset( $_POST['next_max_id'] ) ): ?>
										<input type="hidden" name="next_max_id" value="<?= $_POST['next_max_id'] ?>"/>
									<? endif; ?>
								<? endif; ?>
								<input type="submit" name="chosen_posts" id="submit" value="Generate"
								       class="button button-primary">
							</p>

							<?php // render next page button if there are more than 30 posts ?>
							<? if ( isset( $instagram->result->pagination->next_max_id ) ): ?>
								<? if ( isset( $_POST['next_max_id'] ) ): ?>
									<input type="hidden" name="max_id" value="<?= $_POST['next_max_id'] ?>"/>
									<input type="hidden" name="searchtype" value="<?= $_POST['searchtype'] ?>"/>
									<input type="hidden" name="searchinput" value="<?= $_POST['searchinput'] ?>"/>
								<? endif; ?>
								<button type="submit" name="next_max_id" id="submit"
								        value="<?= $instagram->result->pagination->next_max_id; ?>"
								        class="js-next button button-primary  float--right">Next Page
								</button>
							<? endif; ?>

						</div>
					</form>


				<?php endif; ?>
			<?php endif; ?>

			<? if ( ! empty( $_POST['scraped_id'] ) ): ?>
				<?php // send the selected images to the post generator function ?>
				<?php $instagram = new InstagramFetch; ?>
				<?php $post_generator = new PostGenerator(); ?>

				<?php foreach ( $instagram->result->data as $unmatched_id ): ?>

					<? if ( in_array( $unmatched_id->id, $_POST['scraped_id'] ) ): ?>
						<?php $post_generator::populate_posts( $unmatched_id ); ?>
					<? endif; ?>

				<? endforeach; ?>
				<?php // success message ?>
				<p class="success">Generator Task Completed Successfully!</p>
				<a class="button button-primary" href="<?= admin_url( 'edit.php?post_type=instagram' ) ?>">See
					Generated Links</a>
			<? endif; ?>

		</section>
		<?php

	}

}
