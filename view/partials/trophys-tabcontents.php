<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Trophy
 * @subpackage Trophy/admin/partials
 */
?>
<div class="spcontainer" >
<?php
global $wpdb;
$tab_id = get_post()->ID;
$selected_items = [];
if(get_post_type(  ) == 'sp_team'){
    $items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}trophys_tab_items WHERE team_id = $tab_id");
    if($items){
        foreach($items as $item){
            $selected_items[] = intval($item->trophy_id);
        }
    }
}

if(get_post_type(  ) == 'sp_player'){
    $items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}trophys_tab_items WHERE player_id = $tab_id");
    if($items){
        foreach($items as $item){
            $selected_items[] = intval($item->trophy_id);
        }
    }
}

$args = [
    'post_type' => 'trophys',
    'post_status' => 'publish',
    'numberposts' => -1,
    'order' => 'ASC',
    'orderby' => 'date',
];

$trophys = get_posts($args);

if ($trophys) {
    foreach ($trophys as $trophy) {
        if(in_array($trophy->ID, $selected_items)){
            ?>
            <div class="trophy_item">
                <div class="image">
                    <?php echo get_the_post_thumbnail( $trophy->ID, 'trophy_thumb', array( 'class' => 'trophy_thumb' ) ); ?>
                </div>
                <div class="pcontent">
                    <h3 class="ptitle"><a target="_junu" href="<?php echo get_permalink( $trophy ); ?>?sp=<?php echo str_replace('sp_','',get_post_type(  )).'-'.$tab_id ?>"><?php echo $trophy->post_title; ?></a></h3>
                    
                    <div class="date_obtained">
                        <strong>Obtained date: </strong>
                        <i><?php echo get_post_meta($trophy->ID, 'date_obtained', true); ?></i>
                    </div>
                    <div class="verified">
                        <strong>Verification status: </strong>
                        <?php 
                            switch (intval(get_post_meta($trophy->ID, 'verified', true))) {
                                case 0:
                                    echo '&nbsp;<span class="unverified">Unverified</span>';
                                    break;
                                
                                case 1:
                                    echo '&nbsp;<span class="verified">Verified</span>';
                                    break;
                                
                                case 2:
                                    echo '&nbsp;<span class="vpending">Pending</span>';
                                    break;
                                
                                default:
                                    # code...
                                    break;
                            }
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}

?>
</div>