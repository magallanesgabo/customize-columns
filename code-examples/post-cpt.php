<?php
/**
 * Practical example for the CPT 'post'.
 *
 * In this example, we customize the columns for the 'post' post type by adding
 * new columns such as 'Post ID', 'Featured Image', 'Online', and 'Special Action'.
 * Each column is configured with specific functionality, such as displaying metadata,
 * custom content, or providing actions. The default WordPress columns are also modified.
 */

$post_columns = array(
    'cb' => array(
        'label' => '<input type="checkbox" />', 
    ),
    // Custom Column: Post ID
    'post_id' => array(
        'label' => 'Post ID',
        'content_callback' => function ($post_id) {
            echo $post_id;
        },
        'sortable' => true, // Makes the column sortable
    ),
    // Default Column: Title
    'title' => array(
        'label' => 'Title',
        'content_callback' => function ($post_id) {
            echo get_the_title($post_id);
            // Example: Uncomment below to fetch and display custom metadata
            // echo get_post_meta($post_id, 'custom_field_name', true);
            // echo get_field('custom_field_name', $post_id);
        },
    ),
    'author' => array(
        'label' => 'Author',
        'content_callback' => function ($post_id) {
            $author_id = get_post_field('post_author', $post_id); 
            $author_name = get_the_author_meta('display_name', $author_id); // Gets the author's name
            echo $author_name;
        },
    ),
    // Custom Column: Categories
    'categories' => array(
        'label' => 'Categories', 
        'content_callback' => function ($post_id) {
            $categories = get_the_category($post_id); 
            $categories_names = array_map(function ($category) {
                return $category->name;
            }, $categories);
            echo implode(', ', $categories_names); // Displays a comma-separated list of categories
        },
    ),
    // Custom Column: Featured Image
    'post_image' => array(
        'label' => 'Featured Image',
        'content_callback' => function ($post_id) {
            echo '<div style="display: flex; justify-content: center; align-items: center; width: 70px; height: 70px; overflow: hidden;">';
            echo get_the_post_thumbnail($post_id, 'thumbnail'); // Displays the thumbnail image
            echo '</div>';
        },
    ),
    // Custom Column: Online Status
    'online' => array(
        'label' => 'Online',
        'content_callback' => function ($post_id) {
            $post_status = get_post_status($post_id);
            echo '<input type="checkbox" disabled ' . ($post_status === 'publish' ? 'checked' : '') . '>'; 
        },
    ),
    // Custom Column: Special Action
    'extra' => array(
        'label' => 'Special Action',
        'content_callback' => function ($post_id) {
            $priority = get_post_meta($post_id, '_custom_priority_field', true);
            if ($priority === 'high' && get_post_status($post_id) === 'publish') {
                $action_url = home_url('/special-action/' . $post_id);
                echo '<a href="' . esc_url($action_url) . '" class="button" target="_blank">View Special</a>'; // Displays a button
            }
        },
    ),
    // Default Column: Date
    'date' => array(
        'label' => 'Publication Date', 
        // Default functionality: No callback needed unless modifications are required
    ),
);

// Call the function with the CPT / Taxonomy name and the array of columns
customize_columns('post', $post_columns);
