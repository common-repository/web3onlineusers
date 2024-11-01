<?php
/**
 * Plugin Name: web3onlineusers
 * Plugin URI: http://2moontech.com
 * Description: web3onlineusers
 * Version: 1.0.1
 * Author: Smoggy
 * Author URI: http://web3onlineusers
 */// create custom plugin settings menu

if ( !defined( 'ABSPATH' ) ) {
    exit;
}



register_activation_hook( __FILE__, 'web3onlineusers_create_db' );
function web3onlineusers_create_db() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = 'web3onlineusers_tlist';
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $sql = "CREATE TABLE $table_name (
		 id int(11) NOT NULL AUTO_INCREMENT,         
         adddate int(11) NOT NULL,
         mtoken  varchar(250) NOT NULL,
         active  int(11) NOT NULL,        
           userpage  varchar(250) NOT NULL,
		PRIMARY KEY id (id)
	) $charset_collate;";

    dbDelta( $sql );


}

function web3onlineusers_deactivate() {
    // Unregister the post type, so the rules are no longer in memory.
    unregister_post_type( 'web3onlineusers' );
}
register_deactivation_hook( __FILE__, 'web3onlineusers_deactivate' );

if ( is_admin() ) {


    function web3onlineusers_admin_menu()
    {
        add_menu_page('Web3Plugins', 'Web3Plugins', 'manage_categories', 'web3plugins', 'web3plugins', '', 5);
        add_submenu_page('web3plugins', 'Web3OnlineUsers', 'Web3 Online Users', 'manage_categories', 'web3onlineusers_tracker', 'web3onlineusers_tracker',  6);
        //add_menu_page('Web3OnlineUsers', 'Web3OnlineUsers', 'manage_categories', 'web3onlineusers_tracker', 'web3onlineusers_tracker', '', 6);

     }

    add_action('admin_menu', 'web3onlineusers_admin_menu');


    function web3onlineusers_tracker()
    {

       global $wpdb;

        $active1 = 1;
        $active2 = 0;
        require_once('view/showonlineusers.php');


    }

}
add_action('wp_ajax_web3onlineusers_trackerlive', 'my_web3onlineusers_trackerlive');


function my_web3onlineusers_trackerlive()
{



            global $wpdb;
            $czas=time()-(60*10);

    $wynik=$wpdb->get_results('select a.visa, a.mastercard from wp_mid a, wp_midcontrol b where a.id=b.currentmid','ARRAY_A');


    $current = $wynik['0']['visa'];
   if($wynik['0']['mastercard']!="") $current .=', '.$wynik['0']['mastercard'];




//echo 'select * from web3onlineusers_tlist where adddate>'.$czas.' and mtoken!="" order by adddate DESC';
            $wynik=$wpdb->get_results($wpdb->prepare('select * from web3onlineusers_tlist where adddate>%s and mtoken!="" order by adddate DESC',$czas),'ARRAY_A');
$dane1='';
    $dane1 .='<tr><td colspan="3">MID: '.esc_html($current).'</td></tr>';
            foreach($wynik as $w=>$row)
            {


                $czas=(time()-$row['adddate'])/60;



                if($row['adddate']>(time()-240))
                {

                    $dane1 .='<tr><th style="color:green;text-align:center">online</th><td>'.date("m/d/Y h:i:s a",$row['adddate']).'</td><td>'.$row['userpage'].'</td></tr>';
                } else
                    $dane1 .='<tr><td style="color:red;text-align:center">offline ('.round($czas,0).' min) </td><td>'.date("m/d/Y h:i:s a",$row['adddate']).'</td><td>'.$row['userpage'].'</td></tr>';




            }

           echo wp_kses_post($dane1);
            die();
}

add_action( 'wp_head', 'my_web3onlinetrack' );
function my_web3onlinetrack()
{
    $page='product';
    global $wpdb;

    if($_SESSION['web3ot']=="")
   {
       $_SESSION['web3ot']=time().time().time();

       $wpdb->query($wpdb->prepare('insert web3onlineusers_tlist set adddate=%s, mtoken=%s, active=1,userpage="index"',time(),md5($_SESSION['web3ot'])));
   }

   if(is_checkout())
   {

       $page='checkout';
   }

    $page=sanitize_text_field($_SERVER["REQUEST_URI"]);

  //  $_SESSION['web3ot'] = sanitize_text_field($_SESSION['web3ot']);


    if($_SESSION['web3ot']!="")
    {
        $r=$wpdb->get_results($wpdb->prepare('select * from web3onlineusers_tlist where mtoken=%s ',md5(sanitize_text_field($_SESSION['web3ot'])),'ARRAY_A'));
        if(count($r)>0)
           $wpdb->query($wpdb->prepare('update web3onlineusers_tlist set adddate=%s, userpage=%s where mtoken=%s',time(),$page,md5(sanitize_text_field($_SESSION['web3ot']))));
        else $wpdb->query($wpdb->prepare('insert web3onlineusers_tlist set adddate=%s, userpage=%s, mtoken=%s',time(),$page,md5(sanitize_text_field($_SESSION['web3ot']))));
    }



}