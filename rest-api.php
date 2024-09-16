<?php
// Extend the WordPress REST API to include custom user meta fields for reading only.
// This file registers additional meta fields so they can be accessed via REST API requests. This allows access to extra data about the users through the rest api

add_action('rest_api_init', function () {

    // Register custom fields to the wp/v2/users endpoint
    register_rest_field('user', 'facebook_link', [
        'get_callback' => 'get_user_facebook_link',
        'schema' => [
            'description' => 'Facebook profile link for the user',
            'type' => 'string',
            'context' => ['view'],
        ],
    ]);

    register_rest_field('user', 'twitter_link', [
        'get_callback' => 'get_user_twitter_link',
        'schema' => [
            'description' => 'Twitter profile link for the user',
            'type' => 'string',
            'context' => ['view'],
        ],
    ]);

    register_rest_field('user', 'instagram_link', [
        'get_callback' => 'get_user_instagram_link',
        'schema' => [
            'description' => 'Instagram profile link for the user',
            'type' => 'string',
            'context' => ['view'],
        ],
    ]);

    register_rest_field('user', 'profile_picture', [
        'get_callback' => 'get_user_profile_picture',
        'schema' => [
            'description' => 'Profile picture URL for the user',
            'type' => 'string',
            'context' => ['view'],
        ],
    ]);

});

// Callback functions to retrieve the custom fields

function get_user_facebook_link($user, $field_name, $request) {
    return get_user_meta($user['id'], 'author_customisation_facebook_link', true);
}

function get_user_twitter_link($user, $field_name, $request) {
    return get_user_meta($user['id'], 'author_customisation_twitter_link', true);
}

function get_user_instagram_link($user, $field_name, $request) {
    return get_user_meta($user['id'], 'author_customisation_instagram_link', true);
}

function get_user_profile_picture($user, $field_name, $request) {
    return get_user_meta($user['id'], 'author_customisation_profile_picture', true);
}