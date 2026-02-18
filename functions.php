<?php
/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

// Adds theme support for post formats.
if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

// Enqueues editor-style.css in the editors.
if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
	/**
	 * Enqueues editor-style.css in the editors.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_editor_style() {
		add_editor_style( 'assets/css/editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

// Enqueues style.css on the front.
if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
	/**
	 * Enqueues style.css on the front.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_styles() {
		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( 'style.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

// Registers custom block styles.
if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfive' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_block_styles' );

// Registers pattern categories.
if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'twentytwentyfive' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'twentytwentyfive' ),
				'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

// Registers block binding sources.
if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_block_bindings() {
		register_block_bindings_source(
			'twentytwentyfive/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
				'get_value_callback' => 'twentytwentyfive_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

// Registers block binding callback function for the post format name.
if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function twentytwentyfive_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;






// НАЧАЛО

add_action('init', function() {
    register_post_type('event', [
        'labels' => [
            'name' => 'События',
            'singular_name' => 'Событие'
        ],
        'public' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'has_archive' => true,
        'rewrite' => ['slug' => 'events']
    ]);
});


add_action('init', function() {
    $fields = ['place', 'start_at', 'end_at', 'tags', 'capacity', 'status', 'popularity', 'change_number'];
    
    foreach ($fields as $field) {
        register_meta('post', $field, [
            'object_subtype' => 'event',       
            'type' => 'string',                
            'single' => true,                     
            'show_in_rest' => true,              
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
    }
});

// 
function kgn_calculate_popularity($post_id) {
    $start_at = get_post_meta($post_id, 'start_at', true);
    $capacity = (int)get_post_meta($post_id, 'capacity', true);
    $tags_json = get_post_meta($post_id, 'tags', true);
    
    error_log("Расчет popularity для поста #$post_id");
    error_log("start_at: " . ($start_at ?: 'пусто'));
    error_log("capacity: " . $capacity);
    error_log("tags_json: " . ($tags_json ?: 'пусто'));
    
    if (empty($start_at)) {
        error_log("Нет даты начала, возвращаем 3");
        return 3;
    }
    
    $today = new DateTime('today');
    $today->setTime(0, 0, 0);
    
    $start_date = null;
    
    $start_date = DateTime::createFromFormat('Y-m-d H:i', $start_at);
    
    if (!$start_date) {
        $start_date = new DateTime($start_at);
    }
    
    if (!$start_date) {
        error_log("Не удалось распарсить дату: " . $start_at);
        return 3;
    }
    
    $start_date->setTime(0, 0, 0); 
    
    $interval = $today->diff($start_date);
    $days_to_start = (int)$interval->format('%r%a');
    
    error_log("today: " . $today->format('Y-m-d'));
    error_log("start_date: " . $start_date->format('Y-m-d'));
    error_log("days_to_start: " . $days_to_start);
    
    $tags = json_decode($tags_json, true);
    $tag_count = is_array($tags) ? count($tags) : 0;
    error_log("tag_count: " . $tag_count);
    
    $raw = 3 + ($capacity / 1000) - ($days_to_start / 10) + $tag_count;
    error_log("raw (до округления): " . $raw);
    
    if ($raw < 1) {
        $result = 1;
    } elseif ($raw > 5) {
        $result = 5;
    } else {
        $result = round($raw);
    }
    
    error_log("popularity result: " . $result);
    
    return $result;
}

add_action('save_post_event', function($post_id, $post, $update) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (wp_is_post_revision($post_id)) {
        return;
    }
    
    error_log("=== Сохранение события #$post_id ===");
    
    $popularity = kgn_calculate_popularity($post_id);
    
    update_post_meta($post_id, 'popularity', $popularity);
    
    $change_number = (int)get_post_meta($post_id, 'change_number', true);
    update_post_meta($post_id, 'change_number', $change_number + 1);
    
    error_log("change_number: " . ($change_number + 1));
    error_log("=== Конец сохранения ===");
    
}, 10, 3);

//
add_action('rest_api_init', function() {
    
    register_rest_route('events/v1', '/events', [
        'methods' => 'GET',
        'callback' => 'kgn_get_events_callback',
        'permission_callback' => '__return_true'
    ]);
    
    register_rest_route('events/v1', '/events', [
        'methods' => 'POST',
        'callback' => 'kgn_create_event_callback',
        'permission_callback' => function() {
            return current_user_can('publish_posts');
        }
    ]);
    
    register_rest_route('events/v1', '/events/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'kgn_get_single_event_callback',
        'permission_callback' => '__return_true'
    ]);

	register_rest_route('events/v1', '/events/(?P<id>\d+)', [
		'methods' => 'PUT',
		'callback' => 'kgn_update_event_callback',
		'permission_callback' => function($request) {
			return current_user_can('edit_post', $request['id']);
		}
	]);

	register_rest_route('events/v1', '/events/(?P<id>\d+)', [
		'methods' => 'DELETE',
		'callback' => 'kgn_delete_event_callback',
		'permission_callback' => function($request) {
			return current_user_can('delete_post', $request['id']);
		}
	]);
    
});

//
function kgn_get_events_callback($request) {
    $per_page = 10;
    
    $all_events = get_posts([
        'post_type' => 'event',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ]);
    
    $events_data = [];
    foreach ($all_events as $event) {
        $place = get_post_meta($event->ID, 'place', true);
        $start_at = get_post_meta($event->ID, 'start_at', true);
        $status = get_post_meta($event->ID, 'status', true);
        $popularity = (int)get_post_meta($event->ID, 'popularity', true);
        
        if (!$popularity) {
            $popularity = kgn_calculate_popularity($event->ID);
            update_post_meta($event->ID, 'popularity', $popularity);
        }
        
        $events_data[] = [
            'id' => $event->ID,
            'title' => $event->post_title ?: 'Без названия',
            'place' => $place ?: '',
            'start_at' => $start_at ?: '',
            'status' => $status ?: 'draft',
            'popularity' => $popularity,
            'start_timestamp' => $start_at ? strtotime($start_at) : 0
        ];
    }
    
 
    usort($events_data, function($a, $b) {

        $date_diff = $a['start_timestamp'] - $b['start_timestamp'];
        if ($date_diff != 0) {
            return $date_diff;
        }
        

        if ($a['popularity'] != $b['popularity']) {
            return $b['popularity'] - $a['popularity'];
        }
        
       
        return $a['id'] - $b['id'];
    });
    

    foreach ($events_data as &$event) {
        unset($event['start_timestamp']);
    }
    

    return new WP_REST_Response([
        'data' => $events_data,
        'meta' => [
            'total' => count($events_data),
            'per_page' => $per_page
        ],
        'links' => [
            'self' => '/events/v1/events',
            'next' => null,
            'prev' => null
        ]
    ], 200);
}

//
function kgn_create_event_callback($request) {
    $params = $request->get_json_params();
    

    $required = ['title', 'place', 'start_at', 'end_at', 'capacity'];
    foreach ($required as $field) {
        if (empty($params[$field])) {
            return new WP_Error(
                'missing_field',
                "Поле '$field' обязательно",
                ['status' => 422]
            );
        }
    }
    

    $capacity = (int)$params['capacity'];
    if ($capacity < 1 || $capacity > 5000) {
        return new WP_Error(
            'invalid_capacity',
            'Capacity должно быть от 1 до 5000',
            ['status' => 422]
        );
    }
    

    $start = strtotime($params['start_at']);
    $end = strtotime($params['end_at']);
    
    if (!$start || !$end) {
        return new WP_Error(
            'invalid_date',
            'Неверный формат даты. Используйте YYYY-MM-DD HH:MM',
            ['status' => 422]
        );
    }
    
    if ($start >= $end) {
        return new WP_Error(
            'invalid_dates',
            'start_at должен быть раньше end_at',
            ['status' => 422]
        );
    }
    

    $tags = isset($params['tags']) ? $params['tags'] : [];
    if (count($tags) > 5) {
        return new WP_Error(
            'too_many_tags',
            'Не больше 5 тегов',
            ['status' => 422]
        );
    }
    

    $post_id = wp_insert_post([
        'post_title' => sanitize_text_field($params['title']),
        'post_type' => 'event',
        'post_status' => $params['status'] ?? 'draft',
        'post_content' => $params['description'] ?? ''
    ]);
    
    if (is_wp_error($post_id)) {
        return new WP_Error(
            'creation_failed',
            'Не удалось создать событие',
            ['status' => 500]
        );
    }
    

    update_post_meta($post_id, 'place', sanitize_text_field($params['place']));
    update_post_meta($post_id, 'start_at', $params['start_at']);
    update_post_meta($post_id, 'end_at', $params['end_at']);
    update_post_meta($post_id, 'tags', json_encode($tags));
    update_post_meta($post_id, 'capacity', $capacity);
    update_post_meta($post_id, 'status', $params['status'] ?? 'draft');
    update_post_meta($post_id, 'change_number', 1);
    

    $popularity = kgn_calculate_popularity($post_id);
    update_post_meta($post_id, 'popularity', $popularity);
    

    if ($popularity == 1) {
        wp_delete_post($post_id, true);
        return new WP_Error(
            'low_popularity',
            'Low popularity Not interesting Event',
            ['status' => 400]
        );
    }
    
    return new WP_REST_Response([
        'id' => $post_id,
        'title' => $params['title'],
        'place' => $params['place'],
        'start_at' => $params['start_at'],
        'end_at' => $params['end_at'],
        'tags' => $tags,
        'capacity' => $capacity,
        'status' => $params['status'] ?? 'draft',
        'popularity' => $popularity,
        'change_number' => 1,
        'link' => get_permalink($post_id)
    ], 201);
}

//
function kgn_get_single_event_callback($request) {
    $post_id = $request['id'];
    $post = get_post($post_id);
    
    if (!$post || $post->post_type !== 'event') {
        return new WP_Error(
            'not_found',
            'Событие не найдено',
            ['status' => 404]
        );
    }
    
    $place = get_post_meta($post_id, 'place', true);
    $start_at = get_post_meta($post_id, 'start_at', true);
    $end_at = get_post_meta($post_id, 'end_at', true);
    $tags = json_decode(get_post_meta($post_id, 'tags', true), true) ?: [];
    $capacity = (int)get_post_meta($post_id, 'capacity', true);
    $status = get_post_meta($post_id, 'status', true) ?: 'draft';
    $popularity = (int)get_post_meta($post_id, 'popularity', true);
    $change_number = (int)get_post_meta($post_id, 'change_number', true);
    
    $data = [
        'id' => $post_id,
        'title' => $post->post_title ?: 'Без названия',
        'place' => $place ?: '',
        'start_at' => $start_at ?: '',
        'end_at' => $end_at ?: '',
        'tags' => $tags,
        'capacity' => $capacity,
        'status' => $status,
        'popularity' => $popularity,
        'change_number' => $change_number,
    ];
    

    $today = (int)date('N');
    if ($today == 2 || $today == 3) {
        $data['recommendation'] = 'Рекомендуем по вторникам и средам';
    }
    
    return new WP_REST_Response($data, 200);
}

//
function kgn_update_event_callback($request) {
    $post_id = $request['id'];
    $post = get_post($post_id);
    
    if (!$post || $post->post_type !== 'event') {
        return new WP_Error('not_found', 'Событие не найдено', ['status' => 404]);
    }
    
    $params = $request->get_json_params();
    

    if (isset($params['start_at'])) {
        $start = strtotime($params['start_at']);
        $now = strtotime('now');
        if ($start < $now) {
            return new WP_Error(
                'past_date',
                'Нельзя изменить start_at на дату в прошлом',
                ['status' => 403]
            );
        }
    }
    

    $current_status = get_post_meta($post_id, 'status', true);
    if ($current_status === 'cancelled' && isset($params['status']) && $params['status'] === 'published') {
        return new WP_Error(
            'cancelled_event',
            'Нельзя отменить отменённое событие',
            ['status' => 403]
        );
    }
    

    if (isset($params['title'])) {
        wp_update_post([
            'ID' => $post_id,
            'post_title' => sanitize_text_field($params['title'])
        ]);
    }
    

    $updatable_fields = ['place', 'start_at', 'end_at', 'capacity', 'status'];
    foreach ($updatable_fields as $field) {
        if (isset($params[$field])) {
            update_post_meta($post_id, $field, $params[$field]);
        }
    }
    

    if (isset($params['tags'])) {
        if (count($params['tags']) > 5) {
            return new WP_Error('too_many_tags', 'Не больше 5 тегов', ['status' => 422]);
        }
        update_post_meta($post_id, 'tags', json_encode($params['tags']));
    }
    

    $popularity = kgn_calculate_popularity($post_id);
    update_post_meta($post_id, 'popularity', $popularity);
    

    $change_number = (int)get_post_meta($post_id, 'change_number', true);
    update_post_meta($post_id, 'change_number', $change_number + 1);
    

    return kgn_get_single_event_callback($request);
}

//
function kgn_delete_event_callback($request) {
    $post_id = $request['id'];
    $post = get_post($post_id);
    
    if (!$post || $post->post_type !== 'event') {
        return new WP_Error('not_found', 'Событие не найдено', ['status' => 404]);
    }
    

    $start_at = get_post_meta($post_id, 'start_at', true);
    $status = get_post_meta($post_id, 'status', true);
    
    $now = strtotime('now');
    $start = strtotime($start_at);
    

    if ($start <= $now) {
        return new WP_Error(
            'cannot_delete',
            'Нельзя удалить событие, которое уже началось',
            ['status' => 403]
        );
    }
    
    if ($status === 'published') {
        return new WP_Error(
            'cannot_delete',
            'Нельзя удалить опубликованное событие',
            ['status' => 403]
        );
    }
    
    $deleted = wp_delete_post($post_id, true);
    
    if (!$deleted) {
        return new WP_Error('delete_failed', 'Не удалось удалить событие', ['status' => 500]);
    }
    
    return new WP_REST_Response(null, 204); 
}


add_filter('determine_current_user', function($user) {
    if ($user !== 0) {
        return $user;
    }
    
    $auth_header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
    if (empty($auth_header) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $auth_header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }
    
    if (empty($auth_header)) {
        return $user;
    }
    
    if (strpos($auth_header, 'Basic ') === 0) {
        $token = substr($auth_header, 6);
        $decoded = base64_decode($token);
        if ($decoded) {
            list($username, $password) = explode(':', $decoded);
            $user = wp_authenticate($username, $password);
            if (!is_wp_error($user)) {
                return $user->ID;
            }
        }
    }
    
    return 0;
}, 10);