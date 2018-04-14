<?php
add_filter('the_content','wpvip_filter_content',100);
add_shortcode("vip","vip_form");
function wpvip_filter_content($content){
    global $post,$current_user;
    get_currentuserinfo();
    if(is_singular()){
        $post_level=  get_post_meta($post->ID,'vip-level',true);
        if(empty($post_level) || $post_level=="normal" || vip_is_user_gold($current_user->ID)){
            return $content;
        }elseif($post_level==="vip-silver" && vip_is_user_silver($current_user->ID)){
            return $content;
        }elseif($post_level==="vip-bronze" && (vip_is_user_bronze($current_user->ID) || vip_is_user_silver($current_user->ID))){
            return $content;
        }
        $html= '<div class="sp_content">';
        $html.='<p>این محتوا مخصوص کاربران ویژه وب سایت می باشد</p>';
        $html.='</div>';
        return $html;
    }
    return $content;
}
function vip_is_user_gold($user_id){
    $plan=get_user_meta($user_id,'vip_plan_name',true); 
    return ($plan=='vip-gold');
}
function vip_is_user_silver($user_id){
    $plan=get_user_meta($user_id,'vip_plan_name',true); 
    return ($plan=='vip-silver');
}
function vip_is_user_bronze($user_id){
    $plan=get_user_meta($user_id,'vip_plan_name',true); 
    return ($plan=='vip-bronze');
}
function vip_form(){
global $wpdb,$table_prefix,$user_ID,$current_user;
if(isset($_POST['status']) && $_POST['status'] == 100){
    
    $Status = $_POST['status'];
    $Refnumber = $_POST['refnumber'];
    $Resnumber = $_POST['resnumber'];
    $client = new SoapClient('http://merchant.parspal.com/WebService.asmx?wsdl');
    $res = $client->VerifyPayment(array(
        "MerchantID" => $MerchantID ,
        "Password" =>$Password , 
        "Price" =>$_SESSION['price'],
        "RefNum" =>$Refnumber )
            );
    $Status = $res->verifyPaymentResult->ResultStatus;
    $PayPrice = $res->verifyPaymentResult->PayementedPrice;
    if($Status == 'success')// Your Peyment Code Only This Event
    {
            $wpdb->query("UPDATE {$table_prefix}vip_transactions SET ref_id='{$Refnumber}',status=1 WHERE order_id='{$_SESSION['order_id']}' LIMIT 1");
            vip_add_vip_to_user($user_ID,$_SESSION['pid']);
            echo '<div style="color:green; font-family:tahoma; direction:rtl; text-align:right">
            پرداخت با موفقیت انجام شد ، شماره رسید پرداخت : '.$Refnumber.' ،  مبلغ پرداختی : '.$PayPrice.' !
            <br /></div>';
    }else{
            echo '<div style="color:green; font-family:tahoma; direction:rtl; text-align:right">
            خطا در پردازش عملیات پرداخت ، نتیجه پرداخت : '.$Status.' !
            <br /></div>';
    }
    
    }
if(isset($_POST['submit_pay'])){
        $pid=intval($_POST['product']);
        if($pid){
            $product=$wpdb->get_row("SELECT product_price FROM {$table_prefix}vip_products WHERE product_ID={$pid}");
            $_SESSION['pid']=$pid;
            $_SESSION['uid']=$user_ID;
            if(!$_SESSION['order_id']){
                
                $_SESSION['order_id']=time().$user_ID;
            }
            $_SESSION['price']=$product->product_price;
            $date=date("Y-m-d H:i:s");
            $rp='http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
            $config=array(
                "MerchantID" =>"",
                "Password" =>"",
                "Price" =>$_SESSION['price'],
                "ReturnPath" =>$rp,
                "ResNumber" =>$_SESSION['order_id'], 
                "Description" =>"پرداخت برای عضویت ویژه وب سایت طراح وب",
                "Paymenter" =>0, 
                "Email" =>$current_user->email,
                "Mobile" =>"09123456789"
            );
            $client = new SoapClient('http://merchant.parspal.com/WebService.asmx?wsdl');
            $res = $client->RequestPayment($config);
            $PayPath = $res->RequestPaymentResult->PaymentPath;
            $Status = $res->RequestPaymentResult->ResultStatus;
            if($Status == 'Succeed'){
                $wpdb->query("INSERT INTO {$table_prefix}vip_transactions (user_id,product_id,bank,order_id,amount,date)"
                . " VALUES({$user_ID},{$pid},'parspal','{$_SESSION['order_id']}',{$_SESSION['price']},'{$date}')");
                header("Location: {$PayPath}");
            }
        }
}
$products=$wpdb->get_results("SELECT product_ID,product_title FROM {$table_prefix}vip_products WHERE product_status=1");
?>
<div id="vip_form">
  <?php if(is_user_logged_in()): ?>
      <form action="" method="POST">
        <label for="product">
            <select style="width: 300px;" class="form-control" name="product">
                <?php foreach ($products as $product): ?>
                <option value="<?php echo $product->product_ID; ?>"><?php echo $product->product_title; ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <p>
            <input type="submit" class="btn btn-default" value="خرید" name="submit_pay">
        </p>
    </form>
    <?php else: ?>
    <p>کاربر گرامی برای خرید عضویت ویژه باید در وب سایت لاگین نمایید</p>
    <?php endif; ?>
</div>
<?php

}
