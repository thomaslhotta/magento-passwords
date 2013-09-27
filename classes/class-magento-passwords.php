<?php
/**
 * A class that enables user passwords to be transfered from Magento.
 * 
 * Passwords are converted to the Wordpress/Phpass format on the first login.
 * 
 * @author Thomas Lhotta
 *
 */
class Magento_Passwords
{
    public function __construct()
    {
        add_filter( 'check_password', array( $this, 'check_password' ), 11, 4 );
    }
    
    public function check_password( $check, $password, $hash, $user_id )
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
}