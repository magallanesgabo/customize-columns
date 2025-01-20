<?php
/**
 * Practical example for the CPT 'page'.
 *
 * In this example, we add a new column 'post_id' simply by defining it with that key
 * and placing it in the desired position, in this case #2, right below the 'cb' column.
 * The columns are configured in the order they will appear in the admin list.
 */

$page_columns = array(
    'cb' => array(
        'label' => '<input type="checkbox" />',
    ),
    // My Custom POST ID Column
    'post_id' => array(     // This is the key of the new column 'post_id'
        'label' => 'ID',
        'content_callback' => function ($post_id) {
            echo $post_id;
        },
        'sortable' => true,
    ),
    'title' => array(
        'label' => 'Title',
        'content_callback' => function ($post_id) {
            echo get_the_title($post_id);
            // Example: Retrieve a custom field value or any additional data
            // echo get_post_meta($post_id, 'field_name', true);
            // echo get_field('field_name', $post_id);
        },
    ),
    'author' => array(
        'label' => 'Author',
        'content_callback' => function ($post_id) {
            $author_id = get_post_field('post_author', $post_id);
            $author_name = get_the_author_meta('display_name', $author_id);
            echo $author_name;
        },
    ),
    'date' => array(
        'label' => 'Publication Date',
        // If you don't want to modify the content for the column, you don't need to use a callback
    ),
);

// Call the function with the CPT / taxonomy name and the array of columns
customize_columns('page', $page_columns);
