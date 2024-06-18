<?php
/**
 * Handles Kubota Connect Data Encryption
 * 
 * This class defines all code necessary to encrypt and decrypt data.
 * 
 * @link       https://theapphub.com.au
 * @since      1.0.0
 * 
 */
class Kubota_Connect_Password_Manager {

    /**
     * The encryption key
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $key    The encryption key
     */
    private $key;

    /**
     * The encryption salt
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $salt    The encryption salt
     */
	private $salt;

    /**
     * Constructor
     * 
     * @since    1.0.0
     * 
     * @param    string    $key    The encryption key
     * @param    string    $salt    The encryption salt
     */
    public function __construct(  ) {
        $this->key  = $this->get_default_key();
		$this->salt = $this->get_default_salt();
    }

    /**
     * Encrypt data
     * 
     * @since    1.0.0
     * 
     * @param    string    $value    The value to encrypt
     * @return   string    The encrypted value
     */
	public function encrypt( $value ) {
        if ( ! extension_loaded( 'openssl' ) ) {
            return $value;
        }
    
        $method = 'aes-256-ctr';
        $ivlen  = openssl_cipher_iv_length( $method );
        $iv     = openssl_random_pseudo_bytes( $ivlen );
    
        $raw_value = openssl_encrypt( $value . $this->salt, $method, $this->key, 0, $iv );
        if ( ! $raw_value ) {
            return false;
        }
    
        return base64_encode( $iv . $raw_value );
    }

    /**
     * Decrypt data
     * 
     * @since    1.0.0
     * 
     * @param    string    $raw_value    The value to decrypt
     * @return   string    The decrypted value
     */
	public function decrypt( $raw_value ) {
        if ( ! extension_loaded( 'openssl' ) ) {
            return $raw_value;
        }
    
        $raw_value = base64_decode( $raw_value, true );
    
        $method = 'aes-256-ctr';
        $ivlen  = openssl_cipher_iv_length( $method );
        $iv     = substr( $raw_value, 0, $ivlen );
    
        $raw_value = substr( $raw_value, $ivlen );
    
        $value = openssl_decrypt( $raw_value, $method, $this->key, 0, $iv );
        if ( ! $value || substr( $value, - strlen( $this->salt ) ) !== $this->salt ) {
            return false;
        }
    
        return substr( $value, 0, - strlen( $this->salt ) );
    }

    /**
     * Get the default encryption key
     * 
     * @since    1.0.0
     * 
     * @return   string    The default encryption key
     */
    private function get_default_key() {
		if ( defined( 'LOGGED_IN_KEY' ) && '' !== LOGGED_IN_KEY ) {
			return LOGGED_IN_KEY;
		}

		// If this is reached, you're either not on a live site or have a serious security issue.
		return ')zeevYUO-Bkcn18QQ6,FT4TZ]<YLGGZ+~/B1h25[+//Qcx.Dhs]<IcYSw]MbNfSH';
	}

    /**
     * Get the default encryption salt
     * 
     * @since    1.0.0
     * 
     * @return   string    The default encryption salt
     */
    private function get_default_salt() {
		if ( defined( 'LOGGED_IN_SALT' ) && '' !== LOGGED_IN_SALT ) {
			return LOGGED_IN_SALT;
		}

		// If this is reached, you're either not on a live site or have a serious security issue.
		return 'Ll2g%{9a>?:sipat[%?dX&]PpR>%6AVelP%6qc4E19o^s-x+5j:xWMgH ,mtma{T';
	}
}