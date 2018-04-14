<?php
//utility
function vip_get_status($stat){
    $status="نا مشخص";
    switch ($stat){
        case 1:$status='<span class="vip_stat_active">فعال</span>';
            break;
        case 0:$status='<span class="vip_stat_inactive">غیر فعال</span>';
            break;
    }
    return $status;
}
function vip_get_persian_date($date){
    if(!$date){
        return 0;
    }
    return (function_exists('jdate'))?jdate("H:i:s Y/m/d", $date):0;
}
function vip_add_vip_to_user($user_id,$plan_id){
    global $wpdb,$table_prefix;
    $product=$wpdb->get_row("SELECT product_name,product_days FROM {$table_prefix}vip_products WHERE product_ID={$plan_id}");
    if($product){
        update_user_meta($user_id,'vip_plan_id',$plan_id);
        update_user_meta($user_id,'vip_plan_name', $product->product_name);
        update_user_meta($user_id,'vip_expire',time()+intval($product->product_days)*24*60*60);
    }
}
function vip_delete_vip_from_user($user_id){
     delete_user_meta($user_id,'vip_plan_id');
     delete_user_meta($user_id,'vip_plan_name');
     delete_user_meta($user_id,'vip_expire');
}
function get_post_vip_level($post_id){
    $level=get_post_meta( $post_id, 'vip-level',true);
    if(empty($level) || $level=="normal"){
        return '<span class="free" >FREE</span>';
    }
    return '<span class="'.$level.'" >VIP</span>';
}
function get_user_remain_day($user_id){
    if(!$user_id){
        return 0;
    }
   $expire=  get_user_expire($user_id);
   $days=false;
   if($expire){
       $days= ceil(((($expire-time())/60)/60)/24);
   }
   return ($days<0)?0:$days;
}
//utitlity
function vip_check_user_expire(){
    if(!is_user_logged_in()){
        return false;
    }
    global $user_ID;
    $is_user_vip=vip_is_user_vip($userID);
    if($is_user_vip){
        $remain=  get_user_remain_day($userID);
        if($remain==0){
            vip_delete_vip_from_user($userID);
        }
    }
    
    
}
function vip_is_user_vip($userID){
    $vip_plan=get_user_meta($userID,'vip_plan_id',true);
    return !empty($vip_plan);
}
