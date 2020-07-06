<?php

/*
Plugin Name: Commander Captcha
Version: 0.1
Plugin URI: http://mtgcommander.net
description: Adds a simple question to comment submisions
Author: Gavin Duggan
Author URI: http://mtgcommander.net
*/

add_action( 'comment_form_logged_in_after', 'additional_fields' );
add_action( 'comment_form_after_fields', 'additional_fields' );

function additional_fields($fields) {
    echo '<p class="comment-form-captcha">'.
      '<label for="commandercaptcha">' . __( 'Which basic land produces red mana?' ) .
      '<span class="required">*</span>'.
      '</label>'.
      '<input id="commandercaptcha" name="commandercaptcha" type="text" size="30"  tabindex="4" /></p>';
}


add_action( 'preprocess_comment', 'validate_commandercaptcha' );
function validate_commandercaptcha( $commentdata ) {
    if ( ! isset( $_POST['commandercaptcha'] ) ) {
      wp_die( __( 'Error: You did not answer the required question. Go back and try again.' ) );
    }
    if ( strtolower( $_POST['commandercaptcha'] ) != "mountain" ) {
      wp_die( __( 'Error: You are either a bot, or did not pay enough attention.' ) );
    }
    return $commentdata;
}

