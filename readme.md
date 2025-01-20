# Documentation: `customize_columns()`

---

## **The Problem**

As a developer, you might need to customize or create columns for CPT (Custom Post Types) or taxonomy lists in WordPress. Using an external plugin can add unnecessary weight and complexity to your project. The `customize_columns()` function provides a simpler, faster, and reusable solution to manipulate WordPress columns efficiently.

This function simplifies the use of hooks like `manage_{$post_type}_posts_columns` and `manage_{$post->post_type}_posts_custom_column`, allowing you to define custom columns for displaying metadata or performing specific actions.

---

## **How to Use `customize_columns()`**

### **Step 1: Define Columns**

Create an associative array where each key represents the column ID, and the value is a subarray defining the column's characteristics.

#### Example:

```php
$postname_columns = (
    'post_id' => array(     // This is the key of the new column 'post_id'
        'label' => 'ID',
        'content_callback' => function ($post_id) {
            echo $post_id;
            # Example: Retrieve a custom field value or any additional data
            // echo get_post_meta($post_id, 'field_name', true);
            // echo get_field('field_name', $post_id);
        },
        'sortable' => true, // Make the column sortable
        'width' => '20px' // Adjust column width
        #'visible' => false, // Visible is true by default
    ),
);

```

---

### **Step 2: Call `customize_columns()`**

After defining the columns array, use the `customize_columns()` function. It takes two parameters:

1. `$identifier`: The slug of the CPT or taxonomy.
2. `$columns`: The columns array defined in Step 1.

#### Example:

```php
customize_columns('cpt-name', $columns);
```

---

### **Step 3: Complete Example**

This is the final example of how it would actually be used where you are modifying the columns for the CPT page, and you are adding a new column with the key or column id 'post_id' and you are painting the post id and adding the rest of the default columns if you want to control the order of all the columns.

```php
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
```

---
---

### **Step 4: Verify Changes**

Ensure that the columns appear in the admin list for the specified CPT or taxonomy. If you used `visible => false` or if the column is managed by another plugin, check that the applied modifications (order, labels, width) are as expected.

---

## **Important Notes**

- If a column is already modified by other hooks or plugins, the content (`content_callback`) cannot be overwritten. In such cases:

  - **Recommendation**: Create a new column with a custom ID for full control.
  - Optionally, hide the original column by setting `visible => false`.

- **Default WordPress Columns:** You can modify default WordPress columns like `title`, `date`, `author`, etc., using their IDs.

---

## **How to Create a New Column**

To add a new column, simply define a new element in the array with the desired characteristics. The order of the columns in the admin list will follow the order in the array.

#### Example:

```php
$columns('new_column') = (
    'label' => 'New Column',
    'content_callback' => function($post_id) {
        echo 'This is a new column!';
    },
    'sortable' => false,
    'visible' => true,
    'width' => '150px'
);
```

---

## **How to Modify Column Order**

The order of the columns is determined by their position in the array. To reorder columns, adjust their position in the array.

#### Example:

```php
$columns = (
    'title' => (
        'label' => 'Title',
    ),
    'my_custom_column' => (
        'label' => 'Custom',
    ),
    'date' => (
        'label' => 'Date',
    )
);
```

---

## **Disclaimer**

This function is designed to simplify development and customization of the WordPress admin panel. It does not replace best practices but aims to make implementations cleaner, simpler, and more functional.

---

## **Implementation Guide**

1. Copy the `customize_columns()` function into your theme's or plugin's `functions.php` file.
2. Define your custom columns in an array as shown in the examples.
3. Call the function and pass the CPT or taxonomy slug along with your columns configuration.
4. Test and adjust the functionality as needed.
