<?php

/**
 * Customize columns for a specific CPT or taxonomy.
 *
 * @param string $identifier       The post type or taxonomy identifier.
 * @param array  $columns   Configuration array for columns (label, order, visibility, width, etc.).
 */
function customize_columns($identifier, $columns) {
    static $did_render_column = []; 

    if (!isset($did_render_column[$identifier])) {
        $did_render_column[$identifier] = [];
    }

    /**
     * 1) Define and reorder columns.
     */
    add_filter("manage_edit-{$identifier}_columns", function ($existing_columns) use ($columns) {
        $final_columns = [];

        // Iterate through $columns to define column order and labels.
        foreach ($columns as $col_key => $info) {
            if (isset($info['visible']) && $info['visible'] === false) {
                continue;
            }

            $label = !empty($info['label']) ? $info['label'] : $col_key;
            $final_columns[$col_key] = $label;
        }

        foreach ($existing_columns as $key => $value) {
            if (!isset($final_columns[$key])) {
                $final_columns[$key] = $value;
            }
        }

        return $final_columns;
    }, 9999);

    /**
     * 2) Override column content using the manage_{$identifier}_posts_custom_column hook.
     */
    add_action("manage_{$identifier}_posts_custom_column", function ($col_key, $post_id) use ($columns, &$did_render_column, $identifier) {
        $did_render_column[$identifier][$col_key] = true;

        if (!empty($columns[$col_key]['content_callback']) && is_callable($columns[$col_key]['content_callback'])) {
            call_user_func($columns[$col_key]['content_callback'], $post_id);
        }
    }, 9999, 2);

    /**
     * 3) Fallback: Modify content for default WordPress columns using posts_results.
     */
    add_filter('posts_results', function ($posts, $query) use ($columns, &$did_render_column, $identifier) {
        if (!is_admin() || empty($query->query['post_type']) || $query->query['post_type'] !== $identifier) {
            return $posts;
        }

        // Define which default WordPress columns are supported for content overrides.
        $default_columns = [
            'title'     => 'post_title',
            'date'      => 'post_date',
            'author'    => 'post_author',
            'categories' => 'post_category',
            'tags'      => 'post_tag',
            'comments'  => 'comment_count',
            'status'    => 'post_status',
            'id'        => 'ID',
            'slug'      => 'post_name',
        ];

        foreach ($posts as $post) {
            foreach ($columns as $col_key => $info) {
                if (!empty($did_render_column[$identifier][$col_key])) {
                    continue;
                }

                if (empty($info['content_callback']) || !is_callable($info['content_callback'])) {
                    continue;
                }

                if (isset($default_columns[$col_key])) {
                    ob_start();
                    call_user_func($info['content_callback'], $post->ID);
                    $override_content = ob_get_clean();
                    $post->{$default_columns[$col_key]} = $override_content;
                }
            }
        }

        return $posts;
    }, 10, 2);

    /**
     * 4) Add custom styles for column widths.
     */
    add_action('admin_head', function () use ($identifier, $columns) {
        $screen = get_current_screen();
        if ($screen && $screen->id === "edit-{$identifier}") {
            echo '<style>';
            foreach ($columns as $col_key => $info) {
                if (!empty($info['width'])) {
                    echo ".column-" . esc_attr($col_key) . " { width: " . esc_attr($info['width']) . "; }";
                }
            }
            echo '</style>';
        }
    });
}