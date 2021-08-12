<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Trophy
 * @subpackage Trophy/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Trophy
 * @subpackage Trophy/admin
 * @author     Md trophys <admin@easeare.com>
 */
class Trophy_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version; 
		add_filter( 'sportspress_after_team_template', [$this,'trophys_team_template']);
		add_filter( 'sportspress_after_player_template', [$this,'trophys_player_template']);
		add_image_size( 'trophy_thumb', '140','140', true );

		add_filter('single_template', array($this, 'trophys_page_attributes'));
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/trophys-admin.css', array(), $this->version, 'all' );
		if(get_post_type() == 'trophys'){
			wp_enqueue_style('trophy-select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', '', $this->version, 'all');
		}
	}
	/**
	 * Register the stylesheets for the public area.
	 *
	 * @since    1.0.0
	 */
	public function public_enqueue_styles() {
		wp_enqueue_style('fontawesome', 'https://use.fontawesome.com/releases/v5.8.1/css/all.css', '', '5.8.1', 'all');
		wp_enqueue_style( 'trophy-tabcontents', plugin_dir_url( __FILE__ ) . 'css/trophys-tabcontents.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'vue', plugin_dir_url( __FILE__ ) . 'js/vue.js', array(  ), $this->version, false );
		if(get_post_type() == 'sp_team' || get_post_type() == 'sp_player')
		{
			wp_enqueue_script( 'trophy-metainfo', plugin_dir_url( __FILE__ ) . 'js/meta-info.js', array( 'jquery' ), $this->version, true );
		}
		if(get_post_type() == 'trophys'){
			wp_enqueue_script( 'trophy-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'trophy-spost', plugin_dir_url( __FILE__ ) . 'js/trophys-post.js', array( 'jquery' ), $this->version, true );
		}
	}

	function add_trophy_caps() {
		global $wp_roles;
	
		$role = get_role( 'administrator' );
		$role->add_cap( 'trophys' );
		$role->add_cap( 'edit_trophys' );
		$role->add_cap( 'edit_others_trophys' );
		$role->add_cap( 'delete_others_trophys' );
		$role->add_cap( 'read_private_trophys' );
		$role->add_cap( 'manage_trophys' );
	
		$role = get_role( 'sp_league_manager' );
		$role->add_cap( 'trophys' );
		$role->add_cap( 'edit_trophys' );
		$role->add_cap( 'edit_others_trophys' );
		$role->add_cap( 'delete_others_trophys' );
		$role->add_cap( 'read_private_trophys' );
		$role->add_cap( 'manage_trophys' );
	}

	function custom_script(){
		if(is_single(  )){
			if(get_post_type(  ) == 'sp_team' || get_post_type(  ) == 'sp_player'){
				?>
				<script>
					var trophys_cont = document.getElementById('sp-tab-content-trophys');
					if(trophys_cont.children[0].children[0] == undefined){
						var elements = document.querySelectorAll('.sp-tab-menu-item');
						for(var i = 0; i<elements.length;i++){
							var elementChildren = elements[i].querySelectorAll('[data-sp-tab="trophys"]');
							if(elementChildren[0]){
								elementChildren[0].parentElement.remove()
							}
						}
					}
				</script>
				<?php
				
			}
		}
	}

	public function trophys_page_attributes($template)
    {
        if (is_single(  ) && get_post_type(  ) === 'trophys') {

            if ($theme_file = locate_template(array('_trophys_single.php'))) {
                $template = $theme_file;
            } else {
                $template = dirname(__FILE__) . '/partials/_trophys_single.php';
            }
        }

        if ($template == '') {
            throw new \Exception('No template found');
        }

        return $template;
    }

	function trophys_tables(){
		if(!get_option( 'trophy_table_created' )){
			global $wpdb;
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}trophys_items`" );
			$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}trophys_tab_items`" );

			$trophys_items = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}trophys_items` (
				`ID` INT NOT NULL AUTO_INCREMENT,
				`trophy_id` INT NOT NULL,
				`team_id` INT NOT NULL,
				`player_id` INT NOT NULL,
				PRIMARY KEY (`ID`)) ENGINE = InnoDB";
				dbDelta($trophys_items);
			
			$trophys_tab_items = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}trophys_tab_items` (
				`ID` INT NOT NULL AUTO_INCREMENT,
				`trophy_id` INT NOT NULL,
				`team_id` INT NOT NULL,
				`player_id` INT NOT NULL,
				PRIMARY KEY (`ID`)) ENGINE = InnoDB";
				dbDelta($trophys_tab_items);

			update_option( 'trophy_table_created', 'true' );
		}
		
	}

	function trophys_post_type(){
		$labels = array(
			'name'                  => _x( 'Trophys', 'Trophys', 'trophys' ),
			'singular_name'         => _x( 'Trophys', 'Post type singular name', 'trophys' ),
			'menu_name'             => _x( 'Trophys', 'Admin Menu text', 'trophys' ),
			'name_admin_bar'        => _x( 'Trophys', 'Add New on Toolbar', 'trophys' ),
			'add_new'               => __( 'Add New', 'trophys' ),
			'add_new_item'          => __( 'Add New Trophys', 'trophys' ),
			'new_item'              => __( 'New Trophys', 'trophys' ),
			'edit_item'             => __( 'Edit Trophys', 'trophys' ),
			'view_item'             => __( 'View Trophys', 'trophys' ),
			'all_items'             => __( 'All Trophys', 'trophys' ),
			'search_items'          => __( 'Search Trophys', 'trophys' ),
			'parent_item_colon'     => __( 'Parent Trophys:', 'trophys' ),
			'not_found'             => __( 'No Trophys found.', 'trophys' ),
			'not_found_in_trash'    => __( 'No Trophys found in Trash.', 'trophys' ),
		);
	 
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => true,
			'menu_position'      => 55,
			'supports'           => array( 'title', 'editor','thumbnail' ),
			'menu_icon' 		 => plugin_dir_url( __FILE__ ).'icon.png',
			'map_meta_cap' => true,
			'capabilities' => array(
				'read_posts' => true,
				'edit_post' => true,
				'edit_posts' => 'trophys',
				'published_posts' => 'trophys',
				'edit_published_posts' => 'trophys',
				'edit_others_posts ' => 'trophys'
			),
		);
	 
		register_post_type( 'trophys', $args );

		$this->add_trophy_caps();
	}
	
	function trophys_team_template($data){
		
		$trophys = array(
			'trophys' => array(
				'title' => __( 'Trophy', 'sportspress' ),
				'option' => 'sportspress_team_show_trophys',
				'action' => [$this,'sportspress_output_trophys'],
				'default' => 'yes',
			),
		);

		return array_merge($data, $trophys);
	}

	function trophys_player_template($data){
		$trophys = array(
			'trophys' => array(
				'title' => __( 'Trophy', 'sportspress' ),
				'option' => 'sportspress_player_show_trophys',
				'action' => [$this,'sportspress_output_trophys'],
				'default' => 'yes',
			),
		);
		return array_merge($data, $trophys);
	}
	
	function sportspress_output_trophys(){
		require_once plugin_dir_path( __FILE__ )."partials/trophys-tabcontents.php";
	}

	function trophys_meta_boxes(){

		$screens = ['sp_team','sp_player'];
		foreach($screens as $screen){
			add_meta_box( 'trophys', 'Trophy', [$this,'teamsANDplayers_meta_html'], $screen, 'normal', 'default' );
		}

		add_meta_box( 'teams_post', 'Teams', [$this,'trophys_teams_post_meta_html'], 'trophys', 'side', 'default' );

		add_meta_box( 'players_post', 'Players', [$this,'trophys_players_post_meta_html'], 'trophys', 'side', 'default' );

		add_meta_box( 'date_obtained', 'Date Obtained', [$this,'trophys_date_obtained_meta_html'], 'trophys', 'side', 'default' );

		add_meta_box( 'reference', 'Reference Link', [$this,'trophys_reference_meta_html'], 'trophys', 'side', 'default' );

		add_meta_box( 'verification_statutes', 'Verification status', [$this,'trophys_verification_statutes_meta_html'], 'trophys', 'side', 'default' );

		
	}

	function trophys_teams_post_meta_html(){
		global $wpdb;
		$post_id = get_post()->ID;

		$args = [
			'post_type' => 'sp_team',
			'post_status' => 'publish',
  			'numberposts' => -1,
  			'order' => 'ASC',
  			'orderby' => 'date'
		];

		$teams = get_posts($args);

		if($teams){
			echo '<select name="teams_posts_for[]" class="widefat" multiple id="teams_posts2">';

			$teams_id = [];
			$savedItems = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}trophys_items WHERE trophy_id = $post_id");
			if($savedItems){
				foreach($savedItems as $id){
					$teams_id[] = $id->team_id;
				}
			}

			foreach($teams as $team){
				if(in_array($team->ID, $teams_id)){
					echo '<option selected value="'.$team->ID.'">'.$team->post_title.'</option>';
				}else{
					echo '<option value="'.$team->ID.'">'.$team->post_title.'</option>';
				}
			}
			echo '</select>';
		}
	}

	function trophys_players_post_meta_html(){
		global $wpdb;
		$post_id = get_post()->ID;

		$args = [
			'post_type' => 'sp_player',
			'post_status' => 'publish',
  			'numberposts' => -1,
  			'order' => 'ASC',
  			'orderby' => 'date'
		];

		$players = get_posts($args);

		if($players){
			echo '<select name="players_posts_for[]" class="widefat" multiple id="players_posts2">';

			$players_id = [];
			$savedItems = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}trophys_items WHERE trophy_id = $post_id");
			if($savedItems){
				foreach($savedItems as $id){
					$players_id[] = $id->player_id;
				}
			}

			foreach($players as $player){
				if(in_array($player->ID, $players_id)){
					echo '<option selected value="'.$player->ID.'">'.$player->post_title.'</option>';
				}else{
					echo '<option value="'.$player->ID.'">'.$player->post_title.'</option>';
				}
			}

			echo '</select>';
		}
	}

	function trophys_date_obtained_meta_html(){
		echo '<input value="'.get_post_meta( get_post()->ID, 'date_obtained', true ).'" class="widefat" type="date" name="date_obtained">';
	}

	function trophys_reference_meta_html(){
		echo '<input value="'.get_post_meta( get_post()->ID, 'reference_meta', true ).'" class="widefat" type="url" name="reference_meta">';
	}
	
	function trophys_verification_statutes_meta_html(){
		$post_id = get_post()->ID;
		$verified = 0;
		if(get_post_meta( $post_id, 'verified', true )){
			$verified = get_post_meta( $post_id, 'verified', true );
		}

		echo '<div class="verifybox">';
		echo '<label class="'.($verified == 1?'vactive':'').'" for="verified2">Verified';
		echo '<input '.($verified == 1?'checked':'').' value="1" type="radio" name="verified" id="verified2">';
		echo '</label>';
		echo '<label class="'.($verified == 2?'vactive':'').'" for="vpending2">Pending';
		echo '<input '.($verified == 2?'checked':'').' value="2" type="radio" name="verified" id="vpending2">';
		echo '</label>';
		echo '<label class="'.($verified == 0?'vactive':'').'" for="unverified2">Unverified';
		echo '<input '.($verified == 0?'checked':'').' value="0" type="radio" name="verified" id="unverified2">';
		echo '</label>';
		echo '</div>';
	}
	
	function teamsANDplayers_meta_html(){
		global $post;
		$args = [
			'post_type' => 'trophys',
			'post_status' => 'publish',
  			'numberposts' => -1,
  			'order' => 'ASC',
  			'orderby' => 'date',
		];

		$trophys = get_posts($args);
		if($trophys){

			?>
			<input type="hidden" name="junus_module" value="1">
			<table id="trophy_table2">
				<thead>
					<tr>
						<th>
							<input @change="all_trophys_select(event)" type="checkbox" name="trophys_all">Trophy List
						</th>
					</tr>
				</thead>

				<tbody>
					<?php
					global $wpdb;

					$post_id = get_post()->ID;
							
					$teams_id = [];
					$savedTeams = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}trophys_items WHERE team_id = $post_id");
					if($savedTeams){
						foreach($savedTeams as $id){
							$teams_id[] = $id->trophy_id;
						}
					}

					$players_id = [];
					$savedPlayers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}trophys_items WHERE player_id = $post_id");
					if($savedPlayers){
						foreach($savedPlayers as $id){
							$players_id[] = $id->trophy_id;
						}
					}

					$nofound = '<tr><td>No results found.</td></tr>';
					$nofoundCon = true;
					foreach($trophys as $trophy){
						if(get_post_type(  ) == 'sp_team'){
							if(in_array($trophy->ID, $teams_id)){
								echo 	'<tr>';
								echo 	'<td>';

								$checked = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}trophys_tab_items WHERE team_id = $post_id AND trophy_id = $trophy->ID");

								if($checked){
									$checked = 'checked';
									$value = $trophy->ID;
								}else{
									$checked = '';
									$value = '';
								}

								echo 	'<input '.$checked.' class="singletrophy" @change="single_trophy_select(event)" value="'.$value.'" type="checkbox" data="'.$trophy->ID.'" name="trophys[]">';
								echo 	__($trophy->post_title,$this->plugin_name);

								echo 	'</td>';
								echo 	'</tr>';
								$nofoundCon = false;
							}else{
								if($nofoundCon){
									echo $nofound;
									$nofound = '';
								}
							}
						}
						
						if(get_post_type(  ) == 'sp_player'){
							if(in_array($trophy->ID, $players_id)){
								echo 	'<tr>';
								echo 	'<td>';

								$checked = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}trophys_tab_items WHERE player_id = $post_id AND trophy_id = $trophy->ID");

								if($checked){
									$checked = 'checked';
									$value = $trophy->ID;
								}else{
									$checked = '';
									$value = '';
								}

								echo 	'<input '.$checked.' class="singletrophy" @change="single_trophy_select(event)" value="'.$value.'" type="checkbox" data="'.$trophy->ID.'" name="trophys[]">';
								echo 	__($trophy->post_title, $this->plugin_name);

								echo 	'</td>';
								echo 	'</tr>';
								$nofoundCon = false;
							}else{
								if($nofoundCon){
									echo $nofound;
									$nofound = '';
								}
							}
						}
					}
					?>
				</tbody>
			</table>
			<?php
		}
	}

	function save_tabs_meta_data($post_id, $post){

		global $wpdb;
		if(get_post_type(  ) == 'sp_team'){
			if(isset($_POST['junus_module'])){
				$wpdb->query("DELETE FROM {$wpdb->prefix}trophys_tab_items WHERE team_id = $post_id");
			
				if(is_array($_POST['trophys'])){
					foreach($_POST['trophys'] as $trophyId){
	
						if(!$wpdb->get_var("SELECT ID FROM {$wpdb->prefix}trophys_tab_items WHERE team_id = $post_id AND trophy_id = $trophyId")){
							$wpdb->insert($wpdb->prefix.'trophys_tab_items',array(
								'trophy_id' => $trophyId,
								'team_id' => $post_id,
							),array('%d','%d'));
						}
	
					}
				}
			}
		}

		if(get_post_type(  ) == 'sp_player'){
			if(isset($_POST['junus_module'])){
				$wpdb->query("DELETE FROM {$wpdb->prefix}trophys_tab_items WHERE player_id = $post_id");
				
				if(is_array($_POST['trophys'])){
					foreach($_POST['trophys'] as $trophyId){
						
						if(!$wpdb->get_var("SELECT ID FROM {$wpdb->prefix}trophys_tab_items WHERE player_id = $post_id AND trophy_id = $trophyId")){
							$wpdb->insert($wpdb->prefix.'trophys_tab_items',array(
								'trophy_id' => $trophyId,
								'player_id' => $post_id,
							),array('%d','%d'));
						}

					}
				}
			}
		}
	}

	function save_trophys_meta_data($post_id, $post){
		if(isset($_POST['original_publish'])){
			global $wpdb;
			$wpdb->query("DELETE FROM {$wpdb->prefix}trophys_items WHERE trophy_id = $post_id");
			
			if(is_array($_POST['teams_posts_for'])){
				foreach($_POST['teams_posts_for'] as $team){

					if(!$wpdb->get_var("SELECT ID FROM {$wpdb->prefix}trophys_items WHERE team_id = $team AND trophy_id = $post_id")){
						$wpdb->insert($wpdb->prefix.'trophys_items',array(
							'trophy_id' => $post_id,
							'team_id' => $team,
						),array('%d','%d'));
					}

				}
			}

			if(is_array($_POST['players_posts_for'])){
				foreach($_POST['players_posts_for'] as $player){

					if(!$wpdb->get_var("SELECT ID FROM {$wpdb->prefix}trophys_items WHERE player_id = $player AND trophy_id = $post_id")){
						$wpdb->insert($wpdb->prefix.'trophys_items',array(
							'trophy_id' => $post_id,
							'player_id' => $player,
						),array('%d','%d'));
					}

				}
			}

			if(isset($_POST['date_obtained'])){
				update_post_meta( $post_id, 'date_obtained', $_POST['date_obtained'] );
			}

			if(isset($_POST['reference_meta'])){
				$texts = sanitize_text_field( $_POST['reference_meta'] );
				update_post_meta( $post_id, 'reference_meta', $texts );
			}
			
			if(isset($_POST['verified'])){
				update_post_meta( $post_id, 'verified', intval($_POST['verified']) );
			}
		}
	}
}
