<?php
// The above is the plugin header that provides metadata about the plugin. The plugins name , description, version and author.
/*
Plugin Name: Author Customisation Plugin.
Description: Plugin to customise author input
Version: 1.0
Author: Shana

*/

// This line includes the rest-api.php which allows the code in that file to run as part of the plugin for my plugin
require_once plugin_dir_path(__FILE__) . 'rest-api.php'; //Including this file into here so it will run that code aswell.


// The below are the hooking functions into wordpress
// author_profile_filed is the callback function that will be triggered when the (action) of the user is on their profile viewing/editing their profile.
// This function takes an argument of user which 
add_action('show_user_profile', 'author_profile_field'); // These hooks trigger the author_profile_field function when a user views or edits their profile.
add_action('edit_user_profile', 'author_profile_field'); // These hooks trigger the author_profile_field function when a user views or edits their profile.
add_action('personal_options_update', 'save_author_profile_field'); // These hooks trigger the save_author_profile_field function to save custom field data when the profile is updated.
add_action('edit_user_profile_update', 'save_author_profile_field'); //These hooks trigger the save_author_profile_field function to save custom field data when the profile is updated.
add_action('admin_footer-profile.php', 'set_form_enctype'); // These hooks add functionality to set the form enctype for the file uploads when viewing or editing a user profile.
add_action('admin_footer-user-edit.php', 'set_form_enctype'); // These hooks add functionality to set the form enctype for the file uploads when viewing or editing a user profile.
add_action('init', 'register_block'); // This registers a custom block when wordpress is initialised.


// This retrieves the meta fields (profile picture, Facebook, Twitter, Instagram links) for the current user. If the profile picture is missing it provides a default Gravatar image.
// These fields are shown on the users profile page for viewing/editing.
function author_profile_field($user) {

// All meta fields are converted into variables here 
// We are creating variables which will store the retrieved required data from the get_user_meta. This is a wordpress 
   $profile_picture= get_user_meta($user->ID, 'author_customisation_profile_picture', true);
    if(empty($profile_picture)){
        $profile_picture = 'http://2.gravatar.com/avatar/ea8b076b398ee48b71cfaecf898c582b?s=192&d=mm&r=g';
    }

   $facebook_link = get_user_meta($user->ID, 'author_customisation_facebook_link', true); // retrieving the facebook link that a specific user has stored in their profiel and save it to the $facebook link vairable
   $twitter_link = get_user_meta($user->ID, 'author_customisation_twitter_link', true);
   $instagram_link = get_user_meta($user->ID, 'author_customisation_instagram_link', true);

// Here we end PHP and inject HTML   
?>

<!-- This section displays the users profile and allows the user to upload a new picture. It includes a label for the profile picture and a img tag that displays the current profile picture or default Gravatar -->
<!-- These rows provide input fields for the user to enter their Facebook, Twitter, and Instagram URLs. Each input field is associated with a description to guide the user. -->
<!-- In html a table consists of rows tr (table rows) and columns td (table data) -->
<!-- <th> (Table Header): Represents a header cell in a table row. It is typically bolded and centered by default and indicates that this cell contains a heading or label for the following data -->
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
            <th><label for="author_customisation_facebook_link">Facebook URL</label></th> <!-- Here is the facebook social media field where the user can input their facebook details -->
            <td>
                <input type="text" name="author_customisation_facebook_link" id="author_customisation_facebook_link" value="<?php echo esc_attr($facebook_link) ?>" class="regular-text">
                <br><span class="description">Enter your Facebook profile link.</span>
            </td>
        </tr>
        <tr>
            <th><label for="author_customisation_twitter_link">Twitter URL</label></th> <!-- Here is the twitter input filed where the user can add their twitter details -->
            <td>
                <input type="text" name="author_customisation_twitter_link" id="author_customisation_twitter_link" value="<?php echo esc_attr($twitter_link) ?>" class="regular-text">
                <br><span class="description">Enter your Twitter profile link.</span>
            </td>
        </tr>
        <tr>
            <th><label for="author_customisation_instagram_link">Instagram URL</label></th> <!-- Here is the instagram input filed where the user can add their twitter details -->
            <td>
                <input type="text" name="author_customisation_instagram_link" id="author_customisation_instagram_link" value="<?php echo esc_attr($instagram_link) ?>" class="regular-text">
                <br><span class="description">Enter your Instagram profile link.</span>
            </td>
</table>


<?php



}

