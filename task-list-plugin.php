# Plugin main file: task-list.php
<?php
/*
Plugin Name: Task List Plugin
Description: A simple plugin to manage and display tasks using a custom post type and shortcode
Version: 1.0
Author: Your Name
*/

// Prevent direct access to the plugin
if (!defined('ABSPATH')) {
    exit;
}

class TaskListPlugin {
    public function __construct() {
        // Register hooks
        add_action('init', [$this, 'register_task_post_type']);
        add_action('init', [$this, 'register_task_status_taxonomy']);
        add_shortcode('task_list', [$this, 'render_task_list_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_plugin_styles']);
    }

    // Register custom post type for tasks
    public function register_task_post_type() {
        $labels = [
            'name'               => 'Tasks',
            'singular_name'      => 'Task',
            'menu_name'          => 'Tasks',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Task',
            'edit_item'          => 'Edit Task',
            'new_item'           => 'New Task',
            'view_item'          => 'View Task',
            'search_items'       => 'Search Tasks',
            'not_found'          => 'No tasks found',
            'not_found_in_trash' => 'No tasks found in Trash'
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'task'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => ['title', 'editor', 'thumbnail']
        ];

        register_post_type('task', $args);
    }

    // Register custom taxonomy for task status
    public function register_task_status_taxonomy() {
        $labels = [
            'name'              => 'Task Status',
            'singular_name'     => 'Task Status',
            'search_items'      => 'Search Task Statuses',
            'all_items'         => 'All Task Statuses',
            'edit_item'         => 'Edit Task Status',
            'update_item'       => 'Update Task Status',
            'add_new_item'      => 'Add New Task Status',
            'new_item_name'     => 'New Task Status Name',
            'menu_name'         => 'Task Status'
        ];

        $args = [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'task-status']
        ];

        register_taxonomy('task_status', ['task'], $args);

        // Predefined task statuses
        $statuses = ['pending', 'done'];
        foreach ($statuses as $status) {
            if (!term_exists($status, 'task_status')) {
                wp_insert_term($status, 'task_status');
            }
        }
    }

    // Shortcode to render task list
    public function render_task_list_shortcode($atts) {
        // Parse attributes with default values
        $atts = shortcode_atts([
            'status' => '', // Optional status filter
        ], $atts);

        // Prepare query arguments
        $args = [
            'post_type'      => 'task',
            'posts_per_page' => -1, // Show all tasks
            'orderby'        => 'date',
            'order'          => 'DESC'
        ];

        // Add status filter if provided
        if (!empty($atts['status'])) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'task_status',
                    'field'    => 'slug',
                    'terms'    => $atts['status']
                ]
            ];
        }

        // Run the query
        $tasks_query = new WP_Query($args);

        // Start output buffering
        ob_start();

        // Check if tasks exist
        if ($tasks_query->have_posts()) :
            ?>
            <div class="task-list-container">
                <ul class="task-list">
                    <?php while ($tasks_query->have_posts()) : $tasks_query->the_post(); ?>
                        <li class="task-item">
                            <h3 class="task-title"><?php the_title(); ?></h3>
                            <div class="task-content"><?php the_content(); ?></div>
                            <?php
                            // Get task status
                            $task_statuses = get_the_terms(get_the_ID(), 'task_status');
                            if ($task_statuses) {
                                $status_names = wp_list_pluck($task_statuses, 'name');
                                echo '<div class="task-status">' . implode(', ', $status_names) . '</div>';
                            }
                            ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <?php
            // Reset post data
            wp_reset_postdata();
        else :
            echo '<p class="no-tasks">No tasks found.</p>';
        endif;

        // Return the buffered content
        return ob_get_clean();
    }

    // Enqueue plugin styles
    public function enqueue_plugin_styles() {
        wp_enqueue_style('task-list-styles', plugins_url('assets/css/task-list.css', __FILE__));
    }
}

// Initialize the plugin
new TaskListPlugin();
