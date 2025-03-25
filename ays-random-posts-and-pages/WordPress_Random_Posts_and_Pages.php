<?php
ob_start();
/*
Plugin Name:    Random Posts and Pages
Plugin URI:     https://ays-pro.com/
Description:    The main advantage of this widget is random movement of random links and every time they are changing.
Version:        2.6.0
Author:         Random posts Team
Author          URI: https://ays-pro.com/
License:        GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
Text Domain:    ays-random-posts-and-pages
Domain Path:    /languages
*/
if( ! defined( 'AYS_RANDOM_ADMIN_URL' ) )
    define( 'AYS_RANDOM_ADMIN_URL', plugin_dir_url( __FILE__ ));

class Random_Posts_and_Pages extends WP_Widget {

    function __construct() {
        parent::__construct(
        // Base ID of your widget
        'Random_Posts_and_Pages', 

        // Widget name will appear in UI
        __('Random Posts and Pages widget', 'ays-random-posts-and-pages'), 

        // Widget description
        array( 'description' => __( 'The main advantage of this widget is random movement of random links and every time they are changing.', 'ays-random-posts-and-pages' ), ) 
        );
    }

    // Creating widget front-end
    // This is where the action happens
    public function widget( $args, $instance ) {
        
        $unique_id = apply_filters( 'unique_id', $instance['unique_id'] );
        $title = apply_filters( 'ays_title', $instance['title'] );
        $ays_animation_type = apply_filters( 'ays_animation_type', $instance['ays_animation_type'] );
        $ays_select_post_type = (isset( $instance['select_post_type'] ) && $instance['select_post_type'] !== '') ? apply_filters( 'ays_select_post_type', $instance['select_post_type']) : 'post';
        // Levons add start
        $ays_select_post_category = (!isset($instance['select_post_category']) || $instance['select_post_category'] === false) ? false : apply_filters( 'ays_select_post_category', $instance['select_post_category']);
        // Levons add end
        $ays_width = apply_filters( 'ays_width', $instance['ays_width'] );
        $ays_height = apply_filters( 'ays_height', $instance['ays_height'] );
        $ays_box_background = apply_filters( 'ays_box_background', $instance['ays_box_background'] );
        $ays_box_border_color = apply_filters( 'ays_box_border_color', $instance['ays_box_border_color'] );
        $ays_box_border_thickness = apply_filters('ays_box_border_thickness', $instance['ays_box_border_thickness']);    
        $ays_box_border_radius = apply_filters( 'ays_box_border_radius', $instance['ays_box_border_radius'] );
        $ays_box_shadow = apply_filters( 'ays_box_shadow', $instance['ays_box_shadow'] );  
        $ays_count_links = apply_filters( 'ays_count_links', $instance['ays_count_links'] );
        $ays_count_letters = apply_filters( 'ays_count_letters', $instance['ays_count_letters'] );

        $ays_link_background = apply_filters( 'ays_link_background', $instance['ays_link_background'] );
        $ays_background_image = apply_filters( 'ays_background_image', $instance['ays_background_image'] );
        $ays_link_color = apply_filters( 'ays_link_color', $instance['ays_link_color'] );
        $ays_link_padding = apply_filters( 'ays_link_padding', $instance['ays_link_padding'] );
        $ays_link_border_radius = apply_filters( 'ays_link_border_radius', $instance['ays_link_border_radius'] );
        $ays_link_font = apply_filters( 'ays_link_font', $instance['ays_link_font'] );
        $ays_animate_speed = apply_filters( 'ays_animate_speed', $instance['ays_animate_speed'] );
        $ays_link_hover_img_display = apply_filters( 'ays_link_hover_img_display', $instance['ays_link_hover_img_display'] );
        $ays_link_hover_img_height = apply_filters( 'ays_link_hover_img_height', $instance['ays_link_hover_img_height'] );
        $ays_link_hover_img_size = apply_filters( 'ays_link_hover_img_size', $instance['ays_link_hover_img_size'] );
        $ays_link_hover_bg = apply_filters( 'ays_link_hover_bg', $instance['ays_link_hover_bg'] );
        $ays_link_hover_bg_trans = null;
        if(isset($instance['ays_link_hover_bg_trans'])){
            $ays_link_hover_bg_trans = apply_filters( 'ays_link_hover_bg_trans', $instance['ays_link_hover_bg_trans'] );
        }

        $ays_link_hover_color = apply_filters( 'ays_link_hover_color', $instance['ays_link_hover_color'] );
        $ays_link_hover_border = apply_filters( 'ays_link_hover_border', $instance['ays_link_hover_border'] );
        $ays_rp_same_type = (isset($instance['ays_rp_same_type']) && $instance['ays_rp_same_type'] != "") ? apply_filters( 'ays_rp_same_type', $instance['ays_rp_same_type'] ) : 'off';
        $ays_hide_for_mobile_device = (isset($instance['ays_hide_for_mobile_device']) && $instance['ays_hide_for_mobile_device'] != "") ? apply_filters( 'ays_hide_for_mobile_device', $instance['ays_hide_for_mobile_device'] ) : 'off';
            
        global $wpdb;
            
        if($ays_select_post_category == false){
            $ays_post_category = array();
            $ays_post_category_query = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "term_taxonomy WHERE " . $wpdb->prefix . "term_taxonomy.taxonomy = 'category'");
            foreach ($ays_post_category_query as $ays_post_cat) {
                $ays_post_category[] = " ". $ays_post_cat->term_id ." ";
            }
        }else{
            $ays_post_category = explode("***", $ays_select_post_category);
        }
        $ays_post_category = implode(", ", $ays_post_category);
        // get random posts and pages
        $ays_select_post_type = explode("***", $ays_select_post_type);
        $ays_array = array();
        foreach ($ays_select_post_type as $ays_post_types) {
            $ays_array[] = " '". $ays_post_types ."' ";
        }

        $rand_posts = array();
        $current_post_type = get_post_type();
        $category = $ays_post_category;
        if($current_post_type != "post"){
            $category = "";
        }

        $current_post_id = get_the_ID();

        $current_post_id_sql = '';
        if($current_post_id !== null && $current_post_id !== false){
            $current_post_id_sql = " AND " . $wpdb->prefix . "posts.ID != ".$current_post_id."";
        }else{
             $current_post_id_sql = '';
        }

