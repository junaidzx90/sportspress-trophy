<?php get_header(); ?>
<?php
global $post,$wpdb;
$post_id = get_post()->ID;
?>
<div class="trophy-container">
    <div class="trophy_post">
        <h1 class="ptitle"><?php echo $post->post_title; ?></h1>
        <div class="feature_image_wrap">
            <?php echo get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'feature_image' ) ); ?>
        </div>
        <div class="contents">
            <p class="trophy_pcontent">
            <?php
                echo $post->post_content;
            ?>
            </p>
        </div>
        <div class="feature-contents">
            <h5 class="single_date_obtained">
                Obtained date: <i><?php echo get_post_meta($post_id, 'date_obtained', true); ?></i>
            </h5>
            <div class="tablink">
                <span>This certificate is awarded to: </span>
                <?php
                
                $link_ids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}trophys_items WHERE trophy_id = $post_id");
                
                $tabIds = [];

                if($link_ids){
                    foreach($link_ids as $lid){
                        if($lid->team_id > 0){
                            $tabIds[] = $lid->team_id;
                        }
                    }
                    foreach($link_ids as $lid){
                        if($lid->player_id > 0){
                            $tabIds[] = $lid->player_id;
                        }
                    }
                }
                $sep = '';
                foreach($tabIds as $link_id){
                    echo $sep.'<a href="'.get_the_permalink( $link_id ).'">'.ucfirst(get_the_title( $link_id )).'</a>';
                    $sep = ',&nbsp;';
                }
                
                ?>
            </div>
            <p class="single_reference">
                <a href="<?php echo get_post_meta($post_id, 'reference_meta', true); ?>">Reference link</a>
            </p>
            <div class="verified_status">
                <?php
                switch (intval(get_post_meta($post_id, 'verified', true))) {
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
</div>
<?php
get_footer(); ?>