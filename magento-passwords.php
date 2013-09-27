<?php 
/**
 * A plugin that allows Wordpress to use imported magento passwords.
 * Passwords a converted to the Wordpress format on the first login. 
 *
 * @package   Magento Passwords
 * @author    Thomas Lhotta <th.lhotta@gmail.com>
 * @link      http://www.github.com/thomaslhotta
 * @copyright 2013 Thomas Lhotta
 *
 * @wordpress-plugin
 * Plugin Name: Magento Passwords
 * Plugin URI:  http://www.github.com/thomaslhotta/magento-passwords
 * Description: A plugin that allows wordpress to use magento passwords.
 * Version:     1.0.0
 * Author:      Thomas Lhotta
 * Author URI:  http://www.github.com/thomaslhotta
 * License:     GPL-2.0+
 */
function magento_passwords_check_password( $check, $password, $hash, $user_id )
{
    // Don't do anything if password is already checked.
    if ( $check ) {
        return $check;
    }

    $hash = explode( ':', $hash );

    // Not a magento password
    if ( count( $hash ) <> 2 ) {
        return $check;
    }

    $salt = $hash[1];
    $hash = $hash[0];

    // Compute hash, Magento passwords are unsafe
    $computed_password =  md5($salt . $password);

    if ( $hash !== $computed_password ) {
        return false;
    }

    // Change the user password to the wordpress format.
    wp_set_password( $password, $user_id );

    return true;
}

add_filter( 'check_password', 'magento_passwords_check_password', 11, 4 );