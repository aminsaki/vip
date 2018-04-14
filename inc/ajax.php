<?php
add_action('wp_ajax_vip_delete_product','vip_delete_product');
add_action('wp_ajax_vip_change_product_status','vip_change_product_status');
add_action('wp_ajax_vip_product_discount','vip_product_discount');
add_action('wp_ajax_vip_add_product','vip_add_product');
//users
add_action('wp_ajax_add_user_credit','add_user_credit');
add_action('wp_ajax_sub_user_credit','sub_user_credit');
add_action('wp_ajax_delete_user_vip','delete_user_vip');

//trnasactions
add_action('wp_ajax_vip_delete_transaction','vip_delete_transaction');
add_action('wp_ajax_vip_confirm_transaction','vip_confirm_transaction');


function vip_delete_product(){
    $pid=$_POST['pid'];
    global $wpdb,$table_prefix;
    $res=$wpdb->query("DELETE FROM {$table_prefix}vip_products WHERE product_ID={$pid} LIMIT 1");
    if($res){
        die("محصول مورد نظر با موفقیت حذف گردید");
    }
    die("خطایی رخ داده است لطفا بعدا امتحان کنید");
}
function vip_change_product_status(){
    $pid=$_POST['pid'];
    global $wpdb,$table_prefix;
    $res=$wpdb->query("UPDATE {$table_prefix}vip_products SET product_status=NOT product_status WHERE product_ID={$pid} LIMIT 1");
    if($res){
        die("تغییر وضعیت با موفقیت صورت گرفت");
    }
    die("خطایی رخ داده است لطفا بعدا امتحان کنید");
}
function vip_product_discount(){
    $pid=$_POST['pid'];
    $off=$_POST['off'];
    global $wpdb,$table_prefix;
    $res=$wpdb->query("UPDATE {$table_prefix}vip_products SET product_discount={$off} WHERE product_ID={$pid} LIMIT 1");
    if($res){
        die("تخفیف با موفیت اعمال گردید");
    }
    die("خطایی رخ داده است لطفا بعدا امتحان کنید");
}
function vip_add_product(){
    
?>
<p>
    <label>نوع  محصول :
        <select name="product_name">
            <option value="vip-gold">طلایی</option>
             <option value="vip-silver">نقره ای</option>
              <option value="vip-bronze">برنزی</option>
        </select>
    </label>
</p>
<p>
    <label>عنوان محصول:
        <input name="txt_title" type="text">
    </label>
</p>
<p>
    <label>قیمت :
        <input name="txt_price" type="text">
    </label>
</p>
<p>
    <label>تعداد روز :
        <input name="txt_days" type="text">
    </label>
</p>
<p>
    <input type="submit" name="submit_add" value="اضافه کردن" class="button button-primary">
    <button id="cancel" class="button">بستن</button>
</p>
<?php
die();
}
//users
function add_user_credit(){
    $uid=intval($_POST['uid']);
    $days=intval($_POST['days']);
    $res=vip_user_add_credit($uid, $days);
    if($res){
        vip_output("اعتبار مورد نظر با موفقیت اضافه شد");
    }
    vip_output("خطایی رخ داده است لطفا بعدا امتحان کنید");
}
function sub_user_credit(){
    $uid=intval($_POST['uid']);
    $days=intval($_POST['days']);
    $res=vip_user_sub_credit($uid, $days);
    if($res){
        vip_output("اعتبار مورد نظر با موفقیت کم شد");
    }
    vip_output("خطایی رخ داده است لطفا بعدا امتحان کنید");
}
function delete_user_vip(){
    $uid=intval($_POST['uid']);
    global $wpdb,$table_prefix;
    if($uid){
        $res1=delete_user_meta($uid,'vip_plan_id');
        $res2=delete_user_meta($uid,'vip_plan_name');
        $res3=delete_user_meta($uid,'vip_expire');
        if($res1 && $res2 && $res3){
            $result['stat']=1;
            $result['msg']="کاربر مورد نظر با موفقیت حذف گردید";
            vip_output($result);
        }else{
            $result['stat']=0;
            $result['msg']="خطایی رخ داده اس لطفا بعدا امتحان کنید";
            vip_output($result);
        }
    }
}
function vip_output($result){
    if(is_array($result)){
        die(json_encode($result));
    }
    die($result);
}

//transactions
function vip_delete_transaction(){
    $tid=intval($_POST['tid']);
    global $wpdb,$table_prefix;
    $res=$wpdb->query("DELETE FROM {$table_prefix}vip_transactions WHERE id={$tid} LIMIT 1");
    if($res){
        $result['stat']=1;
        $result['msg']="تراکنش مورد نظر با موفقیت حذف گردید";
        die(json_encode($result));
}else{
        $result['stat']=0;
        $result['msg']="خطایی در سمت سرور رخ داده است لطفا بعدا امتحان کنید";
        die(json_encode($result));
}
}
function vip_confirm_transaction(){
    $tid=intval($_POST['tid']);
    global $wpdb,$table_prefix;
    $res=$wpdb->query("UPDATE {$table_prefix}vip_transactions SET status=1 WHERE id={$tid} LIMIT 1");
    if($res){
        die("تراکنش مورد نظر با موفقیت تایید شد");
}else{
    die("خطایی در سمت سرور رخ داده است لطفا بعدا امتحان کنید");
}
}
