<?php

global $wpdb;

define('CONST_SITE_INFORMATION_PAGE_ID', 8);
define('HOME_PAGE_ID', 26);
define('ABOUT_PAGE_ID', 51);
define('CORPORATE_PAGE_ID', 164);
define('RETAIL_PAGE_ID', 65);
define('GET_IN_TOUCH_PAGE_ID', 85);
define('LEGAL_PAGE_ID', 131);
define('TERMS_AND_CONDITIONS_PAGE_ID', 139);
define('PRIVACY_POLICY_PAGE_ID', 145);
define('COOKIE_DECLARATION_PAGE_ID', 151);
define('COVID_REGULATION_PAGE_ID', 159);
define('TRADE_FOREIGN_EXCHANGE_PAGE_ID', 200);
define('DAY_TO_DAY_PAGE_ID', 205);  
define('FINANCE_PAGE_ID', 580);


define('CONST_AGB_EN_SITE_ID', 1);
define('CONST_AGB_AR_SITE_ID', 2);

define('CONST_CSS_VERSION', "1.1.1");

  function cc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
  }
  add_filter('upload_mimes', 'cc_mime_types');



add_action( 'init', 'creatPosttype' );

function creatPosttype()
{
    // register_post_type( 'service',
    //     array(
    //         'labels' => array(
    //             'name' => __('Services'),
    //             'singular_name' => __('Service')
    //         ),
    //         'public' => true,
    //         'has_archive' => false,
    //         'show_in_rest' => true,
    //         'rewrite' => array('slug' => 'service'),
    //         // 'supports' => array('title', 'thumbnail')

    //     )
    // );

    register_post_type( 'foreign-exchange',
        array(
            'labels' => array(
                'name' => __('Trade Foreign Exchange'),
                'singular_name' => __('Trade Foreign Exchange')
            ),
            'public' => true,
            'has_archive' => false,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'trade-foreign-exchange'),
            // 'supports' => array('title', 'thumbnail')

        )
    );

    register_post_type( 'day-to-day',
        array(
            'labels' => array(
                'name' => __('Day To Day'),
                'singular_name' => __('Day To Day')
            ),
            'public' => true,
            'has_archive' => false,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'day-to-day'),
            // 'supports' => array('title', 'thumbnail')

        )
    );

    
}


function getPostTypeData($post_type,$per_page = -1,$paged='')
{ 
    $arg = array(
      'post_type' => $post_type,
      'order' => 'ASC',
      'posts_per_page' => $per_page,
      'paged' =>  $paged,
      'orderby' => 'publish_date',
      // 'order' => 'ASC',
    );
    $postData = new WP_Query($arg);
    return $postData;
}


  function dd($array)
  {
      echo "<pre>";
      print_r($array);
      echo "</pre>";
  }

  function json_response($response = array()){
    echo json_encode($response);die;
  }

  function checkInputValue($str='')
  {
    if($str)
    {
      $str = strip_tags($str);
      $str = addslashes($str);
    }
    return $str;
  }

  function checkNonce($nonce)
  {
      $nonce = checkInputValue($nonce);
      if (wp_verify_nonce( $nonce, "user_nonce")) 
      {
        return true;
      } 
      return false;
  }

  add_action( 'admin_bar_menu', 'add_admin_header_menu_bar', 999 );

  function add_admin_header_menu_bar( $wp_admin_bar )
  {
      if( current_user_can( 'administrator' ) )
      {

        $menu_args = array(
            'id'    => 'site_info',
            'title' => 'Site Information',
            'href'  => home_url('/wp-admin/post.php?post='.CONST_SITE_INFORMATION_PAGE_ID.'&action=edit')
        );
        $wp_admin_bar->add_node( $menu_args );
    }
  }




function generate_id_by_title($str, $sep='_')
{
  $res = strtolower($str);
  $res = preg_replace('/[^[:alnum:]]/', ' ', $res);
  $res = preg_replace('/[[:space:]]+/', $sep, $res);
  return trim($res, $sep);
}

  //PHP function to make slug (URL string)
  function slugify($text)
  {
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
  }


add_action("wp_ajax_request_information", "RequestInformation");
add_action("wp_ajax_nopriv_request_information", "RequestInformation");

function RequestInformation()
{
  $error_message = get_field('error_message',CONST_SITE_INFORMATION_PAGE_ID);
  $sucess_message = get_field('sucess_message',CONST_SITE_INFORMATION_PAGE_ID);
  $already_subscribed_message  = get_field('already_subscribed_message',CONST_SITE_INFORMATION_PAGE_ID);
  
  if(isset($_POST['nonce'])) 
  {
    $checkNonce = checkNonce($_POST['nonce']);
    if ($checkNonce == false) 
    {
        json_response(array('status' =>0 ,'response'=>'Invalid Token' ));
    }
  }

  if (empty($_POST['user_email'])) 
  {
    json_response(array('status' =>0 ,'response'=>$error_message ));
  }

  global $wpdb;
  $datum = $wpdb->get_results("SELECT * FROM subscribers_log WHERE email = '".$_POST['user_email']."'");
  if($wpdb->num_rows > 0)
  {
    json_response(array('status' =>0 ,'response'=>$already_subscribed_message ));
  }

  $data = array();
  $data['email'] = $_POST['user_email'];
  $data['language'] = $_POST['lang'];
  $data['created']    = date('Y-m-d H:i:s');
  global $wpdb;
  $insert = $wpdb->insert( 'subscribers_log', $data);

  json_response(array('status' =>1 ,'response'=>$sucess_message, 'data' => $data));
}

?>