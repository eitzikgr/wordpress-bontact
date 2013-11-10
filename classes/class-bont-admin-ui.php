<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class BONT_Admin_UI {
	
	public $_admin_pages = array();
	
	protected $_iframe_panel = 'https://dashboard.bontact.com/html/frame_ds.aspx?username=%s&pass=%s&ref=wp&page=%s';
	
	public function admin_init() {
		global $bontact_main_class;
		$username = $bontact_main_class->settings->get_option( 'username' );
		$password = $bontact_main_class->settings->get_option( 'password' );

		if ( empty( $_GET['page'] ) || 'bontact-settings' === $_GET['page'] )
			return;

		if ( empty( $username ) || empty( $password ) ) {
			wp_redirect( admin_url( 'admin.php?page=bontact-settings' ) );
			die();
		}
	}
	
	public function admin_menu() {
		foreach ( $this->_admin_pages as $page_id => $page_title ) {
			add_submenu_page( 'bontact-settings', $page_title, $page_title, 'manage_options', 'bont-' . $page_id, array( &$this, 'render_page' ) );
		}

		global $submenu;
		if ( isset( $submenu['bontact-settings'] ) )
			$submenu['bontact-settings'][0][0] = __( 'General Settings', 'bontact' );
	}
	
	public function render_page() {
		global $bontact_main_class;
		$username = $bontact_main_class->settings->get_option( 'username' );
		$password = $bontact_main_class->settings->get_option( 'password' );
		
		if ( empty( $_GET['page'] ) )
			return;
		
		if ( empty( $username ) || empty( $password ) )
			wp_redirect( admin_url( 'admin.php?page=bontact-settings' ) );
		
		$current_page = str_replace( 'bont-', '', $_GET['page'] );
		if ( ! isset( $this->_admin_pages[ $current_page ] ) )
			return;
		?>
		<div class="wrap">
			<iframe src="<?php echo sprintf( $this->_iframe_panel, urlencode( $username ), urlencode( $password ), $current_page ); ?>" class="bont-admin-embed"></iframe>
		</div>
		<?php
	}
	
	public function admin_print_scripts() {
		wp_enqueue_style( 'bont-admin-ui', plugins_url( '/assets/css/admin-ui.min.css', BONTACT_BASE_FILE ) );
	}
	
	public function __construct() {		
		$this->_admin_pages = array(
			// id => title
			'chatdashboard' => __( 'Chat Dashboard', 'bontact' ),
			'connectionsettings' => __( 'Widget Features', 'bontact' ),
			'widgetsettings' => __( 'Widget Settings', 'bontact' ),
			'myprofile' => __( 'My Profile', 'bontact' ),
			'companyinfo' => __( 'Company Info', 'bontact' ),
			'accounthistory' => __( 'Account History', 'bontact' ),
			'manageuser' => __( 'Manage Agents', 'bontact' ),
			'managedepartments' => __( 'Manage Departments', 'bontact' ),
			'sounds' => __( 'Sound Settings', 'bontact' ),
			'sentencesandlinks' => __( 'Sentences & Links', 'bontact' ),
			'packages' => __( 'Upgrade & Packages', 'bontact' ),
		);
		add_action( 'admin_init', array( &$this, 'admin_init' ), 50 );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 50 );
		add_action( 'admin_print_scripts', array( &$this, 'admin_print_scripts' ) );
	}
	
}