<?php
/**
 * Bimbler Members
 *
 * @package   Bimbler_Members
 * @author    Paul Perkins <paul@paulperkins.net>
 * @license   GPL-2.0+
 * @link      http://www.paulperkins.net
 * @copyright 2014 Paul Perkins
 */

/**
 * Include dependencies necessary... (none at present)
 *
 */

/**
 * Bimbler Users
 *
 * @package Bimbler_Members
 * @author  Paul Perkins <paul@paulperkins.net>
 */
class Bimbler_Members {

        /*--------------------------------------------*
         * Constructor
         *--------------------------------------------*/

        /**
         * Instance of this class.
         *
         * @since    1.0.0
         *
         * @var      object
         */
        protected static $instance = null;

        /**
         * Return an instance of this class.
         *
         * @since     1.0.0
         *
         * @return    object    A single instance of this class.
         */
        public static function get_instance() {

                // If the single instance hasn't been set, set it now.
                if ( null == self::$instance ) {
                        self::$instance = new self;
                } // end if

                return self::$instance;

        } // end get_instance

        /**
         * Initializes the plugin by setting localization, admin styles, and content filters.
         */
        private function __construct() {

        	
        	add_action ('wp_enqueue_scripts', array ($this, 'enqueue_bootstrap_scripts'));
        	
        	add_shortcode( 'bimbler_show_members', array ($this, 'show_members'));
        	        	        	         	
		} // End constructor.
		
		private $script = '<script type="text/javascript">
var responsiveHelper;
var breakpointDefinition = {
    tablet: 1024,
    phone : 480
};
var tableContainer;

	jQuery(document).ready(function($)
	{
		tableContainer = $("#table-1");
		
		var thing = tableContainer.dataTable({
			"sPaginationType": "bootstrap",
			"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"bStateSave": true,

		    // Responsive Settings
		    bAutoWidth     : false,
		    fnPreDrawCallback: function () {
		        // Initialize the responsive datatables helper once.
		        if (!responsiveHelper) {
		            responsiveHelper = new ResponsiveDatatablesHelper(tableContainer, breakpointDefinition);
		        }
		    },
		    fnRowCallback  : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
		        responsiveHelper.createExpandIcon(nRow);
		    },
		    fnDrawCallback : function (oSettings) {
		        responsiveHelper.respond();
		    }
		});
				
		thing.columnFilter({
			"sPlaceHolder" : "head:after"
		});
				
		$(".dataTables_wrapper select").select2({
			minimumResultsForSearch: -1
		});
				
		$("#table-1").dataTable( {
	        "order": [[ 3, "desc" ]]
   		 } );				
				
	});
				
				
</script>';
		
	private $script_bot = '	<script src="/wp-content/plugins/bimbler-members/assets/js/jquery.dataTables.min.js"></script>
	<script src="/wp-content/plugins/bimbler-members/assets/js/datatables/TableTools.min.js"></script>
	<script src="/wp-content/plugins/bimbler-members/assets/js/dataTables.bootstrap.js"></script>
	<script src="/wp-content/plugins/bimbler-members/assets/js/datatables/jquery.dataTables.columnFilter.js"></script>
	<script src="/wp-content/plugins/bimbler-members/assets/js/datatables/lodash.min.js"></script>
	<script src="/wp-content/plugins/bimbler-members/assets/js/datatables/responsive/js/datatables.responsive.js"></script>';
		
	function enqueue_bootstrap_scripts () {
		wp_register_style( 'style-datatables', plugins_url('data-tables.css', __FILE__) );
		wp_enqueue_style( 'style-datatables' );
		
		wp_register_style( 'style-entypo', plugins_url('entypo.css', __FILE__) );
		wp_enqueue_style( 'style-entypo' );
	}
		
	function get_users () {
		global $wpdb;
			
		//$table_name = $wpdb->base_prefix . $rsvp_db_table;
			
		$sql =  'SELECT u.id as uid, ';
		$sql .= ' u.user_registered as reg_date ';
		$sql .= " FROM {$wpdb->users} u, ";
		$sql .= " {$wpdb->usermeta} m1 ";
		$sql .= ' WHERE u.id = m1.user_id ';
		$sql .= ' AND m1.meta_key = \'wp_capabilities\' ';
		$sql .= ' AND m1.meta_value NOT LIKE \'%unverified%\' ';
		$sql .= ' ORDER BY u.user_registered DESC';
		
		$users = $wpdb->get_results ($sql);
		
		if (!isset ($users)) {
			//echo 'Cannot get user list.';
			echo $wpdb->print_error ();
		}
		
		return $users;
	}
	
	/*
	 * 
	 *
	 */
	function show_members($atts) {
		
		//global $post;
		
/*		$a = shortcode_atts (array (
								'ahead' 	=> 7,
								'send_mail' => 'Y',
							), $atts);
		
		if (!isset ($a)) {
			error_log ('send_reminder called with no interval set.');
			return;
		} */
		
		$content = '';
		
		$content .= '<table class="table table-bordered table-striped xdatatable" id="table-1">';
		
		$content .= '	<thead>';
/*		$content .= '	<tr class="replace-inputs">';
		$content .= '		<th></th>';
		$content .= '		<th>Name</th>';
		$content .= '		<th data-hide="phone,tablet">Nick Name</th>';
		$content .= '		<th>Joined</th>';
		$content .= '	</tr>'; */ 
		$content .= '	<tr>';
		$content .= '		<th></th>';
		$content .= '		<th>Name</th>';
		$content .= '		<th data-hide="phone,tablet">Nick Name</th>';
		$content .= '		<th>Joined</th>';
		$content .= '	</tr>'; 
		$content .= '	</thead>';
		$content .= '	<tbody>';
		
		$odd = true;
		
		if ( !is_user_logged_in() ){
			$content = '<div class="bimbler-alert-box notice"><span>Notice: </span>You must be logged-in to view this page.</div>';
		
			return $content;
		}
		
		
		$users = $this->get_users();
		
		if (!isset ($users)) {
			return "Error";
		}

		foreach ( $users as $user) {
			$user_info   = get_userdata ($user->uid);
			$username = $user_info->user_login;
			$user_person = $user_info->user_firstname . ' ' . $user_info->user_lastname;
			$registered = $user->reg_date;
			$avatar = get_avatar ($user->uid, $size='150');
			

			$content .= '<tr class="';
			if ($odd) {
				$content .= ' odd';
			} else {
				$content .= ' even';
			}

			$content .= ' grade A">';
			
			$content .= '<td><div class="bimbler-avatar-medium text-center">' . $avatar . '</div></td>';
			$content .= '<td>' . $user_person . '</td>';
			$content .= '<td>' . $user_info->nickname . '</td>';
			$content .= '<td>' . $registered . '</td>';
				
			$content .= '</tr>';

			if ($odd) {
				$odd = false;
			} else {
				$odd = true;
			}
		}
		
		$content .= '	</tbody>';
		$content .= '</table>';
		
		$content .= $this->script;
		$content .= $this->script_bot;
		
		return $content;
				
	}
					
} // End class
