<?php

namespace HfH;

use WP_Query;

/**
 * Plugin Name:       HfH Slider
 * Description:       Block that displays a slider with random posts of a certain category.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Matthias Nötzli
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       hfh-slider
 *
 * @package           hfh-slider
 */

class Slider
{
	static $instance = false;

	public static function getInstance()
	{
		if (!self::$instance)
			self::$instance = new self;
		return self::$instance;
	}

	private function __construct()
	{
		add_action('init', array($this, 'init'));
		add_filter('the_posts', array($this, 'query_filter'), 10, 2);
	}

	public function init()
	{
		register_taxonomy_for_object_type('category', 'page');
		add_post_type_support('page', 'excerpt');
		register_block_type(__DIR__ . '/build', array(
			'render_callback' => array($this, 'render_callback'),
			'attributes' => array(
				'categoryId' => array(
					'type' => 'integer'
				),
				'numberOfSlides' => array(
					'type' => 'integer',
					'default' => 6
				)
			)
		));
	}

	public function render_callback($block_attributes, $content)
	{
		$this->enqueue_styles();
		$this->enqueue_scripts();
		$category_id = $block_attributes['categoryId'];
		$alignwide = $block_attributes['align'] === "wide";
		$number_of_slides = $block_attributes['numberOfSlides'];
		$query = new WP_Query(array(
			'post_type' => 'page',
			'posts_per_page' => -1,
			'cat' =>  $category_id,
			'hfh_shuffle_and_pick' => $number_of_slides
		));
		if ($query->have_posts()) :
			ob_start();

?>
			<div class="hfh-slider splide <?php if ($alignwide) : ?>alignwide<?php endif; ?>">
				<div class="splide__track">
					<ul class="splide__list">
						<!-- Slides -->
						<?php
						while ($query->have_posts()) :
							$query->the_post();
						?>
							<div class="hfh-slider__slide splide__slide">
								<div class="hfh-slider__text-wrapper">
									<div class="hfh-slider__text">
										<div class="hfh-slider__title"><?php the_title() ?></div>
										<div class="hfh-slider__excerpt"><?php the_excerpt() ?></div>
										<a class="hfh-slider__link" href="<?php the_permalink(); ?>">Mehr erfahren</a>
									</div>
								</div>
								<div class="hfh-slider__image">
									<?php if (has_post_thumbnail()) : ?>
										<?php the_post_thumbnail(); ?>
									<?php endif; ?>
								</div>
							</div>
						<?php
						endwhile;
						?>
					</ul>
				</div>
			</div>
<?php
			$output = ob_get_clean();
			wp_reset_postdata();
		endif;
		return $output;
	}

	private function enqueue_styles()
	{
		wp_enqueue_style('splide', "https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.12/dist/css/splide-core.min.css");
	}

	private function enqueue_scripts()
	{
		wp_enqueue_script('splide', "https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.12/dist/js/splide.min.js");
		wp_enqueue_script('hfh-slider', plugin_dir_url(__FILE__) . 'js/hfh-slider.js');
	}

	/**
	 * Picks n random posts from query result.
	 */
	public function query_filter($posts, WP_Query $query)
	{
		if ($pick = $query->get('hfh_shuffle_and_pick')) {
			shuffle($posts);
			$posts = array_slice($posts, 0, (int) $pick);
		}
		return $posts;
	}
}

Slider::getInstance();
