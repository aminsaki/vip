<?php
function vip_add_menus(){
    $vip_main=add_menu_page('عضویت ویژه','عضویت ویژه','manage_options','vip_main','vip_dashboard_page');
    $vip_main_sub=add_submenu_page('vip_main','عضویت ویژه','صفحه اصلی','manage_options','vip_main');
    $vip_products=add_submenu_page('vip_main','محصولات','محصولات','manage_options','vip_products','vip_products_page');
    $vip_users=add_submenu_page('vip_main','کاربران','کاربران','manage_options','vip_users','vip_users_page');
     $vip_trans=add_submenu_page('vip_main','تراکنش ها','تراکنش ها','manage_options','vip_transactions','vip_transactions_page');
    add_action("load-{$vip_main}","load_vip_scripts");
    add_action("load-{$vip_main_sub}","load_vip_scripts");
    add_action("load-{$vip_products}","load_vip_scripts");
    add_action("load-{$vip_users}","load_vip_scripts");
    add_action("load-{$vip_trans}","load_vip_scripts");
}
function load_vip_scripts(){
    wp_register_style('vip_admin_styles',VIP_CSS_URL.'vip_admin_styles.css');
    wp_enqueue_style('vip_admin_styles');
        
    wp_register_script('vip_admin_script',VIP_JS_URL.'vip_admin_script.js',array('jquery'));
    wp_localize_script('vip_admin_script','wpvip',array('ajaxurl'=>admin_url('admin-ajax.php')));
    wp_enqueue_script('vip_admin_script');
}
//products page
function vip_delete_products(){
   $selected=$_POST['selected'];
   if(count($selected)){
       global $wpdb,$table_prefix;
       foreach ($selected as $pid){
           $wpdb->query("DELETE FROM {$table_prefix}vip_products WHERE product_id={$pid} LIMIT 1");
       }
   }
}
function vip_change_price(){
   $selected=$_POST['selected'];
   $price=intval($_POST['txt_price']);
   if(!$price){return;}
   if(count($selected)){
       global $wpdb,$table_prefix;
       foreach ($selected as $pid){
           $wpdb->query("UPDATE {$table_prefix}vip_products SET product_price={$price} WHERE product_id={$pid} LIMIT 1");
       }
   }
}
//users
function vip_user_add_credit($uid,$days){
    $expire=  get_user_expire($uid);
    if(!$expire){
        return false;
    }
    $credit=intval($days)*24*60*60;
    $new_expire=intval($expire)+$credit;
    return update_user_meta($uid,'vip_expire',$new_expire);
}
function vip_user_sub_credit($uid,$days){
    $expire=  get_user_expire($uid);
    if(!$expire){
        return false;
    }
    $credit=intval($days)*24*60*60;
    $new_expire=intval($expire)-$credit;
    return update_user_meta($uid,'vip_expire',$new_expire);
}
function get_user_vip_plan($user_id){
    
    $plan="";
    if(intval($user_id)){
       $plan=get_user_meta($user_id,'vip_plan_name',true);
    }
    return !empty($plan)?$plan:false;
    
}
function get_user_plan_title($user_id){
    $plan_name=  get_user_vip_plan($user_id);
    $name="نامشخص";
    switch ($plan_name){
        case 'vip-gold':$name="عضویت طلایی";
            break;
        case 'vip-silver':$name="عضویت نقره ای";
            break;
        case 'vip-bronze':$name="عضویت برنزی";
    }
    return $name;
}
function get_user_expire($user_id){
     $expire=0;
    if(intval($user_id)){
       $expire=intval(get_user_meta($user_id,'vip_expire',true));
    }
    return !empty($expire)?$expire:false; 
}
function get_user_expire_date($user_id){
    $expire=  get_user_expire($user_id);
    if($expire){
        return strftime("%Y-%m-%d %H:%M:%S",$expire);
    }
    return false;
}


function vip_bulk_add_user(){
    $selected=$_POST['selected'];
    $plan_id=$_POST['vip_plan'];
    if(count($selected)){
        foreach ($selected as $user_id){
            vip_add_vip_to_user($user_id,$plan_id);
        }
    }
}
function vip_bulk_user_up(){
    $selected=$_POST['selected'];
    $days=$_POST['txt_days'];
    if(count($selected)){
        foreach ($selected as $user_id){
            vip_user_add_credit($user_id,$days);
        }
    }
}
function vip_bulk_user_down(){
      $selected=$_POST['selected'];
    $days=$_POST['txt_days'];
    if(count($selected)){
        foreach ($selected as $user_id){
            vip_user_sub_credit($user_id,$days);
        }
    }
 
}
function vip_bulk_user_delete(){
     $selected=$_POST['selected'];
    if(count($selected)){
        foreach ($selected as $user_id){
            vip_delete_vip_from_user($user_id);
        }
    }
}
//utility
//trnasactions
function vip_bulk_confirm_transaction(){
    $selected=$_POST['selected'];
    if(count($selected)){
        global $wpdb,$table_prefix;
        foreach ($selected as $tid){
            $wpdb->query("UPDATE {$table_prefix}vip_transactions SET status=1 WHERE id={$tid} LIMIT 1");
        }
    }
}
function vip_bulk_delete_transaction(){
    $selected=$_POST['selected'];
    if(count($selected)){
        global $wpdb,$table_prefix;
        foreach ($selected as $tid){
            $wpdb->query("DELETE FROM {$table_prefix}vip_transactions WHERE id={$tid} LIMIT 1");
        }
    }
}
function vip_get_bank_title($bm){
    $bank="";
    switch ($bm){
        case 'parspal':$bank="پارس پال";
    }
    return $bank;
}
function vip_get_trans_status($stat){
    $status="نا مشخص";
    switch ($stat){
        case 1:$status='<span class="vip_stat_active">تایید شده</span>';
            break;
        case 0:$status='<span class="vip_stat_inactive">تایید نشده</span>';
            break;
    }
    return $status;
}
