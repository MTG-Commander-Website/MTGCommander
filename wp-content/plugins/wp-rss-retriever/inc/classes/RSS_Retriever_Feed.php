<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class RSS_Retriever_Feed {
	private $feed_items 	= array(); // each feed post item
	private $settings 		= array(); // shortcode attributes
	private $transient_key 	= ''; // used as a key to check cached feeds
	private $is_cached 		= false;

	function __construct($data) {
		if(!is_array($data)) {
			wp_rss_retriever_error("Unable to construct " . get_class($this) . " with variable type: " . gettype($data));
		} else if (count($data) > 0) {
			// generate an encrypted key with the settings array
			$this->set_transient_key($data);

			if (!$this->get_cached_feed()) {
				foreach ($data as $name => $value) {
					$this->settings[$name] = $value;
				}
				if ($this->settings['ajax'] === 'false') {
					$this->validate_settings();
					$this->retrieve_feed();
				}
			}
		} else {
			wp_rss_retriever_error("Not enough data.");
		}
	}

	private function retrieve_feed() {
		// fetch the feed
		$rss = new SimplePie();
		$rss->set_feed_url($this->settings['url']);	
		$rss->set_useragent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36');	
		$rss->force_feed(true);
		$rss->enable_cache(false);
		$rss->enable_order_by_date(false);
		$rss->init();
    	$rss->set_output_encoding( get_option( 'blog_charset' ) );
		$rss->handle_content_type();

    	if (!$rss || !is_wp_error( $rss )) {
    		// if the feed doesn't work, try the WP built-in method fetch_feed
    		if (@$rss->get_item_quantity( $this->settings['items'] ) == 0) {
    			// reset the built-in cache to 1 second as we'll be using our own caching
    			add_filter( 'wp_feed_cache_transient_lifetime' , array(__CLASS__, 'return_1') );
    			$rss = fetch_feed($this->settings['url']);
    			remove_filter( 'wp_feed_cache_transient_lifetime' , array(__CLASS__, 'return_1') );
    		}
    		// use original feed order
    		if ($this->settings['orderby'] === 'date' || $this->settings['orderby'] === 'date_reverse') {
                $rss->enable_order_by_date(true);
            }
            // suppress "non-numeric value warning for Date"
		    if (!is_wp_error( $rss ) && !@$rss->get_item_quantity( $this->settings['items'] ) == 0) {
		    	$rss_items = $rss->get_items( 0, $this->settings['items'] );
		    	$rss_items = $this->orderby_sort($rss_items);

		    	foreach($rss_items as $item) {
		    		$item_object = new RSS_Retriever_Feed_Item($item, $this->settings);
		    		$this->feed_items[] = $item_object;
		    	}

		    	$this->set_cached_feed();
		    } else {
		    	// include the rss error message from the object if it exists
		    	if (isset($rss->error[0])) {
		    		$more_details = ' More details: ' . $rss->error[0];
		    	} else {
		    		$more_details = '';
		    	}
		    	wp_rss_retriever_error('No RSS items found with URL: <strong>' . implode(',', $this->settings['url']) . '</strong>.' . $more_details);
		    }
		} else {
			wp_rss_retriever_error('Unable to fetch RSS feed with URL: <strong>' . implode(',', $this->settings['url']) . '</strong>.');
		}
	}

	public function return_1() {
		return 1;
	}

	private function set_transient_key($data) {
		// always reset ajax setting to false, this way the data is cached regardless of the request method
		$data['ajax'] = 'false';
		// set the transient to an encrypted key (ex. rss_retriever_feed_96f040501c24f0cbe83a95ec2b148b62)
		$this->transient_key = 'rss_retriever_feed_' . md5(json_encode($data));
	}

	private function get_cached_feed() {
		// check if a cached version of the feed exists
		if ($cache = get_transient($this->transient_key)) {
			$this->feed_items = $cache->feed_items;
			$this->settings = $cache->settings;
			$this->is_cached = true;
			return true;
		} else {
			return false;
		}
	}

	private function set_cached_feed() {
		// store the feed into transient cache
		set_transient( $this->transient_key, $value = $this, $expires = $this->settings['cache'] );
	}

	public function display_feed() {
		if (!$this->settings['ajax'] || $this->is_cached) :
	        $output = '<div' . $this->get_wrapper_classes() . '>';
	            $output .= '<ul class="wp_rss_retriever_list">';
	            	foreach($this->feed_items as $item) {
	                    $output .= '<li' . $this->get_item_inline_css() . ' class="wp_rss_retriever_item">';
		                    $output .= '<div class="wp_rss_retriever_item_wrapper">';
		                    	$default_layout = array(
		                    		'title', 
		                    		'thumbnail', 
		                    		'content', 
		                    		'postdata'
		                    	);
		                    	$layout = apply_filters( 'wp_rss_retriever_layout',  $default_layout);

		                    	// set the default layout values (title, thumbnail, content, postdata)
		                    	foreach($default_layout as $default_layout_item => $value) {
		                    		if (false !== $key = array_search($value, $layout)) {
		                    			$func_name = 'get_' . $value;
		                    			if (method_exists($this, $func_name)) {
			                    			$layout[$key] = $this->$func_name($item);
			                    		}
		                    		}
		                    	}
		                    	// add layout items to the output
		                    	for ($i=0; $i < count($layout); $i++) { 
		                    		$output .= $layout[$i];
		                    	}
		            		$output .= '</div>';
	            		$output .= '</li>';
	            	}
	            $output .= '</ul>';
	        $output .= '</div>';
	    else :
	    	$id = 'rss' . substr(str_shuffle(MD5(microtime())), 0, 10);
	    	$loading_gif = WP_RSS_RETRIEVER_PLUGIN_URL . 'inc/imgs/ajax-loader.gif';

	    	$output  = '<div class="wprss_ajax" data-id="' . $id . '">';
	    		$output .= '<img src="' . $loading_gif . '" alt="Loading RSS Feed" width="16" height="16">';
	    	$output .= '</div>';
	    	wp_localize_script( 'rss-retriever-ajax', $id, $this->settings );

	    	wp_enqueue_script('rss-retriever-ajax');
	    endif;

		return $output;
	}

	private function orderby_sort($items) {
		if ($this->settings['orderby'] == 'date_reverse') {
		    $items = array_reverse($items);
		}

		if ($this->settings['orderby'] == 'random') {
		    shuffle($items);
		}

		return $items;
	}

	private function get_title($item) {
		if ($this->settings['title']) {
		    $output = '<a class="wp_rss_retriever_title"' . $this->get_link_target() . ' href="' . esc_url($item->permalink) . '"' .
		        $this->get_link_dofollow() .
		        'title="' . $item->title . '">';
		        $output .= apply_filters( 'wp_rss_retriever_title', $item->title);
		    $output .= '</a>'; 

		    return $output;  
		} else {
			return null;
		}
	}

	private function get_thumbnail($item) {
		if (property_exists($item, 'thumbnail') && $this->settings['thumbnail'] && strlen($item->thumbnail) > 0) {
			$output = '<a class="wp_rss_retriever_image"' . $this->get_thumbnail_inline_styles() . $this->get_link_target()  . ' href="' . esc_url($item->permalink) . '">';
				$output .= '<img class="portrait" src="' . esc_attr($item->thumbnail) . '" alt="' . esc_attr($item->title) . '" onerror="this.parentNode.style.display=\'none\'"/>';
			$output .= '</a>'; 
		} else {
			$output = null;
		}

		return $output;
	}

	private function get_content($item) {
		if ($this->settings['excerpt']) {
			$output = '<div class="wp_rss_retriever_container">';
			    $output .= $item->content;

			    // read more link
			    if($this->settings['read_more']) {
			        $output .= ' <a class="wp_rss_retriever_readmore"' . $this->get_link_target() . 
			        	' href="' . esc_url($item->permalink) . '"' .
			            $this->get_link_dofollow() .
			            'title="' . esc_attr($item->title) . '">';
			            	$output .= __( 'Read more', 'wp-rss-retriever' ) . '&nbsp;&raquo;';
			        $output .= '</a>';
			    }
			$output .= '</div>';
		} else {
			$output = null;
		}

		return $output;
	}

	private function get_postdata($item) {
		if ($this->settings['source'] || $this->settings['date']) {
		    $output = '<div class="wp_rss_retriever_metadata' .'">';
		        // source
		        if ($this->settings['source'] && $item->source) {
		            $label = __( 'Source', 'wp-rss-retriever' ) . ': ';
		            $output .= '<span class="wp_rss_retriever_source">' . $label . '<span>' . esc_html($item->source) . '</span></span>';
		        }
		        // separator
		        if ($this->settings['source'] && $this->settings['date']) {
		            $output .= ' | ';
		        }
		        // date
		        if ($this->settings['date'] && $item->date) {
		            $label = __( 'Published', 'wp-rss-retriever' ) . ': ';
		            $output .= '<span class="wp_rss_retriever_date">' . $label . '<span>' . esc_html($item->date) . '</span></span>';
		        }
		    $output .= '</div>';
		} else {
			$output = null;
		}

		return $output;
	}

	private function get_thumbnail_inline_styles() {
	    if ($this->settings['thumbnail']){
	        $output = ' style="width:' . esc_attr($this->settings['thumbnail']['width']) . '; height:' . esc_attr($this->settings['thumbnail']['height']) . ';"';
	    } else {
	        $output = '';
	    }
	    return $output;
	}

	private function get_link_target() {
		if ($this->settings['new_window']) {
			return ' target="_blank"';
		} else {
			return null;
		}
	}

	private function get_link_dofollow() {
		if ($this->settings['dofollow']) {
			return ' rel="nofollow"';
		} else {
			return null;
		}
	}

	private function get_wrapper_classes() {
		$layout_class_name = '';
		$output = ' class="wp_rss_retriever' . $layout_class_name . '"';
		
		return $output;
	}

	private function get_item_inline_css() {
		$output = '';

		return $output;	
	}

	/**
	 * Checks each shortcode attribute for validation and displays
	 * errors if the input is invalid. Converts each attribute into 
	 * an appropriate data type (ie. boolean, array, integer, etc.)
	 */
	private function validate_settings() {
		// convert open/closing double quotes
		foreach($this->settings as $attr => $value) {
			$remove_characters = array('“', '”', '‘', '’', '″');
			$value = str_replace($remove_characters, '', $value);
			$this->settings[$attr] = $value;
		}

		// Setting: URL
		// convert comma separated urls into array
		if( strpos($this->settings['url'], ',') !== false ) {
			$urls = explode(',', $this->settings['url']);
			foreach($urls as $url) {
				$this->validate_url($url);
			}
			$this->settings['url'] = $urls;
		} else {
			$this->validate_url($this->settings['url']);
			// always use an array
			$this->settings['url'] = array($this->settings['url']);
		}

		// Setting: ITEMS
		if (!is_numeric($this->settings['items']) || intval($this->settings['items']) < 1) { 
			$this->validation_error('items');
		} else {
			$this->settings['items'] = intval($this->settings['items']);
		}

		// Setting: ORDERBY
		$acceptable_values = array(
			'default',
			'date',
			'date_reverse',
			'random'
		);
		if (!in_array($this->settings['orderby'], $acceptable_values)) {
			$this->validation_error('orderby');
		}

		// Setting: TITLE
		$this->validate_str_bool_setting('title');

		// Setting: EXCERPT
		if (!is_numeric($this->settings['excerpt']) && $this->settings['excerpt'] !== 'none') {
			$this->validation_error('excerpt');
		} else {
			if (is_numeric($this->settings['excerpt'])) {
				// convert to integer
				$this->settings['excerpt'] = intval($this->settings['excerpt']);
			} else {
				$this->settings['excerpt'] = false;
			}
		}

		// Setting: READ_MORE
		$this->validate_str_bool_setting('read_more');

		// Setting: NEW_WINDOW
		$this->validate_str_bool_setting('new_window');

		// Setting: THUMBNAIL
		// always remove 'px' from the input
		$this->settings['thumbnail'] = str_replace('px', '', $this->settings['thumbnail']);
		if (!$this->is_str_bool($this->settings['thumbnail']) && 
			!is_numeric(str_replace(array('x', '%'), '', $this->settings['thumbnail']))) {
			$this->validation_error('thumbnail');
		} else {
			// "true" or "false"
			if ($this->is_str_bool($this->settings['thumbnail'])) {
				$this->validate_str_bool_setting('thumbnail');

				if ($this->settings['thumbnail'] === true) {
					$this->settings['thumbnail'] = array (
						'width' => '150px',
						'height' => '150px'
					);
				}
			} else {
				// setting has an x (ie 100x200)
				if (strpos($this->settings['thumbnail'], 'x') !== false) {
					$size = explode('x', $this->settings['thumbnail']);
					// make sure both values exist (x99, 99x will fail)
					if (count($size) > 1 && strlen($size[0]) > 0 && strlen($size[1]) > 0) {
						$width = $size[0];
						$height = $size[1];
					} else {
						$this->validation_error('thumbnail');
					}
				} else {
					$width = $this->settings['thumbnail'];
					$height = $this->settings['thumbnail'];
				}

				// add 'px' if '%' is missing
				$width 	= (strpos($width, '%')) ? $width : $width . 'px';
				$height = (strpos($height, '%')) ? $height : $height . 'px';

				$this->settings['thumbnail'] = array (
					'width' => $width,
					'height' => $height
				);
			}
		}

		// Setting: SOURCE
		$this->validate_str_bool_setting('source');

		// Setting: DATE
		$this->validate_str_bool_setting('date');

		// Setting: CACHE
		if (!is_numeric(strtotime($this->settings['cache'], 0)) && !is_numeric($this->settings['cache'])) {
			$this->validation_error('cache');
		} else {
			// convert the cache to seconds if it is a string (ie. 1 hour, 1 day, etc.)
			if (!is_numeric($this->settings['cache'])) {
				$this->settings['cache'] = strtotime($this->settings['cache'], 0);
			}

			if ($this->settings['cache'] === 0) {
				// Do not allow 0 seconds, this will create a cached feed that never expires
				$this->settings['cache'] = -1;
			}
		}

		// Setting: DOFOLLOW
		$this->validate_str_bool_setting('dofollow');

		// Setting: CREDITS
		$this->validate_str_bool_setting('credits');

		// Setting: AJAX
		$this->validate_str_bool_setting('ajax');

	}

	private function validate_str_bool_setting($setting) {
		if (!$this->is_str_bool($this->settings[$setting])) {
			// not 'true' or 'false'
			$this->validation_error($setting);
		} else {
			// convert the value to an actual boolean
			$this->settings[$setting] = ($this->settings[$setting] === 'true') ? true : false;
		}
	}

	private function is_str_bool($value) {
		if ($value !== 'true' && $value !== 'false') {
			return false;
		} else {
			return true;
		}
	}

	private function validate_url($url) {
		$url = filter_var($url, FILTER_SANITIZE_URL);
		if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
			return true;
		} else {
			$this->validation_error('url');
		}
	}

	private function validation_error($setting) {
		wp_rss_retriever_error('Invalid <strong>' . $setting . '</strong> value: <strong>' . $this->settings[$setting] . '</strong>. Please check your shortcode.');
	}
}