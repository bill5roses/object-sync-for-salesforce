<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://code.minnpost.com
 * @since      1.0.0
 *
 * @package    Wordpress_Salesforce_Rest
 * @subpackage Wordpress_Salesforce_Rest/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wordpress_Salesforce_Rest
 * @subpackage Wordpress_Salesforce_Rest/admin
 * @author     Jonathan Stegall <jstegall@minnpost.com>
 */
class Wordpress_Salesforce_Rest_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The path of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_path    The path of this plugin.
	 */
	private $plugin_path;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The options name to be used in this plugin
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @var  	string 		$option_name 	Option name of this plugin
	 */
	private $option_name = 'wordpress_salesforce_rest';

	/**
	 * The array of tabs for plugin admin navigation
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @var  	array 		$settings_tabs 	Option name of this plugin
	 */
	private $settings_tabs = array();
	private $settings_key = 'salesforce_settings';
	private $tokens_key = 'salesforce_tokens';
    private $fieldmaps_key = 'fieldmaps_settings';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $plugin_path       The path of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_path, $version ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_path = $plugin_path;
		$this->version = $version;

	}

	/**
	 * Load settings for each section from database
	 *
	 * @since    1.0.0
	 */
	public function load_settings() {
	    $this->settings = (array) get_option( $this->settings_key );
	    $this->tokens = (array) get_option( $this->tokens_key );
	    $this->fieldmaps = (array) get_option( $this->fieldmaps_key );
	}

	/**
	 * Get setting value
	 *
	 * @since    1.0.0
	 */
	public function get_setting_value( $array, $key ) {
	    if ( array_key_exists( $key, $array ) ) {
	    	return esc_attr( $array["$key"] );
	    } else {
	    	return '';
	    }
	}

	

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Salesforce_Rest_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Salesforce_Rest_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wordpress-salesforce-rest-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Salesforce_Rest_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Salesforce_Rest_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wordpress-salesforce-rest-admin.js', array( 'jquery' ), $this->version, false );

	}



	/**
	 * Set up menu items for plugin sections
	 *
	 * @since  1.0.0
	 */
	function add_admin_menus() {
	    //add_options_page( 'Salesforce', 'Salesforce', 'manage_options', $this->plugin_options_key, array( &$this, 'salesforce' ) );
	    if( !session_id() ) {
	        session_start();
	    }
	    $this->plugin_screen_hook_suffix = add_options_page(
			__( 'Salesforce', $this->plugin_name ),
			__( 'Salesforce', $this->plugin_name ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'plugin_options_page' )
		);
	}

	/**
	 * Render a settings page, including tabs, for plugin
	 *
	 * @since  1.0.0
	 */
	function plugin_options_page() {
	    $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->settings_key;
	    if ( $tab !== 'authorize' ) {
	    	include_once 'partials/wordpress-salesforce-rest-admin-default.php';
	    } else {
	    	// tab is authorize
	    	$this->authorize_salesforce();
	    }
	}

	function authorize_salesforce() {
		if ( !defined( 'SALESFORCE_CONSUMER_KEY' ) ) {
			define( 'SALESFORCE_CONSUMER_KEY', $this->get_setting_value( $this->settings, 'salesforce_consumer_key' ) );
		}
		if ( !defined( 'SALESFORCE_CONSUMER_SECRET' ) ) {
			define( 'SALESFORCE_CONSUMER_SECRET', $this->get_setting_value( $this->settings, 'salesforce_consumer_secret' ) );
		}
		if ( !defined( 'SALESFORCE_CALLBACK_URL' ) ) {
			define( 'SALESFORCE_CALLBACK_URL', $this->get_setting_value( $this->settings, 'salesforce_callback_url' ) );
		}
		if ( !defined( 'SALESFORCE_LOGIN_BASE_URL' ) ) {
			define( 'SALESFORCE_LOGIN_BASE_URL', $this->get_setting_value( $this->settings, 'salesforce_base_url' ) );
		}

		$is_authorized = isset( $_SESSION['access_token'] );
		$salesforce_token = $this->get_setting_value( $this->tokens, 'salesforce_token' );
		
		if ( $salesforce_token !== '' ) {
			$is_authorized = true;
		}
		
		/*if ( $is_authorized === true ) {
			$access_token = $this->get_setting_value( $this->settings, 'salesforce_token' );
			$instance_url = $this->get_setting_value( $this->settings, 'salesforce_base_url' );
		}*/

		if ( !$is_authorized ) {
    		$has_code = isset( $_REQUEST['code'] );

			if ( !$has_code ) {
				$auth_url = SALESFORCE_LOGIN_BASE_URL
				.'/services/oauth2/authorize?response_type=code'
				.'&client_id=' . SALESFORCE_CONSUMER_KEY 
				.'&redirect_uri=' . urlencode( SALESFORCE_CALLBACK_URL );

				// Redirect to the authorization page
				//header('Location: '.$auth_url);
				//exit();
				//echo '<a href="' . $auth_url . '">Log in with Salesforce</a>';
				include_once 'partials/wordpress-salesforce-rest-admin-salesforce.php';
				exit();
			}

			// If we're here, Salesforce must be returning us a code that we can exchange for
			// a proper access token
			$code = $_REQUEST['code'];

			// Make our first call to the API to convert that code into an access token that
			// we can use on subsequent API calls
			$token_url = SALESFORCE_LOGIN_BASE_URL . '/services/oauth2/token';

			$post_fields = array(
				'code' => $code,
				'grant_type' => 'authorization_code',
				'client_id' => SALESFORCE_CONSUMER_KEY,
				'client_secret' => SALESFORCE_CONSUMER_SECRET,
				'redirect_uri' => SALESFORCE_CALLBACK_URL,
			);

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $token_url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );
			curl_setopt( $ch, CURLOPT_POST, TRUE );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_fields );
        
			// Make the API call, and then extract the information from the response
			$token_request_body = curl_exec( $ch ) or die( "Call to get token from code failed: '$token_url' - " . print_r( $post_fields, true ) );
			$token_response_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

			if (($token_response_code<200)||($token_response_code>=300)||empty($token_request_body))
			die("Call to get token from code failed with $token_response_code: '$token_url' - ".print_r($post_fields, true)." - '$token_request_body'");
			$token_request_data = json_decode($token_request_body, true);
			if (empty($token_request_data))
			die("Couldn't decode '$token_request_data' as a JSON object");
			if (!isset($token_request_data['access_token'])||
			!isset($token_request_data['instance_url']))
			die("Missing expected data from ".print_r($token_request_data, true));

			// Save off the values we need for future use
			$_SESSION['access_token'] = $token_request_data['access_token'];
			$_SESSION['instance_url'] = $token_request_data['instance_url'];

			$salesforce_tokens = $this->tokens;
			
			$salesforce_tokens['salesforce_token'] = $token_request_data['access_token'];
			$salesforce_tokens['salesforce_instance_url'] = $token_request_data['instance_url'];
			
			update_option( 'salesforce_tokens', $salesforce_tokens ); // update the database
			echo 'Salesforce is already authorized.';

			// Redirect to the main page without the code in the URL
		    //header( 'Location: ' . SALESFORCE_CALLBACK_URL );
		    exit();
    	}

		// If we're here, we must have a valid session containing the access token for the
		// API, so grab it ready for subsequent use
		//$access_token = $_SESSION['access_token'];
		//$instance_url = $_SESSION['instance_url'];
		$access_token = $this->get_setting_value( $this->tokens, 'salesforce_token' );
		$instance_url = $this->get_setting_value( $this->tokens, 'salesforce_instance_url' );

		// Now we're going to test the API by querying some data from our accounts table
		// Start by specifying the URL of the call
		$query_url = $instance_url . '/services/data/v35.0/query';

		// Now append the actual query we want to run
		$query_url .= '?q=' . urlencode( 'SELECT Name, Id from Contact LIMIT 100' );
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $query_url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		// We need to pass the access token in the header, *not* as a URL parameter
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: OAuth ' . $access_token ) );

		// Make the API call, and then extract the information from the response
		$query_request_body = curl_exec( $ch ) or die( "Query API call failed: '$query_url'" );
		$query_response_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		if ( ( $query_response_code < 200 ) || ( $query_response_code >= 300 ) || empty( $query_request_body ) ) {
		    unset($_SESSION['access_token']);
		    unset($_SESSION['instance_url']);
		    die( "Query API call failed with $query_response_code: '$query_url' - '$query_request_body'" );
		}
		$query_request_data = json_decode( $query_request_body, true );
		if ( empty( $query_request_data ) )
		    die( "Couldn't decode '$query_request_data' as a JSON object" );
		if ( !isset( $query_request_data['totalSize'] ) || !isset( $query_request_data['records'] ) )
		    die( "Missing expected data from " . print_r( $query_request_data, true ) );

		// Grab the information we're interested in
		$total_size = $query_request_data['totalSize'];
		$records = $query_request_data['records'];

		// Now build a simple HTML page to display the results
		include_once 'partials/wordpress-salesforce-rest-admin-salesforce.php';
	}

	/**
	 * Set up tabs for Salesforce sections
	 *
	 * @since  1.0.0
	 */
	function plugin_options_tabs() {
	    $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->settings_key;

	    screen_icon();
	    echo '<h2 class="nav-tab-wrapper">';
	    foreach ( $this->settings_tabs as $tab_key => $tab_caption ) {
	        $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
	        echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_name . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
	    }
	    echo '</h2>';
	}


	/**
	 * Register settings for settings tab
	 *
	 * @since  1.0.0
	 */
	public function register_salesforce_settings() {
		$this->settings_tabs[$this->settings_key] = 'Settings';
		register_setting( $this->settings_key, $this->settings_key );
    	add_settings_section(
    		'section_settings', 'Settings',
    		array( &$this, 'section_settings_desc' ),
    		$this->settings_key
    	);
    	add_settings_field(
    		'salesforce_consumer_key', 'Consumer Key',
    		array( &$this, 'field_salesforce_consumer_key' ),
    		$this->settings_key,
    		'section_settings'
    	);
    	add_settings_field(
    		'salesforce_consumer_secret', 'Consumer Secret',
    		array( &$this, 'field_salesforce_consumer_secret' ),
    		$this->settings_key,
    		'section_settings'
    	);
    	add_settings_field(
    		'salesforce_callback_url', 'Callback URL',
    		array( &$this, 'field_salesforce_callback_url' ),
    		$this->settings_key,
    		'section_settings'
    	);
    	add_settings_field(
    		'salesforce_base_url', 'Login Base URL',
    		array( &$this, 'field_salesforce_base_url' ),
    		$this->settings_key,
    		'section_settings'
    	);
		/*add_settings_field(
			$this->option_name . '_day',
			__( 'Post is outdated after', 'outdated-notice' ),
			array( $this, $this->option_name . '_day_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_day' )
		);*/
	}

	function section_settings_desc() {
		echo 'General section description goes here.';
	}

	function field_salesforce_consumer_key() {
		if ( !defined( 'SALESFORCE_CONSUMER_KEY' ) ) {
	    ?>
	    	<input type="text" name="<?php echo $this->settings_key; ?>[salesforce_consumer_key]" value="<?php echo $this->get_setting_value( $this->settings, 'salesforce_consumer_key' ); ?>" />
	    <?php } else { ?>
	    	<p><code>Defined in wp-config.php</code></p>
		<?php }
	}

	function field_salesforce_consumer_secret() {
		if ( !defined( 'SALESFORCE_CONSUMER_SECRET' ) ) {
	    ?>
	    <input type="text" name="<?php echo $this->settings_key; ?>[salesforce_consumer_secret]" value="<?php echo $this->get_setting_value( $this->settings, 'salesforce_consumer_secret' ); ?>" />
	    <?php } else { ?>
	    	<p><code>Defined in wp-config.php</code></p>
		<?php }
	}

	function field_salesforce_callback_url() {
		if ( !defined( 'SALESFORCE_CALLBACK_URL' ) ) {
	    ?>
	    <input type="text" name="<?php echo $this->settings_key; ?>[salesforce_callback_url]" value="<?php echo $this->get_setting_value( $this->settings, 'salesforce_callback_url' ); ?>" />
	    <?php } else { ?>
	    	<p><code>Defined in wp-config.php</code></p>
		<?php }
	}

	function field_salesforce_base_url() {
		if ( !defined( 'SALESFORCE_LOGIN_BASE_URL' ) ) {
	    ?>
	    <input type="text" name="<?php echo $this->settings_key; ?>[salesforce_base_url]" value="<?php echo $this->get_setting_value( $this->settings, 'salesforce_base_url' ); ?>" />
	    <?php } else { ?>
	    	<p><code>Defined in wp-config.php</code></p>
		<?php }
	}

	function register_salesforce_fieldmaps() {
	    $this->settings_tabs[$this->fieldmaps_key] = 'Fieldmaps';

	    register_setting( $this->fieldmaps_key, $this->fieldmaps_key );
	    add_settings_section( 'section_fieldmaps', 'Fieldmaps', array( &$this, 'section_fieldmaps_desc' ), $this->fieldmaps_key );
	    add_settings_field( 'advanced_option', 'An Advanced Option', array( &$this, 'field_advanced_option' ), $this->fieldmaps_key, 'section_fieldmaps' );
	}

	function section_fieldmaps_desc() {
		echo 'Advanced section description goes here.';
	}

	function field_advanced_option() {
	    ?>
	    <input type="text" name="<?php echo $this->fieldmaps_key; ?>[advanced_option]" value="<?php echo esc_attr( $this->fieldmaps['advanced_option'] ); ?>" />
	    <?php
	}

	/**
	 * Register settings for settings tab
	 *
	 * @since  1.0.0
	 */
	public function register_salesforce_authorize() {
		$this->settings_tabs['authorize'] = 'Authorize';
	}

	

	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function wordpress_salesforce_rest_api_settings() {
		echo '<p>' . __( 'Please change the settings accordingly.', $this->plugin_name ) . '</p>';
	}

	/**
	 * Render the radio input field for position option
	 *
	 * @since  1.0.0
	 */
	public function wordpress_salesforce_rest_salesforce_username() {
		$username = get_option( $this->option_name . '_salesforce_username' );
		echo '<input type="text" name="' . $this->option_name . '_salesforce_username' . '" id="' . $this->option_name . '_salesforce_username' . '" value="' . $username . '">';
	}

	/**
	 * Sanitize the text value before being saved to database
	 *
	 * @param  string $value $_POST value
	 * @since  1.0.0
	 * @return string           Sanitized value
	 */
	public function wordpress_salesforce_rest_sanitize( $value ) {
		if ( in_array( $value, array( 'before', 'after' ), true ) ) {
	        return $value;
	    }
	}

}