// This function is responsible for saving the custom author profile fileds (social media profiles and profile picture) when a user updates their profile in wordpress. 
function save_author_profile_field($user_id) {
    if(!current_user_can('edit_user', $user_id)){  // Checking if the current logged in user has permission to edit the profile of the user with ID $user_id. Ensuring that only authrised users can modify the profile.
        return false;
    }

    update_user_meta($user_id, 'author_customisation_facebook_link', esc_attr($_POST['author_customisation_facebook_link'])); // saves the link provided by the user
    update_user_meta($user_id, 'author_customisation_twitter_link', esc_attr($_POST['author_customisation_twitter_link'])); // POST retireves the facebook link submitted via the form when the profile is updated.
    update_user_meta($user_id, 'author_customisation_instagram_link', esc_attr($_POST['author_customisation_instagram_link'])); // update_user_meta() function stored the value in the wordpress database. This is a wordpress function.

    // This is checking if a file (profile picture) was uploaded and if the file has a name and isn't blank
    //  wp_die will stop the script from running and display the error message provided in the parantheses. 
    if(!empty($_FILES['author_customisation_profile_picture'])) {
        if (!empty($_FILES['author_customisation_profile_picture']['name'])) {
        $uploaded = media_handle_upload('author_customisation_profile_picture', 0);
        if(is_wp_error($uploaded)) {
         wp_die('Profile picture upload failed');
        } else {
            update_user_meta($user_id, 'author_customisation_profile_picture', wp_get_attachment_url($uploaded)); // If the file is uploaded it is saved as a usea metafield and uploaded. 
        }
    }
    }
}
// This function outputs Javascript when called
// This function wi; ensure that the form with the ID of your-profile on wordpress can handle file uploads by setting the corrrect encoding type for the form
// multipart/form-data attribute ensures the form submitted will include multiple different types of data like file uploads (the profile picture upload)
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

// This function is used in wordpress to register a custom block script for Gutenberg. 
// The 'wp-blocks' etc array are the depencies of my script. These are wordpress core javascript libraries that the script will depend on.
function register_block() {
    wp_register_script (
        'author_customisation_block',
        plugins_url('build/index.js', __FILE__),
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data']
    );

// This code is registering my css file that will style my custom gutenberg block in the wordpress block editor. 
    wp_register_style(
        'author_customisation_block_editor',
        plugins_url('build/index.css', __FILE__),
        ['wp-edit-blocks']
    );

// Here I am registering a new custom block in wordpress block editor.
// Linking javascript and css
// Sets up a function to control how the block is rendered the front end
// Defines the data structure (attributes) that the block can store such as selectedAuthor. 
    register_block_type(
        'custom/author-block',
        [
            'editor_script' => 'author_customisation_block',
            'editor_style' => 'author_customisation_block_editor',
            'render_callback' => 'custom_author_block_render',
            'attributes' => [
                'selectedAuthor' => [
                    'type' => 'string',
                    'default' => "",
                ],
            ],
        ]
        );

        

}


function custom_author_block_render( $attributes ) {
    if ( empty( $attributes['selectedAuthor'] ) ) {
        return '<p>Select an author to display their profile.</p>';
    }

    $user_id = $attributes['selectedAuthor'];
    $author_name = get_the_author_meta( 'display_name', $user_id );
    $profile_picture = get_user_meta( $user_id, 'author_customisation_profile_picture', true );
    $twitter_link = get_user_meta( $user_id, 'author_customisation_twitter_link', true );
    $facebook_link = get_user_meta( $user_id, 'author_customisation_facebook_link', true );
    $instagram_link = get_user_meta( $user_id, 'author_customisation_instagram_link', true );

    ob_start();
    ?>
    <div class="custom-author-profile">
        <?php if ( $profile_picture ): ?>
            <img src="<?= esc_url( $profile_picture ); ?>" alt="<?= esc_attr( $author_name ); ?>" style="width:100px;height:100px;" />
        <?php endif; ?>
        <h3><?= esc_html( $author_name ); ?></h3>
        <ul>
            <?php if ( $twitter_link ): ?>
                <li><a href="<?= esc_url( $twitter_link ); ?>" target="_blank">Twitter</a></li>
            <?php endif; ?>
            <?php if ( $facebook_link ): ?>
                <li><a href="<?= esc_url( $facebook_link ); ?>" target="_blank">Facebook</a></li>
            <?php endif; ?>
            <?php if ( $instagram_link ): ?>
                <li><a href="<?= esc_url( $instagram_link ); ?>" target="_blank">Instagram</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}