        if($ays_rp_same_type == "on"){
            $rand_posts = get_posts(array(
                    'numberposts' => $ays_count_links,
                    'category'    => $category,
                    "post_type"   => $current_post_type,
                    'orderby'     => "rand",
                    'exclude'     => $current_post_id,
                    'post_status' => "publish"
                )
            );
        }
        else{
            if(in_array(" 'page' ", $ays_array)){
                $rand_posts1 = $wpdb->get_results("SELECT " . $wpdb->prefix . "posts.ID, " . $wpdb->prefix . "posts.post_title, " . $wpdb->prefix . "posts.post_type
            FROM " . $wpdb->prefix . "posts WHERE " . $wpdb->prefix . "posts.post_type = 'page'  AND " . $wpdb->prefix . "posts.post_status = 'publish' ".$current_post_id_sql."
            ORDER BY rand() LIMIT ".$ays_count_links." ");
            }
    
            $ays_array = implode(", ", $ays_array);
                // Levons add start
            $rand_posts = $wpdb->get_results("SELECT " . $wpdb->prefix . "posts.ID, " . $wpdb->prefix . "posts.post_title, " . $wpdb->prefix . "posts.post_type
            FROM " . $wpdb->prefix . "posts
            LEFT JOIN " . $wpdb->prefix . "term_relationships ON (" . $wpdb->prefix . "posts.ID = " . $wpdb->prefix . "term_relationships.object_id)
            LEFT JOIN " . $wpdb->prefix . "term_taxonomy ON (" . $wpdb->prefix . "term_relationships.term_taxonomy_id = " . $wpdb->prefix . "term_taxonomy.term_taxonomy_id)
            WHERE " . $wpdb->prefix . "posts.post_type IN ( ".$ays_array." ) AND " . $wpdb->prefix . "term_taxonomy.term_id IN ($ays_post_category)  AND " . $wpdb->prefix . "posts.post_status = 'publish' ".$current_post_id_sql."
            GROUP BY " . $wpdb->prefix . "posts.ID
            ORDER BY rand()
            LIMIT ".$ays_count_links." ");
            
            if(isset($rand_posts1) ){
                $rand_post = array_merge($rand_posts,$rand_posts1);
                shuffle($rand_post);
                $rand_posts = array_slice($rand_post, 0, intval($ays_count_links), true);
            }

        }
            // Levons add end
        wp_enqueue_style( 'ays_randposts_animate.css', plugins_url( 'css/animate.css', __FILE__ ) );
        wp_enqueue_script('jquery');
        wp_enqueue_script( 'jquery-core-effects' );
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        $color_prefix="#";
        if($ays_hide_for_mobile_device == 'on' && wp_is_mobile()){
            echo 'You will not able to see the widget on mobile devices';
        }else{
            if ( ! empty( $title ) )
                echo $args['before_title'] . $title . $args['after_title'];
            echo "<div class='aysdiv_container_".$unique_id."'>";
                foreach($rand_posts as $ays_post)
                {
                    $title_len=strlen($ays_post->post_title);
                    if($title_len>$ays_count_letters)
                    {
                        $innerlink=substr($ays_post->post_title, 0, $ays_count_letters).'...';
                    }
                    else
                    {
                        $innerlink=$ays_post->post_title;
                    }
                    $href = get_permalink($ays_post->ID);
                    if(get_the_post_thumbnail_url($ays_post) != false && $ays_link_hover_img_display == 'hover_image'){            
                        echo "<a href='".$href."' data_title='".htmlspecialchars($ays_post->post_title)."' class='ayslink_serial_".$unique_id."'>".$innerlink."";
                        echo "<img class='rand_post_and_pages_f_image_".$unique_id."' src='".get_the_post_thumbnail_url($ays_post)."'>";
                        echo "</a>";
                    }elseif(get_the_post_thumbnail_url($ays_post) != false && $ays_link_hover_img_display == 'opened_image'){
                        echo "<a href='".$href."' data_title='".htmlspecialchars($ays_post->post_title)."' class='ayslink_serial_".$unique_id."'>".$innerlink."";
                        echo "<img style='display:block;' class='rand_post_and_pages_f_image_".$unique_id."' src='".get_the_post_thumbnail_url($ays_post)."'>";
                        echo "</a>";
                    }else{
                        echo "<a href='".$href."' data_title='".htmlspecialchars($ays_post->post_title)."' class='ayslink_serial_".$unique_id."'>".$innerlink."</a>";
                    }
                }
                echo "</div>";

            if(strpos($ays_box_border_color, $color_prefix) === false && strpos($ays_box_border_color, 'rgba') === false ){
                $ays_box_border_color = $color_prefix . $ays_box_border_color;  
            }
            if(strpos($ays_box_background, $color_prefix) === false && strpos($ays_box_background, 'rgba') === false){
                $ays_box_background = $color_prefix . $ays_box_background;
            }
            if(strpos($ays_link_background, $color_prefix) === false && strpos($ays_link_background, 'rgba') === false){
                $ays_link_background = $color_prefix . $ays_link_background;
            }
            if(strpos($ays_link_color, $color_prefix) === false && strpos($ays_link_color, 'rgba') === false){
                $ays_link_color = $color_prefix . $ays_link_color;
            }
            if(strpos($ays_link_hover_bg, $color_prefix) === false && strpos($ays_link_hover_bg, 'rgba') === false ){
                $ays_link_hover_bg = $color_prefix . $ays_link_hover_bg;
            }
            if(strpos($ays_link_hover_color, $color_prefix) === false && strpos($ays_link_hover_color, 'rgba') === false){
                $ays_link_hover_color = $color_prefix . $ays_link_hover_color;
            }
            if(strpos($ays_link_hover_border, $color_prefix) === false && strpos($ays_link_hover_border, 'rgba') === false){
                $ays_link_hover_border = $color_prefix . $ays_link_hover_border;
            }
        }


        ?>
            <style>
                div.aysdiv_container_<?php echo $unique_id; ?> {
                    height:<?php 
                                if(strpos($ays_height, '%') !== false ||
                                strpos($ays_height, 'px') !== false ||
                                strpos($ays_height, 'pt') !== false ||
                                strpos($ays_height, 'rem') !== false ||
                                strpos($ays_height, 'em') !== false){
                                    echo $ays_height;
                                }else{
                                    echo $ays_height.'px';
                                }
                        ?>;
                    width: <?php 
                                if($ays_width == ''){ 
                                    echo '100%'; 
                                }else{ 
                                    if(strpos($ays_width, '%') !== false ||
                                    strpos($ays_width, 'px') !== false ||
                                    strpos($ays_width, 'pt') !== false ||
                                    strpos($ays_width, 'rem') !== false ||
                                    strpos($ays_width, 'em') !== false){
                                        echo $ays_width;
                                    }else{
                                        echo $ays_width.'px';
                                    }
                                } ?>;
                    border:<?php
                                    if(strpos($ays_box_border_thickness, '%') !== false ||
                                    strpos($ays_box_border_thickness, 'px') !== false ||
                                    strpos($ays_box_border_thickness, 'pt') !== false ||
                                    strpos($ays_box_border_thickness, 'rem') !== false ||
                                    strpos($ays_box_border_thickness, 'em') !== false){
                                        echo $ays_box_border_thickness;
                                    }else{
                                        echo $ays_box_border_thickness.'px';
                                    }
                                ?> solid <?php echo $ays_box_border_color; ?>;
                    background-color:<?php echo $ays_box_background; ?>;
                    background-image:url('<?php echo $ays_background_image; ?>');
                    background-size: cover;
                    background-repeat:no-repeat;
                    background-position:center;
                    border-radius: <?php
                                    $ays_box_border_radius = explode(' ', $ays_box_border_radius);
                                    for($i=0; $i<count($ays_box_border_radius); $i++){
                                        if(strpos($ays_box_border_radius[$i], '%') !== false ||
                                        strpos($ays_box_border_radius[$i], 'px') !== false ||
                                        strpos($ays_box_border_radius[$i], 'pt') !== false ||
                                        strpos($ays_box_border_radius[$i], 'rem') !== false ||
                                        strpos($ays_box_border_radius[$i], 'em') !== false){
                                            $ays_box_border_radius[$i] = $ays_box_border_radius[$i];
                                        }else{
                                            $ays_box_border_radius[$i] = intval($ays_box_border_radius[$i]).'px';
                                        }
                                    }
                                    $ays_box_border_radius = implode(' ', $ays_box_border_radius);
                                    echo $ays_box_border_radius; ?>; 
                    position: relative;
                    max-width: 100%;

                }	
                div.aysdiv_container_<?php echo $unique_id; ?>:hover {
                    box-shadow: <?php
                                    $ays_box_shadow = explode(' ', $ays_box_shadow);
                                    for($i=0; $i<count($ays_box_shadow); $i++){
                                        if(strpos($ays_box_shadow[$i], '#') !== false){
                                        }else{
                                            if(strpos($ays_box_shadow[$i], '%') !== false ||
                                            strpos($ays_box_shadow[$i], 'px') !== false ||
                                            strpos($ays_box_shadow[$i], 'pt') !== false ||
                                            strpos($ays_box_shadow[$i], 'rem') !== false ||
                                            strpos($ays_box_shadow[$i], 'em') !== false){
                                                $ays_box_shadow[$i] = $ays_box_shadow[$i];
                                            }else{
                                                $ays_box_shadow[$i] = intval($ays_box_shadow[$i]).'px';
                                            }
                                        }
                                    }
                                    $ays_box_shadow = implode(' ', $ays_box_shadow);
                                    echo $ays_box_shadow;
                                ?>;
                }

                div.aysdiv_container_<?php echo $unique_id; ?> a.ayslink_serial_<?php echo $unique_id; ?> {
                    background-color:<?php echo $ays_link_background; ?>;
                    color:<?php echo $ays_link_color; ?>;
                    padding:<?php 
                                if(strpos($ays_link_padding, '%') !== false ||
                                strpos($ays_link_padding, 'px') !== false ||
                                strpos($ays_link_padding, 'pt') !== false ||
                                strpos($ays_link_padding, 'rem') !== false ||
                                strpos($ays_link_padding, 'em') !== false){
                                    echo $ays_link_padding;
                                }else{
                                    echo $ays_link_padding.'px';
                                } ?>;
                    border-radius: <?php
                                    $ays_link_border_radius = explode(' ', $ays_link_border_radius);
                                    for($i=0; $i<count($ays_link_border_radius); $i++){
                                        if(strpos($ays_link_border_radius[$i], '%') !== false ||
                                        strpos($ays_link_border_radius[$i], 'px') !== false ||
                                        strpos($ays_link_border_radius[$i], 'pt') !== false ||
                                        strpos($ays_link_border_radius[$i], 'rem') !== false ||
                                        strpos($ays_link_border_radius[$i], 'em') !== false){
                                            $ays_link_border_radius[$i] = $ays_link_border_radius[$i];
                                        }else{
                                            $ays_link_border_radius[$i] = intval($ays_link_border_radius[$i]).'px';
                                        }
                                    }
                                    $ays_link_border_radius = implode(' ', $ays_link_border_radius);
                                    echo $ays_link_border_radius; ?>;
                    -webkit-border-radius: <?php
                                    $ays_link_border_radius = explode(' ', $ays_link_border_radius);
                                    for($i=0; $i<count($ays_link_border_radius); $i++){
                                        if(strpos($ays_link_border_radius[$i], '%') !== false ||
                                        strpos($ays_link_border_radius[$i], 'px') !== false ||
                                        strpos($ays_link_border_radius[$i], 'pt') !== false ||
                                        strpos($ays_link_border_radius[$i], 'rem') !== false ||
                                        strpos($ays_link_border_radius[$i], 'em') !== false){
                                            $ays_link_border_radius[$i] = $ays_link_border_radius[$i];
                                        }else{
                                            $ays_link_border_radius[$i] = intval($ays_link_border_radius[$i]).'px';
                                        }
                                    }
                                    $ays_link_border_radius = implode(' ', $ays_link_border_radius);
                                    echo $ays_link_border_radius; ?>;
                    -moz-border-radius: <?php
                                    $ays_link_border_radius = explode(' ', $ays_link_border_radius);
                                    for($i=0; $i<count($ays_link_border_radius); $i++){
                                        if(strpos($ays_link_border_radius[$i], '%') !== false ||
                                        strpos($ays_link_border_radius[$i], 'px') !== false ||
                                        strpos($ays_link_border_radius[$i], 'pt') !== false ||
                                        strpos($ays_link_border_radius[$i], 'rem') !== false ||
                                        strpos($ays_link_border_radius[$i], 'em') !== false){
                                            $ays_link_border_radius[$i] = $ays_link_border_radius[$i];
                                        }else{
                                            $ays_link_border_radius[$i] = intval($ays_link_border_radius[$i]).'px';
                                        }
                                    }
                                    $ays_link_border_radius = implode(' ', $ays_link_border_radius);
                                    echo $ays_link_border_radius; ?>;
                    
                    cursor:pointer;
                    position: absolute;
                    display: block;
                    text-decoration: none;
                    width: auto;
                    font-family:<?php echo $ays_link_font; ?>;
                    

                }
                
                div.aysdiv_container_<?php echo $unique_id; ?> .rand_post_and_pages_f_image_<?php echo $unique_id; ?> {
                    position: absolute;
                    left: 0;
                    top: <?php echo '-'.$ays_link_hover_img_height.'px'; ?>;
                    padding: 0;    
                    display: none;
                    width: 100%;
                    max-height: 250px;
                    height: <?php echo $ays_link_hover_img_height.'px'; ?>;
                    background-color: <?php echo $ays_link_background; ?>;
                    object-fit: <?php echo $ays_link_hover_img_size; ?>;
                    object-position: center center;
                    animation-name: rand_post_and_pages_f_image_<?php echo $unique_id; ?>;
                    animation-duration: .5s;
                }
                
                div.aysdiv_container_<?php echo $unique_id; ?> .rand_post_and_pages_f_image_<?php echo $unique_id; ?>:hover {
                    display: block;
                    padding: 0;
                    margin: 0;
                    background-color: <?php echo  $ays_link_hover_bg; ?>;
                }
                
                .aysdiv_container_<?php echo $unique_id; ?> a .rand_post_and_pages_f_image_<?php echo $unique_id; ?> {
                    box-shadow: none !important;
                }
                
                .aysdiv_container_<?php echo $unique_id; ?> a:hover .rand_post_and_pages_f_image_<?php echo $unique_id; ?> {
                    display: block;
                    box-shadow: none;
                }
                
                @keyframes rand_post_and_pages_f_image_<?php echo $unique_id; ?> {
                    from { top: <?php echo '-'.($ays_link_hover_img_height - $ays_link_hover_img_height/4).'px'; ?>; opacity: 0;}
                    to {top: <?php echo '-'.$ays_link_hover_img_height.'px'; ?>; opacity: 1;}
                }
            </style>

            <script>
            window.onload = function() {

                jQuery('.aysdiv_container_<?php echo $unique_id; ?>').find('.ayslink_serial_<?php echo $unique_id; ?>').each(function () {
                    var one_a = this;
                    setTimeout(function () {
                        <?php
                        if ($ays_animation_type != '' || $ays_animation_type != null) {
                            $ays_animate_type = $ays_animation_type;
                        } else {
                            $ays_animate_type = 'move';
                        }
                        if ($ays_animate_type == 'move') {
                            echo "aysAnimateArtMove_$unique_id(jQuery(one_a));";
                        } elseif ($ays_animate_type == 'fade') {
                            echo "aysAnimateArtFade_$unique_id(jQuery(one_a));";
                        }
                        ?>
                    }, Math.floor(Math.random() * 1000));
                });
                jQuery(".ayslink_serial_<?php echo $unique_id; ?>").mouseover(function () {
                    jQuery(this).stop(true, false);
                    var data_title = jQuery(this).text();
                    jQuery(this).css('white-space', 'nowrap');
                    jQuery(this).attr('data_title',data_title);

                    jQuery(this).css("z-index", "2");
                    jQuery(this).css("opacity", "1");
                    jQuery(this).css("color", "<?php echo $ays_link_hover_color; ?>");
                    jQuery(this).css("border", "solid 1px <?php echo $ays_link_hover_border; ?>");
                    jQuery(this).css("background-color", "<?php echo $ays_link_hover_bg; ?>");
                    jQuery(this).find('.rand_post_and_pages_f_image_<?php echo $unique_id; ?>').css({
                        "background-color": "<?php echo $ays_link_hover_bg; ?>",
                        
                    });

                });

                jQuery(".ayslink_serial_<?php echo $unique_id; ?>").mouseout(function () {
                    <?php
                    if ($ays_animate_type == 'move') {
                        echo "aysAnimateArtMove_$unique_id(jQuery(this));";
                    } elseif ($ays_animate_type == 'fade') {
                        echo "aysAnimateArtFade_$unique_id(jQuery(this));";
                    }
                    ?>
                    jQuery(this).css("z-index", "0");
                    //av
                    var data_title = jQuery(this).text();
                    jQuery(this).attr('data_title',data_title);
                    jQuery(this).css("background-color", "<?php echo $ays_link_background; ?>");
                    jQuery(this).css("color", "<?php echo $ays_link_color; ?>");
                    jQuery(this).css("border", "none");
                    jQuery(this).css("background-color", "<?php echo $color_prefix . $ays_link_background; ?>");
                    jQuery(this).find('.rand_post_and_pages_f_image_<?php echo $unique_id; ?>').css("background-color", "<?php echo $ays_link_background; ?>");
                });

                // });
            }
                function aysnewPosition_<?php echo $unique_id; ?>(cont,serial_link) {
                    serial_link.get(0).style.padding = "<?php 
                    if(strpos($ays_link_padding, '%') !== false ||
                        strpos($ays_link_padding, 'px') !== false ||
                        strpos($ays_link_padding, 'pt') !== false ||
                        strpos($ays_link_padding, 'rem') !== false ||
                        strpos($ays_link_padding, 'em') !== false){
                        echo $ays_link_padding;
                    }else{
                        echo $ays_link_padding.'px';
                    } ?>";
                    var h = cont.height() - serial_link.height()-2*parseInt(serial_link.get(0).style.padding);
                    var w = cont.width() - serial_link.width()-2*parseInt(serial_link.get(0).style.padding);
                    
                    var nh = Math.floor(Math.random() * h);
                    var nw = Math.floor(Math.random() * w);

                    return [nh, nw];
                    
                }
                
                var speed=<?php echo $ays_animate_speed ?>;        
                var aystimeout = 0;
                function aysAnimateArtFade_<?php echo $unique_id; ?>(serial_link) {
                    var cont = jQuery('.aysdiv_container_<?php echo $unique_id; ?>');
                    var new_pos = aysnewPosition_<?php echo $unique_id; ?>(cont,serial_link);
                    
                    if(aystimeout == 0){
                        serial_link.animate({
                                opacity: 0
                            },
                            Math.floor(300+(Math.random() * 1000)),
                            function() {
                                aystimeout = 1;
        //                        setTimeout(function(){
                                    aysAnimateArtFade_<?php echo $unique_id; ?>(serial_link);
        //                        },
        //                        Math.floor(speed+(Math.random() * 800)) 
        //                        );
                        });
                        serial_link.animate({
                            top: new_pos[0],
                            left: new_pos[1]
                            }
                        );
                    }
                    if(aystimeout == 1){
                        serial_link.animate({
                                opacity: 1
                            },
                            Math.floor(speed+(Math.random() * 1000)),
                            function() {
                                aystimeout = 0;
        //                        setTimeout(function(){
                                    aysAnimateArtFade_<?php echo $unique_id; ?>(serial_link);
        //                        },
        //                        Math.floor(speed+(Math.random() * 800))
        //                        );
                        });
                        serial_link.animate({
                            opacity: 0
                            }
                        );
                        serial_link.animate({
                            top: new_pos[0],
                            left: new_pos[1]
                            }
                        );
                    }

                }
                

                function aysAnimateArtMove_<?php echo $unique_id; ?>(serial_link) {
                    var cont = jQuery('.aysdiv_container_<?php echo $unique_id; ?>');
                    var new_pos = aysnewPosition_<?php echo $unique_id; ?>(cont,serial_link);
                    serial_link.animate({
                        top: new_pos[0],
                        left: new_pos[1]
                        }, Math.floor(speed+(Math.random() * 1000)), function() {
                        aysAnimateArtMove_<?php echo $unique_id; ?>(serial_link);
                    });

                }

            </script>
        <?php
        // This is where you run the code and display the output

        echo $args['after_widget'];
    }
            
    // Widget Backend
    public function form( $instance ) {
        $rand_id = rand(100, 999);
        $unique_id = __( $rand_id, 'ays-random-posts-and-pages' );

        $title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] :  __( 'AYS random internal links', 'ays-random-posts-and-pages' );
        $ays_animation_type = ( isset( $instance[ 'ays_animation_type' ] ) ) ? $instance[ 'ays_animation_type' ] :  __( 'move', 'ays-random-posts-and-pages' );
        $ays_select_post_type = ( !isset($instance[ 'select_post_type' ]) || $instance[ 'select_post_type' ] === false ) ?   __( 'post', 'ays-random-posts-and-pages' ) : explode('***', $instance[ 'select_post_type' ]);
            // Levons add start
        $ays_select_post_category = ( !isset($instance[ 'select_post_category' ]) || $instance[ 'select_post_category' ] === false ) ? '' : explode('***', $instance[ 'select_post_category' ]);
            // Levons add end
            
        $ays_width = ( isset( $instance[ 'ays_width' ] ) ) ? $instance[ 'ays_width' ] :  __( '270px', 'ays-random-posts-and-pages' );
        $ays_height = ( isset( $instance[ 'ays_height' ] ) ) ? $instance[ 'ays_height' ] :  __( '300px', 'ays-random-posts-and-pages' );
        $ays_box_background = ( isset( $instance[ 'ays_box_background' ] ) ) ? $instance[ 'ays_box_background' ] :  __( 'cedfe0', 'ays-random-posts-and-pages' );
        $ays_box_border_color = ( isset( $instance[ 'ays_box_border_color' ] ) ) ? $instance[ 'ays_box_border_color' ] :  __( '004466', 'ays-random-posts-and-pages' );

        $ays_box_border_thickness = ( isset($instance['ays_box_border_thickness'])) ? $instance['ays_box_border_thickness'] : __('3px', 'ays-random-posts-and-pages' );
        $ays_box_border_radius = ( isset( $instance[ 'ays_box_border_radius' ] ) ) ? $instance[ 'ays_box_border_radius' ] :  __( '10px', 'ays-random-posts-and-pages' );
        $ays_box_shadow = ( isset( $instance[ 'ays_box_shadow' ] ) ) ? $instance[ 'ays_box_shadow' ] :  __( '0px 0px 6px 8px #BCE7E9', 'ays-random-posts-and-pages' );

        $ays_count_links = ( isset( $instance[ 'ays_count_links' ] ) ) ? $instance[ 'ays_count_links' ] :  __( '5', 'ays-random-posts-and-pages' );
        $ays_count_letters = ( isset( $instance[ 'ays_count_letters' ] ) ) ? $instance[ 'ays_count_letters' ] :  __( '15', 'ays-random-posts-and-pages' );

        $ays_link_background = ( isset( $instance[ 'ays_link_background' ] ) ) ? $instance[ 'ays_link_background' ] :  __( '808080', 'ays-random-posts-and-pages' );
        $ays_background_image = ( isset( $instance[ 'ays_background_image' ] ) ) ? $instance[ 'ays_background_image' ] :  "";
        $ays_link_color = ( isset( $instance[ 'ays_link_color' ] ) ) ? $instance[ 'ays_link_color' ] :  __( 'FFFFFF', 'ays-random-posts-and-pages' );
        $ays_link_padding = ( isset( $instance[ 'ays_link_padding' ] ) ) ? $instance[ 'ays_link_padding' ] :  __( '4px', 'ays-random-posts-and-pages' );
        $ays_link_border_radius = ( isset( $instance[ 'ays_link_border_radius' ] ) ) ? $instance[ 'ays_link_border_radius' ] :  '';
        $ays_link_font = ( isset( $instance[ 'ays_link_font' ] ) ) ? $instance[ 'ays_link_font' ] :  __( 'arial', 'ays-random-posts-and-pages' );
        $ays_animate_speed = ( isset( $instance[ 'ays_animate_speed' ] ) ) ? $instance[ 'ays_animate_speed' ] :  __( '1400', 'ays-random-posts-and-pages' );
        $ays_link_hover_img_display = ( isset( $instance[ 'ays_link_hover_img_display' ] ) ) ? $instance[ 'ays_link_hover_img_display' ] :  __( 'yes', 'ays-random-posts-and-pages' );
        $ays_link_hover_img_height = ( isset( $instance[ 'ays_link_hover_img_height' ] ) ) ? $instance[ 'ays_link_hover_img_height' ] :  __( '100', 'ays-random-posts-and-pages' );
        $ays_link_hover_img_size = ( isset( $instance[ 'ays_link_hover_img_size' ] ) ) ? $instance[ 'ays_link_hover_img_size' ] :  __( 'contain', 'ays-random-posts-and-pages' );
        $ays_link_hover_bg = ( isset( $instance[ 'ays_link_hover_bg' ] ) ) ? $instance[ 'ays_link_hover_bg' ] :  __( 'FFFFFF', 'ays-random-posts-and-pages' );
        $ays_link_hover_bg_trans = ( isset( $instance[ 'ays_link_hover_bg_trans' ] ) ) ? $instance[ 'ays_link_hover_bg_trans' ] :  '';
        $ays_link_hover_color = ( isset( $instance[ 'ays_link_hover_color' ] ) ) ? $instance[ 'ays_link_hover_color' ] :  __( '808080', 'ays-random-posts-and-pages' );
        $ays_link_hover_border = ( isset( $instance[ 'ays_link_hover_border' ] ) ) ? $instance[ 'ays_link_hover_border' ] :  __( '004466', 'ays-random-posts-and-pages' );
        $ays_rp_same_type = ( isset( $instance[ 'ays_rp_same_type' ] )  && $instance[ 'ays_rp_same_type' ] == "on" ) ? "checked" : "";
        $ays_hide_for_mobile_device = ( isset( $instance[ 'ays_hide_for_mobile_device' ] )  && $instance[ 'ays_hide_for_mobile_device' ] == "on" ) ? "checked" : "";

        $color_prefix="#";
        if(strpos($ays_box_border_color, $color_prefix) === false && strpos($ays_box_border_color, 'rgba') === false ){
            $ays_box_border_color = $color_prefix . $ays_box_border_color;  
        }
        if(strpos($ays_box_background, $color_prefix) === false && strpos($ays_box_background, 'rgba') === false){
            $ays_box_background = $color_prefix . $ays_box_background;
        }
        if(strpos($ays_link_background, $color_prefix) === false && strpos($ays_link_background, 'rgba') === false){
            $ays_link_background = $color_prefix . $ays_link_background;
        }
        if(strpos($ays_link_color, $color_prefix) === false && strpos($ays_link_color, 'rgba') === false){
            $ays_link_color = $color_prefix . $ays_link_color;
        }
        if(strpos($ays_link_hover_bg, $color_prefix) === false && strpos($ays_link_hover_bg, 'rgba') === false ){
            $ays_link_hover_bg = $color_prefix . $ays_link_hover_bg;
        }
        if(strpos($ays_link_hover_color, $color_prefix) === false && strpos($ays_link_hover_color, 'rgba') === false){
            $ays_link_hover_color = $color_prefix . $ays_link_hover_color;
        }
        if(strpos($ays_link_hover_border, $color_prefix) === false && strpos($ays_link_hover_border, 'rgba') === false){
            $ays_link_hover_border = $color_prefix . $ays_link_hover_border;
        }

        // Include our css for admin
        wp_enqueue_style( 'ays_widget.css', plugins_url( 'css/ays_widget.css', __FILE__ ) );
        // Include our custom jQuery file with WordPress Color Picker dependency
        // wp_enqueue_script( 'jscolor', plugins_url( 'jscolor/jscolor.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' ); 
        /////////////////////////////////////////////////////////////////////////////////////////
        wp_enqueue_script( 'wp-color-picker-alpha', plugins_url( 'jscolor/wp-color-picker-alpha.min.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 

        wp_enqueue_script( 'ays-random-posts-and-pages-admin', plugins_url( 'js/ays-random-posts-and-pages-admin.js', __FILE__ ), array( 'jquery' ), false, true ); 
        
        ////////////////////////////////////////////////////////////////////////////////////////
        // Widget admin form
        global $wpdb;
        $post_types = $wpdb->get_results("SELECT post_type FROM " . $wpdb->prefix . "posts WHERE (post_type <> 'attachment') AND (post_type <> 'nav_menu_item') AND (post_type <> 'customize_changeset') ");
        $post_types_array = [];
        foreach($post_types as $types) {
            $post_types_array[] = $types->post_type;
        }
        $post_types_array = array_unique($post_types_array);
        $post_types_array = array_values($post_types_array);

        ?>
        
        <input type='hidden' name="<?php echo $this->get_field_name( 'unique_id' ); ?>" value="<?php echo esc_attr( $unique_id ); ?>">
        <p class="ays_field_section">
            <!-- box title -->
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title:','ays-random-posts-and-pages' ); ?></label> 
            <input class="ays_field" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" placeholder="<?php echo __( 'AYS random internal links','ays-random-posts-and-pages' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
        </p>

        <p class="ays_field_section ">
        <div class="only_pro" style="padding: 15px 0;"> 
        <!-- links animation speed -->
            <div class="pro_features">                            
            <div>
                <p>
                    <?php echo __("This feature is available only in ", 'ays-random-posts-and-pages'); ?>
                    <a href="https://ays-pro.com/wordpress/random-posts-and-pages" target="_blank" title="PRO feature"><?php echo __("PRO version!!!",'ays-random-posts-and-pages'); ?></a>
                </p>
            </div>
        </div>
        <label for=""><?php echo __( 'Themes:', 'ays-random-posts-and-pages' ); ?></label> 
        <select class="ays_field"  id="">
            <option value="default">Default</option>
            <option value="light">Light</option>
            <option value="dark">Dark</option>
        </select>
        </div>
        </p>
        <!-- /////////////////////////////////////// -->
        <p class="ays_field_section">
            <!-- links animation speed -->
            <label for="<?php echo $this->get_field_id( 'ays_animation_type' ); ?>"><?php echo __( 'Animation type:', 'ays-random-posts-and-pages' ); ?></label> 
            <select class="ays_field" name="<?php echo $this->get_field_name( 'ays_animation_type' ); ?>" id="<?php echo $this->get_field_id( 'ays_animation_type' ); ?>">
                <option <?php if($ays_animation_type=="move") echo "selected"; ?>  value="move">Move</option>
                <option <?php if($ays_animation_type=="fade") echo "selected"; ?>  value="fade">Fade</option>
            <option value="fixed" disabled="disabled">Fixed</option>
            </select>
        </p>

        <div class="only_pro">
            <div class="pro_features">                            
            <div>
                <p>
                    <?php echo __("This feature is available only in ", 'ays-random-posts-and-pages'); ?>
                    <a href="https://ays-pro.com/wordpress/random-posts-and-pages" target="_blank" title="PRO feature"><?php echo __("PRO version!!!",'ays-random-posts-and-pages'); ?></a>
                </p>
            </div>
        </div>
            <hr>
        <blockquote style="border-left: 2px solid #848181; padding:15px 0;">
        <p style="padding-left: 10px; font-style: italic;">Animation type <span style="font-weight: bold">fixed</span></p>
        </blockquote>
        <hr>
        </div>
        <p class="ays_field_section">
            <label for="<?php echo $this->get_field_id( 'select_post_type' ); ?>"><?php echo __( 'Post type:', 'ays-random-posts-and-pages' ); ?></label>
            <select multiple id="<?php echo $this->get_field_id( 'select_post_type' ); ?>" name="<?php echo $this->get_field_name('select_post_type') ?>[]">
                <?php
                    foreach($post_types_array as $types) {
                        if(is_array($ays_select_post_type)){
                            if(in_array($types, $ays_select_post_type)){
                                $selected_post_type = ' selected ';
                            }
                            else { $selected_post_type = ''; }
                        }else{
                            if(trim($types) === trim($ays_select_post_type)){
                                $selected_post_type = ' selected ';
                            }
                            else { $selected_post_type = ''; }
                        }

                        echo "<option ".$selected_post_type." value='$types'>".ucfirst($types)."</option>";
                    }
                ?>
            </select>
            <span style="font-size: 13px; padding-left: 3px;"><?php echo __( 'Hold the CTRL/CMD key and select the post types of your choice.','ays-random-posts-and-pages' ); ?></span>
        </p>
         <!-- Xcho start same post type -->
        <p class="ays_field_section" style="display: flex;align-items: center;">
            <label for="<?php echo $this->get_field_name( 'ays_rp_same_type' ); ?>" style="width: 235px;margin-top: 0;"><?php echo __( 'Show posts with the same post type:','ays-random-posts-and-pages' ); ?></label>
            <input type="checkbox" name="<?php echo $this->get_field_name( 'ays_rp_same_type' ); ?>" id="<?php echo $this->get_field_name( 'ays_rp_same_type' ); ?>" value="on" <?php echo $ays_rp_same_type;?>>
        </p>
        <!-- Xcho start same post type end -->
        <div class="only_pro">
            <div class="pro_features">                            
            <div>
                <p>
                    <?php echo __("This feature is available only in ", 'ays-random-posts-and-pages'); ?>
                    <a href="https://ays-pro.com/wordpress/random-posts-and-pages" target="_blank" title="PRO feature"><?php echo __("PRO version!!!",'ays-random-posts-and-pages'); ?></a>
                </p>
            </div>
        </div>
            <hr>
        <blockquote style="border-left: 2px solid #848181; padding:15px 0;">
        <p style="padding-left: 10px; font-style: italic;">If you would like to mark some of your posts as recommended, please copy the <span style="font-size: 14px;font-weight: bold;">"recommended"</span> word, and paste it into the Tag of the given post/page.</p>
        </blockquote>
        <hr>
        </div>
        <!--
            // Levons add start
        -->
        <p class="ays_field_section">
            <label for="<?php echo $this->get_field_id( 'select_post_category' ); ?>"><?php echo __( 'Post category:', 'ays-random-posts-and-pages' ); ?></label>
            <select multiple id="<?php echo $this->get_field_id( 'select_post_category' ); ?>" name="<?php echo $this->get_field_name('select_post_category') ?>[]">
                <?php
                    $categories = get_categories( array(
                        'orderby' => 'name',
                        'order'   => 'ASC'
                    ));

                    foreach( $categories as $category ) {
                        if(is_array($ays_select_post_category)){
                            if(in_array($category->cat_ID, $ays_select_post_category)){
                                $selected_post_category = ' selected ';
                            }
                            else { $selected_post_category = ''; }
                        }else{
                            if(trim($category->cat_ID) === trim($ays_select_post_category)){
                                $selected_post_category = ' selected ';
                            }
                            else { $selected_post_category = ''; }
                        }
                        echo "<option ".$selected_post_category." value='$category->cat_ID'>".ucfirst($category->name)."</option>";
                    }
                ?>
            </select>
            <span style="font-size: 13px; padding-left: 3px;"><?php echo __( 'Hold the CTRL/CMD key and select the post category of your choice.', 'ays-random-posts-and-pages' ); ?></span>
        </p>
       
        <!--
            // Levons add end
        -->
        <p class="ays_field_section">
            <!-- box width -->
            <label for="<?php echo $this->get_field_id( 'ays_width' ); ?>"><?php echo __( 'Box Width (300px)(for the 100% save empty blank):', 'ays-random-posts-and-pages' ); ?></label> 
            <input class="ays_field" id="<?php echo $this->get_field_id( 'ays_width' ); ?>" name="<?php echo $this->get_field_name( 'ays_width' ); ?>" type="text" placeholder="300px" value="<?php echo esc_attr( $ays_width ); ?>" />
        </p>
        <p class="ays_field_section">
            <!-- box height -->
            <label for="<?php echo $this->get_field_id( 'ays_height' ); ?>"><?php echo __( 'Box Height (300px):', 'ays-random-posts-and-pages' ); ?></label> 
            <input class="ays_field" id="<?php echo $this->get_field_id( 'ays_height' ); ?>" name="<?php echo $this->get_field_name( 'ays_height' ); ?>" type="text" placeholder="300px" value="<?php echo esc_attr( $ays_height ); ?>" />
        </p>
        <p class="ays_field_section">
            <!-- box background color -->
            <label for="<?php echo $this->get_field_id( 'ays_box_background' ); ?>"><?php echo __( 'Box background color:', 'ays-random-posts-and-pages' ); ?><br></label> 
            <input data-alpha="true" autocomplete="off" class="ays_color_picker" id="<?php echo $this->get_field_id( 'ays_box_background' ); ?>" name="<?php echo $this->get_field_name( 'ays_box_background' ); ?>" placeholder="CEDFE0" data-default-color="#CEDFE0" type="text" value="<?php echo esc_attr( $ays_box_background ); ?>" />
        </p>
        <div class="ays_field_section">
            <label for="<?= $this->get_field_id( 'ays_background_image' ); ?>"><?php echo __( 'Background Image:', 'ays-random-posts-and-pages' ); ?></label>
            <div id="<?= $this->get_field_id( 'ays_background_image' ); ?>" style="position: relative;overflow: hidden;">
                <img id="ays_button" class="<?= $this->id ?>_img" src="<?= (isset($ays_background_image) && $ays_background_image != '') ? $ays_background_image : ''; ?>" style="margin:0;padding:0;max-width:100%;display:block"/>
                <img src="<?php echo AYS_RANDOM_ADMIN_URL."/images/wrong.png"; ?>" class="ays_button_closer" style="position: absolute; top:0; right: 0;cursor: pointer;">
                <input type="hidden" class="widefat <?= $this->id ?>_url" name="<?= $this->get_field_name( 'ays_background_image' ); ?>" value="<?= (isset($ays_background_image) && $ays_background_image != '') ? $ays_background_image : ''; ?>" style="margin-top:5px;" />
            </div>
            <input type="button" id="<?= $this->id ?>" class="button button-primary js_custom_upload_media" value="Upload Image" style="margin-top:5px;" />
        </div>
        <p class="ays_field_section">
            <!-- box border color -->
            <label for="<?php echo $this->get_field_id( 'ays_box_border_color' ); ?>"><?php echo __( 'Box border color:', 'ays-random-posts-and-pages' ); ?><br></label> 
            <input data-alpha="true" class="ays_color_picker" id="<?php echo $this->get_field_id( 'ays_box_border_color' ); ?>" name="<?php echo $this->get_field_name( 'ays_box_border_color' ); ?>" type="text" data-default-color="#004466" placeholder="004466" value="<?php echo esc_attr( $ays_box_border_color ); ?>" />
        </p>
        <!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                                                                        <!-- Nareks added start -->
        <!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
        <p class="ays_field_section">
            <!-- box border thickness -->
            <label for="<?php echo $this->get_field_id( 'ays_box_border_thickness' ); ?>"><?php echo __( 'Box border thickness (3px):', 'ays-random-posts-and-pages' ); ?></label> 
            <input class="ays_field" id="<?php echo $this->get_field_id( 'ays_box_border_thickness' ); ?>" name="<?php echo $this->get_field_name( 'ays_box_border_thickness' ); ?>" type="text" placeholder="3px" value="<?php echo esc_attr( $ays_box_border_thickness ); ?>" />
        </p>
        <p class="ays_field_section">
            <!-- box border radius -->
            <label for="<?php echo $this->get_field_id( 'ays_box_border_radius' ); ?>"><?php echo __( 'Box border radius (10px):', 'ays-random-posts-and-pages' ); ?></label> 
            <input class="ays_field" id="<?php echo $this->get_field_id( 'ays_box_border_radius' ); ?>" name="<?php echo $this->get_field_name( 'ays_box_border_radius' ); ?>" placeholder="10px" type="text" value="<?php echo esc_attr( $ays_box_border_radius ); ?>" />
        </p>
        <p class="ays_field_section">
            <!-- box shadow -->
            <label for="<?php echo $this->get_field_id('ays_box_shadow'); ?>"><?php echo __('Box shadow (0px 0px 6px 8px #BCE7E9):', 'ays-random-posts-and-pages'); ?></label>
            <input class="ays_field" id="<?php echo $this->get_field_id('ays_box_shadow'); ?>" name="<?php echo $this->get_field_name('ays_box_shadow'); ?>" type="text" placeholder="0px 0px 6px 8px #BCE7E9" value="<?php echo esc_attr($ays_box_shadow); ?>" />
        </p>

        <!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                                                                        <!-- Nareks added ended -->
        <!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
        <p class="ays_field_section">
            <!-- box links count -->
            <label class='label_marg' for="<?php echo $this->get_field_id( 'ays_count_links' ); ?>"><?php echo __( 'Links count:', 'ays-random-posts-and-pages' ); ?></label> 
            <input class="ays_field" id="<?php echo $this->get_field_id( 'ays_count_links' ); ?>" name="<?php echo $this->get_field_name( 'ays_count_links' ); ?>" type="number" placeholder="10" value="<?php echo esc_attr( $ays_count_links ); ?>" />
        </p>
        <p class="ays_field_section">
            <!-- links count letters-->
            <label class='label_marg' for="<?php echo $this->get_field_id( 'ays_count_letters' ); ?>"><?php echo __( 'Quantity letters:', 'ays-random-posts-and-pages' ); ?></label> 
            <input class="ays_field" id="<?php echo $this->get_field_id( 'ays_count_letters' ); ?>" name="<?php echo $this->get_field_name( 'ays_count_letters' ); ?>" type="number" placeholder="15" value="<?php echo esc_attr( $ays_count_letters ); ?>" />
        </p>

        <p class="ays_field_section">
            <!-- links background color -->
            <label class='label_marg' for="<?php echo $this->get_field_id( 'ays_link_background' ); ?>"><?php echo __( 'Links Background color:', 'ays-random-posts-and-pages' ); ?><br></label>
            <input data-alpha="true" class="ays_color_picker" id="<?php echo $this->get_field_id( 'ays_link_background' ); ?>" name="<?php echo $this->get_field_name( 'ays_link_background' ); ?>" type="text" data-default-color="#808080" placeholder="808080" value="<?php echo esc_attr( $ays_link_background ); ?>" />
        </p>
        <p class="ays_field_section">
            <!-- links color -->
            <label class='label_marg' for="<?php echo $this->get_field_id( 'ays_link_color' ); ?>"><?php echo __( 'Links color:', 'ays-random-posts-and-pages' ); ?><br></label> 
            <input data-alpha="true" class="ays_color_picker" id="<?php echo $this->get_field_id( 'ays_link_color' ); ?>" name="<?php echo $this->get_field_name( 'ays_link_color' ); ?>" type="text" data-default-color="#FFFFFF" placeholder="FFFFFF" value="<?php echo esc_attr( $ays_link_color ); ?>" />
        </p>
        <p class="ays_field_section">
            <!-- links padding -->
            <label class='label_marg' for="<?php echo $this->get_field_id( 'ays_link_padding' ); ?>"><?php echo __( 'Link padding (4px):', 'ays-random-posts-and-pages' ); ?></label> 
            <input class="ays_field" id="<?php echo $this->get_field_id( 'ays_link_padding' ); ?>" name="<?php echo $this->get_field_name( 'ays_link_padding' ); ?>" type="text" placeholder="4px" value="<?php echo esc_attr( $ays_link_padding ); ?>" />
        </p>
        <p class="ays_field_section">
            <!-- links border radius -->
            <label class='label_marg' for="<?php echo $this->get_field_id( 'ays_link_border_radius' ); ?>"><?php echo __( 'Links border radius:', 'ays-random-posts-and-pages' ); ?></label> 
            <input class="ays_field" id="<?php echo $this->get_field_id( 'ays_link_border_radius' ); ?>" name="<?php echo $this->get_field_name( 'ays_link_border_radius' ); ?>" placeholder="0px 0px 5px 5px " type="text" value="<?php echo esc_attr( $ays_link_border_radius ); ?>" />
        </p>
        <p class="ays_field_section">
            <!-- links text font -->
            <label for="<?php echo $this->get_field_id( 'ays_link_font' ); ?>"><?php echo __( 'Font:', 'ays-random-posts-and-pages' ); ?></label> 
            <select class="ays_field" name="<?php echo $this->get_field_name( 'ays_link_font' ); ?>" id="<?php echo $this->get_field_id( 'ays_link_font' ); ?>">
                <option <?php if($ays_link_font=="arial") echo "selected"; ?> value="arial">Arial</option>
                <option <?php if($ays_link_font=="lucida grande") echo "selected"; ?> value="lucida grande">Lucida grande</option>
                <option <?php if($ays_link_font=="segoe ui") echo "selected"; ?> value="segoe ui">Segoe ui</option>
                <option <?php if($ays_link_font=="tahoma") echo "selected"; ?> value="tahoma">Tahoma</option>
                <option <?php if($ays_link_font=="trebuchet ms") echo "selected"; ?> value="trebuchet ms">Trebuchet ms</option>
                <option <?php if($ays_link_font=="verdana") echo "selected"; ?> value="verdana">Verdana</option>
            </select>
        </p>
        <p class="ays_field_section">
            <!-- links animation speed -->
            <label for="<?php echo $this->get_field_id( 'ays_animate_speed' ); ?>"><?php echo __( 'Animation speed:', 'ays-random-posts-and-pages' ); ?></label> 
            <select class="ays_field" name="<?php echo $this->get_field_name( 'ays_animate_speed' ); ?>" id="<?php echo $this->get_field_id( 'ays_animate_speed' ); ?>">
                <option <?php if($ays_animate_speed=="700") echo "selected"; ?>  value="700">Fast</option>
                <option <?php if($ays_animate_speed=="1400") echo "selected"; ?>  value="1400">Normal</option>
                <option <?php if($ays_animate_speed=="2300") echo "selected"; ?>  value="2300">Slow</option>
            </select>
        </p>
        <p class="ays_field_section">
            <!-- links hover featured image display -->
            <label class="ays_field" for="<?php echo $this->get_field_id( 'ays_link_hover_img_display' ); ?>"><?php echo __( 'Links hover image display:', 'ays-random-posts-and-pages' ); ?></label>
            <select class="ays_field" name="<?php echo $this->get_field_name( 'ays_link_hover_img_display' ); ?>" id="<?php echo $this->get_field_id( 'ays_link_hover_img_display' ); ?>">
                <option <?php if($ays_link_hover_img_display=="no_image") echo "selected"; ?>  value="no_image">No Image</option>
                <option <?php if($ays_link_hover_img_display=="hover_image") echo "selected"; ?>  value="hover_image">Hovered Image</option>
                <option <?php if($ays_link_hover_img_display=="opened_image") echo "selected"; ?>  value="opened_image">Opened Image</option>
            </select>
        </p>
        <p class="ays_field_section">
            <!-- links hover featured image height -->
            <label class="ays_field" for="<?php echo $this->get_field_id( 'ays_link_hover_img_height' ); ?>"><?php echo __( 'Links hover image height (px):', 'ays-random-posts-and-pages' ); ?></label>
            <input class="ays_field" id="<?php echo $this->get_field_id( 'ays_link_hover_img_height' ); ?>" name="<?php echo $this->get_field_name( 'ays_link_hover_img_height' ); ?>" type="number" placeholder="100" value="<?php echo esc_attr( $ays_link_hover_img_height );  ?>"  />
        </p>
        <p class="ays_field_section">
            <!-- links hover featured image size -->
            <label for="<?php echo $this->get_field_id( 'ays_link_hover_img_size' ); ?>"><?php echo __( 'Links hover image size:', 'ays-random-posts-and-pages'); ?></label> 
            <select class="ays_field" name="<?php echo $this->get_field_name( 'ays_link_hover_img_size' ); ?>" id="<?php echo $this->get_field_id( 'ays_link_hover_img_size' ); ?>">
                <option <?php if($ays_link_hover_img_size=="contain") echo "selected"; ?>  value="contain">Contain</option>
                <option <?php if($ays_link_hover_img_size=="cover") echo "selected";  ?>  value="cover">Cover</option>
            </select>
        </p>
        <p class="ays_field_section">
            <!-- links hover background color -->
            <label for="<?php echo $this->get_field_id( 'ays_link_hover_bg' ); ?>"><?php echo __( 'Links hover background color:', 'ays-random-posts-and-pages' ); ?><br></label>
            <input data-alpha="true" class="ays_color_picker" id="<?php echo $this->get_field_id( 'ays_link_hover_bg' ); ?>" name="<?php echo $this->get_field_name( 'ays_link_hover_bg' ); ?>" type="text" data-default-color="#FFFFFF" placeholder="FFFFFF" value="<?php if($ays_link_hover_bg_trans){echo 'FFFFFF';}else{ echo esc_attr( $ays_link_hover_bg );} ?>" />
            
        </p>
        <p class="ays_field_section">
            <!-- links hover color -->
            <label for="<?php echo $this->get_field_id( 'ays_link_hover_color' ); ?>"><?php echo __( 'Links hover color:', 'ays-random-posts-and-pages' ); ?><br></label> 
            <input data-alpha="true" class="ays_color_picker" id="<?php echo $this->get_field_id( 'ays_link_hover_color' ); ?>" name="<?php echo $this->get_field_name( 'ays_link_hover_color' ); ?>" data-default-color="#808080" type="text" placeholder="808080" value="<?php echo esc_attr( $ays_link_hover_color ); ?>" />
        </p>
        <p class="ays_field_section">
            <!-- links hover border color -->
            <label for="<?php echo $this->get_field_id( 'ays_link_hover_border' ); ?>"><?php echo __( 'Links hover border color:', 'ays-random-posts-and-pages' ); ?><br></label> 
            <input data-alpha="true" class="ays_color_picker" id="<?php echo $this->get_field_id( 'ays_link_hover_border' ); ?>" name="<?php echo $this->get_field_name( 'ays_link_hover_border' ); ?>" type="text" placeholder="#004466" data-default-color="#004466" value="<?php echo esc_attr( $ays_link_hover_border ); ?>" />
        </p>
        <p class="ays_field_section" style="display: flex;align-items:center">
            <!-- Hide for mobile -->
            <label for="<?php echo $this->get_field_id( 'ays_hide_for_mobile_device' ); ?>" style="width: 40%;"><?php echo __( 'Hide on mobile devices:', 'ays-random-posts-and-pages' ); ?><br></label> 
            <input type="checkbox" id="<?php echo $this->get_field_id( 'ays_hide_for_mobile_device' ); ?>" name="<?php echo $this->get_field_name( 'ays_hide_for_mobile_device' ); ?>" value="on" <?php echo $ays_hide_for_mobile_device; ?>/>
        </p>

        <div class='form-group row gpg_pro_link_cont' style="background: rgb(211 211 211);padding: 20px 20px;">
            <div class='gpg_pro_link' style="display: flex;align-items: center;justify-content: center;">
                <div>
                    <img style="width: 40px; "src="<?php echo AYS_RANDOM_ADMIN_URL."/images/gallery_img.png"; ?>" alt="">
                </div>
                <div class='gpg_pro_link_prem' style="margin: 0 8px;">
                    <h2><?php echo __('Upgrade to Premium','ays-random-posts-and-pages') ; ?></h2>
                </div>
                <div>
                    <a href="https://ays-pro.com/wordpress/random-posts-and-pages" class="button button-primary ays-button" id="ays-button-top" target="_blank" style="display: inline-block; height: 20px; align-items: center; font-weight: 500; "><?php echo __('Get Now!','ays-random-posts-and-pages') ; ?></a>
                </div>
            </div>
        </div>
        <p class="ays_field_section">
                <!-- links hover border color -->
            <input type="button" class="button ays_reset<?php echo esc_attr( $unique_id ); ?>" name="ays_reset<?php echo esc_attr( $unique_id ); ?>" value="<?php echo __('Reset','ays-random-posts-and-pages') ; ?>" />
        </p>

            <script>
                (function(){
                'use stirct';
                jQuery(document).ready(function(){            
                    function initColorPicker( widget ) {
                        widget.find( 'input.ays_color_picker' ).wpColorPicker();                        
                        jQuery(document).find('.wp-picker-default').val("Default");
                        jQuery(document).find('.wp-color-result-text').text("Select Color");
                    }

                    function onFormUpdate( event, widget ) {
                        initColorPicker( widget );
                    }

                    jQuery( document ).on( 'widget-added widget-updated', onFormUpdate );
                    jQuery( '#widgets-right .widget:has(.ays_color_picker)' ).each( function () {
                            initColorPicker( jQuery( this ) );
                    } );
                    jQuery(document).on("click", ".ays_reset<?php echo esc_attr($unique_id); ?>", function () {
                        // $('input').trigger('change');
                        jQuery("#<?php echo $this->get_field_id('ays_link_hover_color'); ?>").val('#808080');
                        jQuery(document).find('img#ays_button').attr('src', ' ');
                        jQuery(document).find('input.widefat').val(" ");
                        jQuery("#<?php echo $this->get_field_id('ays_link_hover_bg'); ?>").val('#FFFF22');
                        jQuery("#<?php echo $this->get_field_id( 'ays_link_hover_bg_trans' ); ?>").attr("checked", false);
                        jQuery("#<?php echo $this->get_field_id('ays_link_hover_img_size'); ?> option").eq(0).attr('selected', 'selected');
                        jQuery("#<?php echo $this->get_field_id('ays_link_hover_img_height'); ?>").val('100');
                        jQuery("#<?php echo $this->get_field_id('ays_link_hover_img_display'); ?> option").eq(0).attr('selected', 'selected');
                        jQuery("#<?php echo $this->get_field_id('ays_animate_speed'); ?> option").eq(0).attr('selected', 'selected');
                        jQuery("#<?php echo $this->get_field_id('ays_link_font'); ?> option").eq(0).attr('selected', 'selected');
                        jQuery("#<?php echo $this->get_field_id('ays_link_border_radius'); ?>").val('');
                        jQuery("#<?php echo $this->get_field_id('ays_link_padding'); ?>").val('4px');
                        jQuery("#<?php echo $this->get_field_id('ays_link_color'); ?>").val('#FFFFFF');
                        jQuery("#<?php echo $this->get_field_id('ays_link_background'); ?>").val('#808080');
                        jQuery("#<?php echo $this->get_field_id('ays_count_letters'); ?>").val('15');
                        jQuery("#<?php echo $this->get_field_id('ays_count_links'); ?>").val('5');
                        jQuery("#<?php echo $this->get_field_id('ays_box_shadow'); ?>").val('0px 0px 6px 8px #BCE7E9');
                        jQuery("#<?php echo $this->get_field_id('ays_box_border_radius'); ?>").val('10px');
                        jQuery("#<?php echo $this->get_field_id('ays_box_border_thickness'); ?>").val('3px');
                        jQuery("#<?php echo $this->get_field_id('ays_box_border_color'); ?>").val('#004466');
                        jQuery("#<?php echo $this->get_field_id('ays_box_background'); ?>").val('#cedfe0');
                        jQuery("#<?php echo $this->get_field_id('ays_height'); ?>").val('300px');
                        jQuery("#<?php echo $this->get_field_id('ays_width'); ?>").val('270px');
                        jQuery("#<?php echo $this->get_field_id('ays_animation_type'); ?>").val('move');
                        jQuery("#<?php echo $this->get_field_id('select_post_category'); ?>").html('<option value="1">Uncategorized</option>');
                        jQuery("#<?php echo $this->get_field_id('select_post_type'); ?>").html("<option value='page'>Page</option><option selected='' value='post'>Post</option><option value='revision'>Revision</option>");
                        jQuery("#<?php echo $this->get_field_id('title'); ?>").val('AYS random internal links');
                        jQuery("#<?php echo $this->get_field_id( 'ays_box_background' ); ?>").wpColorPicker('color', '#CEDFE0');
                        jQuery("#<?php echo $this->get_field_id( 'ays_box_border_color' ); ?>").wpColorPicker('color', '#004466');
                        jQuery("#<?php echo $this->get_field_id( 'ays_link_background' ); ?>").wpColorPicker('color', '#808080');
                        jQuery("#<?php echo $this->get_field_id( 'ays_link_color' ); ?>").wpColorPicker('color', '#FFFFFF');
                        jQuery("#<?php echo $this->get_field_id( 'ays_link_hover_bg' ); ?>").wpColorPicker('color', '#FFFFFF');
                        jQuery("#<?php echo $this->get_field_id( 'ays_link_hover_color' ); ?>").wpColorPicker('color', '#808080');
                        jQuery("#<?php echo $this->get_field_id( 'ays_link_hover_border' ); ?>").wpColorPicker('color', '#004466');
                    });
                function media_upload(button_selector) {
                    var _custom_media = true,
                        _orig_send_attachment = wp.media.editor.send.attachment;
                jQuery(document).on('click', button_selector, function () {
                    var button_id = jQuery(this).attr('id');
                    wp.media.editor.send.attachment = function (props, attachment) {
                        if (_custom_media) {
                        jQuery('.' + button_id + '_img').attr('src', attachment.url);                
                        jQuery('.' + button_id + '_url').val(attachment.url).trigger('change');     
                        } else {                  
                        return _orig_send_attachment.apply(jQuery('#' + button_id), [props, attachment]);
                        }
                    }
                    wp.media.editor.open(jQuery('#' + button_id));              
                    return false;          
                    });
                    
                }          
                media_upload('.js_custom_upload_media');
                jQuery(document).on('click','.ays_button_closer', function () {
                    jQuery(this).parent().find('img#ays_button').attr('src', ' ');            
                    jQuery(this).parent().find('input.widefat').val(" ").trigger('change');           
                    });
                }); 
                })();
                
            </script>
        <?php
    }
        
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['unique_id'] = ( ! empty( $new_instance['unique_id'] ) ) ? strip_tags( $new_instance['unique_id'] ) : '';
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['ays_animation_type'] = ( !empty( $new_instance['ays_animation_type'] ) ) ? strip_tags( $new_instance['ays_animation_type'] ) : '';

        if ( !empty( $new_instance['select_post_type'] ) ) {
            if ( is_array( $new_instance['select_post_type'] ) ) {
                $instance['select_post_type'] = strip_tags( implode("***", $new_instance['select_post_type']) );
            } else {
                $instance['select_post_type'] = strip_tags( $new_instance['select_post_type'] );
            }
        }

        $instance['ays_background_image'] = ( !empty( $new_instance['ays_background_image'] ) ) ? strip_tags( $new_instance['ays_background_image'] ) : '';   
        // Levons add start

        if ( !empty( $new_instance['select_post_category'] ) ) {
            if ( is_array( $new_instance['select_post_category'] ) ) {
                $instance['select_post_category'] = strip_tags( implode("***", $new_instance['select_post_category']) );
            } else {
                $instance['select_post_category'] = strip_tags( $new_instance['select_post_category'] );
            }
        }
            
        // Levons add end
        $instance['ays_width'] = ( ! empty( $new_instance['ays_width'] ) ) ? strip_tags( $new_instance['ays_width'] ) : '';
        $instance['ays_height'] = ( ! empty( $new_instance['ays_height'] ) ) ? strip_tags( $new_instance['ays_height'] ) : '';
        $instance['ays_box_background'] = ( ! empty( $new_instance['ays_box_background'] ) ) ? strip_tags( $new_instance['ays_box_background'] ) : '';
        $instance['ays_box_border_color'] = ( ! empty( $new_instance['ays_box_border_color'] ) ) ? strip_tags( $new_instance['ays_box_border_color'] ) : '';

        $instance['ays_box_border_thickness'] = ( ! empty( $new_instance['ays_box_border_thickness'] ) ) ? strip_tags( $new_instance['ays_box_border_thickness'] ) : ''; 
        $instance['ays_box_border_radius'] = ( ! empty( $new_instance['ays_box_border_radius'] ) ) ? strip_tags( $new_instance['ays_box_border_radius'] ) : '';     
        $instance['ays_box_shadow'] = (!empty($new_instance['ays_box_shadow'])) ? strip_tags($new_instance['ays_box_shadow']) : '';
            
        $instance['ays_count_links'] = ( ! empty( $new_instance['ays_count_links'] ) ) ? strip_tags( $new_instance['ays_count_links'] ) : '';
        $instance['ays_count_letters'] = ( ! empty( $new_instance['ays_count_letters'] ) ) ? strip_tags( $new_instance['ays_count_letters'] ) : '';
        $instance['ays_link_background'] = ( ! empty( $new_instance['ays_link_background'] ) ) ? strip_tags( $new_instance['ays_link_background'] ) : '';
        $instance['ays_link_color'] = ( ! empty( $new_instance['ays_link_color'] ) ) ? strip_tags( $new_instance['ays_link_color'] ) : '';
        $instance['ays_link_padding'] = ( ! empty( $new_instance['ays_link_padding'] ) ) ? strip_tags( $new_instance['ays_link_padding'] ) : '';
        $instance['ays_link_border_radius'] = ( ! empty( $new_instance['ays_link_border_radius'] ) ) ? strip_tags( $new_instance['ays_link_border_radius'] ) : '';
        $instance['ays_link_font'] = ( ! empty( $new_instance['ays_link_font'] ) ) ? strip_tags( $new_instance['ays_link_font'] ) : '';
        $instance['ays_animate_speed'] = ( ! empty( $new_instance['ays_animate_speed'] ) ) ? strip_tags( $new_instance['ays_animate_speed'] ) : '';
        $instance['ays_link_hover_img_display'] = ( ! empty( $new_instance['ays_link_hover_img_display'] ) ) ? strip_tags( $new_instance['ays_link_hover_img_display'] ) : '';
        $instance['ays_link_hover_img_height'] = ( ! empty( $new_instance['ays_link_hover_img_height'] ) ) ? strip_tags( $new_instance['ays_link_hover_img_height'] ) : '';
        $instance['ays_link_hover_img_size'] = ( ! empty( $new_instance['ays_link_hover_img_size'] ) ) ? strip_tags( $new_instance['ays_link_hover_img_size'] ) : '';
        $instance['ays_link_hover_bg'] = ( ! empty( $new_instance['ays_link_hover_bg'] ) ) ? strip_tags( $new_instance['ays_link_hover_bg'] ) : '';
        $instance['ays_link_hover_bg_trans'] = ( ! empty( $new_instance['ays_link_hover_bg_trans'] ) ) ? strip_tags( $new_instance['ays_link_hover_bg_trans'] ) : '';
        $instance['ays_link_hover_color'] = ( ! empty( $new_instance['ays_link_hover_color'] ) ) ? strip_tags( $new_instance['ays_link_hover_color'] ) : '';
        $instance['ays_link_hover_border'] = ( ! empty( $new_instance['ays_link_hover_border'] ) ) ? strip_tags( $new_instance['ays_link_hover_border'] ) : '';
        $instance['ays_rp_same_type'] = ! empty($new_instance['ays_rp_same_type']) && $new_instance['ays_rp_same_type'] == "on" ? "on" : "off";
        $instance['ays_hide_for_mobile_device'] = ! empty($new_instance['ays_hide_for_mobile_device']) && $new_instance['ays_hide_for_mobile_device'] == "on" ? "on" : "off";

        return $instance;
    }
} // Class random ends here

// Register and load the widget
function wpb_load_widget() {
    
	register_widget( 'Random_Posts_and_Pages' );
}

function wpdocs_load_textdomain() {	
    load_plugin_textdomain( 'ays-random-posts-and-pages', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

add_action( 'widgets_init', 'wpb_load_widget' );
add_action( 'widgets_init', 'wpdocs_load_textdomain' );

?>
