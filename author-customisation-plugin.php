<?php
/*
Plugin Name: Author Customisation Plugin
Description: Plugin to customise author input
Version: 1.0
Author: Shana
*/

require_once plugin_dir_path(__FILE__) . 'rest-api.php'; // Include your REST API file

// Enqueue block assets (JavaScript and CSS)
function register_block_assets() {
    // Register editor script
    wp_register_script(
        'author_customisation_block',
        plugins_url('build/index.js', __FILE__),
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data'],
        null,
        true
    );

    // Register editor style
    wp_register_style(
        'author_customisation_block_editor',
        plugins_url('build/index.css', __FILE__),
        ['wp-edit-blocks'],
        null
    );

    // Register front-end style
    wp_register_style(
        'author_customisation_block_frontend',
        plugins_url('build/style.css', __FILE__)
    );

    // Register block type using block.json
    register_block_type_from_metadata(__DIR__);

    // Enqueue editor styles for the block editor
    wp_enqueue_style('author_customisation_block_editor');
}
add_action('init', 'register_block_assets');

// Render callback function for the front-end display
function custom_author_block_render($attributes) {
    if (empty($attributes['selectedAuthor'])) {
        return '<p>Select an author to display their profile.</p>';
    }

    $user_id = $attributes['selectedAuthor'];
    $author_name = get_the_author_meta('display_name', $user_id);
    $profile_picture = get_user_meta($user_id, 'author_customisation_profile_picture', true);
    $twitter_link = get_user_meta($user_id, 'author_customisation_twitter_link', true);
    $facebook_link = get_user_meta($user_id, 'author_customisation_facebook_link', true);
    $instagram_link = get_user_meta($user_id, 'author_customisation_instagram_link', true);

    ob_start();
    ?>
    <div class="custom-author-profile">
        <?php if ($profile_picture): ?>
            <img src="<?= esc_url($profile_picture); ?>" alt="<?= esc_attr($author_name); ?>" style="width:100px;height:100px;" />
        <?php endif; ?>
        <h3><?= esc_html($author_name); ?></h3>
        <ul>
            <?php if ($twitter_link): ?>
                <li><a href="<?= esc_url($twitter_link); ?>" target="_blank">Twitter</a></li>
            <?php endif; ?>
            <?php if ($facebook_link): ?>
                <li><a href="<?= esc_url($facebook_link); ?>" target="_blank">Facebook</a></li>
            <?php endif; ?>
            <?php if ($instagram_link): ?>
                <li><a href="<?= esc_url($instagram_link); ?>" target="_blank">Instagram</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}

// Add profile customization fields (social media links and profile picture)
add_action('show_user_profile', 'author_profile_field'); 
add_action('edit_user_profile', 'author_profile_field'); 
add_action('personal_options_update', 'save_author_profile_field'); 
add_action('edit_user_profile_update', 'save_author_profile_field'); 
add_action('admin_footer-profile.php', 'set_form_enctype'); 
add_action('admin_footer-user-edit.php', 'set_form_enctype');

// Display custom profile fields
function author_profile_field($user) {
    $profile_picture = get_user_meta($user->ID, 'author_customisation_profile_picture', true);
    if (empty($profile_picture)) {
        $profile_picture = 'http://2.gravatar.com/avatar/ea8b076b398ee48b71cfaecf898c582b?s=192&d=mm&r=g';
    }

    $facebook_link = get_user_meta($user->ID, 'author_customisation_facebook_link', true);
    $twitter_link = get_user_meta($user->ID, 'author_customisation_twitter_link', true);
    $instagram_link = get_user_meta($user->ID, 'author_customisation_instagram_link', true);
    ?>
    <h3>Custom Profile Fields</h3>
    <table class='form-table'>
        <tr>
            <th><label for="author_customisation_profile_picture">Profile Picture</label></th>
            <td>
                <img src="<?php echo esc_url($profile_picture); ?>" alt="Profile Picture" style="max-width: 100px; max-height: 100px"/><br/> 
                <input type="file" name="author_customisation_profile_picture" id="author_customisation_profile_picture"/><br/>
                <span class="description">Upload Your Profile Picture (jpeg, png, gif)</span>
            </td>
        </tr>
        <tr>
            <th><label for="author_customisation_facebook_link">Facebook URL</label></th>
            <td>
                <input type="text" name="author_customisation_facebook_link" id="author_customisation_facebook_link" value="<?php echo esc_attr($facebook_link) ?>" class="regular-text">
                <br><span class="description">Enter your Facebook profile link.</span>
            </td>
        </tr>
        <tr>
            <th><label for="author_customisation_twitter_link">Twitter URL</label></th>
            <td>
                <input type="text" name="author_customisation_twitter_link" id="author_customisation_twitter_link" value="<?php echo esc_attr($twitter_link) ?>" class="regular-text">
                <br><span class="description">Enter your Twitter profile link.</span>
            </td>
        </tr>
        <tr>
            <th><label for="author_customisation_instagram_link">Instagram URL</label></th>
            <td>
                <input type="text" name="author_customisation_instagram_link" id="author_customisation_instagram_link" value="<?php echo esc_attr($instagram_link) ?>" class="regular-text">
                <br><span class="description">Enter your Instagram profile link.</span>
            </td>
        </tr>
    </table>
    <?php
}

// Save the custom fields for the user profile
function save_author_profile_field($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    update_user_meta($user_id, 'author_customisation_facebook_link', esc_attr($_POST['author_customisation_facebook_link']));
    update_user_meta($user_id, 'author_customisation_twitter_link', esc_attr($_POST['author_customisation_twitter_link']));
    update_user_meta($user_id, 'author_customisation_instagram_link', esc_attr($_POST['author_customisation_instagram_link']));

    if (!empty($_FILES['author_customisation_profile_picture'])) {
        if (!empty($_FILES['author_customisation_profile_picture']['name'])) {
            $uploaded = media_handle_upload('author_customisation_profile_picture', 0);
            if (is_wp_error($uploaded)) {
                wp_die('Profile picture upload failed');
            } else {
                update_user_meta($user_id, 'author_customisation_profile_picture', wp_get_attachment_url($uploaded));
            }
        }
    }
}

// Ensure the form supports file uploads
function set_form_enctype() {
    echo '<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            var profileForm = document.getElementById("your-profile");
            if (profileForm) {
                profileForm.setAttribute("enctype", "multipart/form-data");
            }
        });
    </script>';
}