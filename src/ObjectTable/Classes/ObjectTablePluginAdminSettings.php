<?php

namespace CommonGateway\ObjectTable\Classes;

class ObjectTablePluginAdminSettings
{

    public function __construct()
    {
        // The Admin menu Item
        add_action('admin_menu', [$this, 'objecttable_options_page']);

        // Initiating the settings page
        add_action('admin_init', [$this, 'wporg_settings_init']);
        add_action('admin_post_objecttable_add_config', [$this, 'handle_add_config']);

        // Handle removing a config
        add_action('admin_post_objecttable_remove_config', [$this, 'handle_remove_config']);

        // Use css
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
    }

    public function handle_remove_config()
    {
        // Check user
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        // Get existing configs
        $configs = get_option('objecttable_configs', []);

        // Get the ID of the config to remove
        $remove_id = intval($_POST['remove_config_id']);

        // Remove the config with the specified ID
        $configs = array_filter($configs, function($config) use ($remove_id) {
            return $config['id'] !== $remove_id;
        });

        // Update the option in the database
        update_option('objecttable_configs', array_values($configs));

        // Redirect back to the settings page
        wp_redirect(admin_url('options-general.php?page=objecttable'));
        exit;
    }

    public function enqueue_admin_styles()
    {
        wp_enqueue_style(
            'objecttable-admin-styles',
            plugin_dir_url(dirname(__FILE__, 3)) . 'src/ObjectTable/Assets/css/admin-styles.css',
            array(),
            filemtime(plugin_dir_path(dirname(__FILE__, 3)) . 'src/ObjectTable/Assets/css/admin-styles.css') 
        );
    }

    public function handle_add_config()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }
        
        $configs = get_option('objecttable_configs', []);
        
        // Get the last used ID, or default to 0 if it doesn't exist
        $last_id = get_option('objecttable_last_id', 0);
        
        $new_id = $last_id + 1;
        
        $new_config = [
            'id' => $new_id,
            'url' => sanitize_text_field($_POST['new_config_url']),
            'key' => sanitize_text_field($_POST['new_config_key']),
            'cssclass' => sanitize_text_field($_POST['new_config_cssclass']),
            'mapping' => isset($_POST['new_config_mapping']) ? stripslashes($_POST['new_config_mapping']) : null 
        ];
        
        $configs[] = $new_config;
        
        // Update the option in the database
        update_option('objecttable_configs', $configs);
        
        // Update the last used ID in the database
        update_option('objecttable_last_id', $new_id);
        
        // Redirect back to the settings page
        wp_redirect(admin_url('options-general.php?page=objecttable'));
        exit;
    }
    
    
    public function objecttable_options_page_html()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $configs = get_option('objecttable_configs', []);

        // Start wrap.
        echo '<div class="wrap">';

        // Title.
        echo '<h1 class="wp-heading-inline">ObjectTable - Manage API configurations</h1>';

        // Description.
        echo '<h2>How to use shortcodes</h2>';
        echo '<p>A shortcode can be added to a page like [object-table configId="1"] where configId is a id of a configuration chosen from the table below you want to fetch and render.</p>';
        
        $this->renderForm();
        $this->renderTable($configs);

        // Close wrap.
        echo '</div>';
    }

    /**
     * The settings menu item
     */
    public function objecttable_options_page()
    {
        add_submenu_page(
            'options-general.php',
            'ObjectTable',
            'ObjectTable',
            'manage_options',
            'objecttable',
            [$this, 'objecttable_options_page_html']
        );
    }

    /**
     * Lets define some settings
     */
    public function wporg_settings_init()
    {
        // register a new section in the "reading" page
        add_settings_section(
            'default', // id
            'API  Configuration', // title
            [$this, 'wporg_settings_section_callback'], // callback
            'objecttable_api' // page
        );
    }

    /**
     * callback functions
     */

    // section content cb
    public function wporg_settings_section_callback()
    {
        echo '<p>In order to use the objecttable api you wil need to provide api credentials.</p>';
    }

    private function renderForm()
    {
        // Form for adding new config
        echo '<h2>Add a configuration</h2>';
        echo '<form method="post" action="' . admin_url('admin-post.php') . '" class="objecttable-form">';  
        echo '<div class="objecttable-form-field-row">';  
        echo '<div class="objecttable-form-field">';
        echo '<input type="hidden" name="action" value="objecttable_add_config">'; 
        echo '<input type="text" name="new_config_url" placeholder="API URL" class="objecttable-input" required>';
        echo '</div>';
        echo '<div class="objecttable-form-field">';
        echo '<input type="text" name="new_config_key" placeholder="API Key" class="objecttable-input" required>';
        echo '</div>';
        echo '<div class="objecttable-form-field">';
        echo '<input type="text" name="new_config_cssclass" placeholder="Table CSS class" class="objecttable-input">';
        echo '</div>';
        echo '</div>';  
        echo '<div class="objecttable-form-field objecttable-form-field-full">'; 
        echo '<textarea name="new_config_mapping" placeholder="Mapping (JSON)" class="objecttable-textarea"></textarea>';
        echo '</div>';
        echo '<div class="objecttable-form-submit">';
        echo '<input type="submit" value="Add Config" class="objecttable-submit">';
        echo '</div>';
        echo '</form>';
    }
    

    private function renderTable(array $configs)
    {
        // Table for viewing existing configs  
        echo '<h2>Existing configurations</h2>';
        echo '<table class="objecttable-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';  
        echo '<th>API URL</th>';  
        echo '<th>API Key</th>';  
        echo '<th>Table CSS class</th>';  
        echo '<th>Mapping</th>';  
        echo '<th></th>';  
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach($configs as $config) {
            echo '<tr>';
            echo '<td>' . esc_html($config['id']) . '</td>';
            echo '<td>' . esc_html($config['url']) . '</td>';
            echo '<td>' . esc_html($config['key']) . '</td>';
            echo '<td>' . (isset($config['cssclass']) ? esc_html($config['cssclass']) : '') .  '</td>';
            echo '<td>' . (isset($config['mapping']) ? esc_html($config['mapping']) : '') . '</td>'; 
            echo '<td>';
            echo '<form method="post" action="' . admin_url('admin-post.php') . '">';
            echo '<input type="hidden" name="action" value="objecttable_remove_config">';
            echo '<input type="hidden" name="remove_config_id" value="' . esc_attr($config['id']) . '">';
            echo '<input type="submit" value="Remove">';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
}
