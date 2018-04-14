<?php

function vip_dashboard_page() {
    ?>
    <div class="wrap ">
        <h3>صفحه اصلی</h3>

    </div>
    <?php
}

function vip_products_page() {
    global $wpdb, $table_prefix;
    $err = false;
    $succ = false;
    $msg = "";
    if (isset($_POST['submit_add'])) {
        $type = $_POST['product_name'];
        $title = $_POST['txt_title'];
        $price = $_POST['txt_price'];
        $days = $_POST['txt_days'];
        $sql = "INSERT INTO {$table_prefix}vip_products (product_name,product_title,product_price,product_days,product_discount,product_status)";
        $sql.=" VALUES ('{$type}','{$title}',{$price},{$days},0,1) ";
        $res = $wpdb->query($sql);
        if ($res) {
            $succ = true;
            $msg = "محصول جدید با موفقیت اضافه شد";
        } else {
            $err = true;
            $msg = "در اجرای عملیات خطایی رخ داده است";
        }
    }
    if (isset($_POST['submit_action_group'])) {
        $action = $_POST['action_group'];
        switch ($action) {
            case 'delete_products':vip_delete_products();
                break;
            case 'change_price':vip_change_price();
                break;
        }
    }
    $products = $wpdb->get_results("SELECT * FROM {$table_prefix}vip_products");
    ?>
    <div class="wrap">
        <h3>محصولات وب سایت</h3>
        <form action="" method="POST">
            <div class="vip_container">
                <div id="vip_loader">لطفا صبر کنید ...</div>
                <?php if ($succ): ?>
                    <div class="vip_message vip_success"><?php echo $msg; ?></div>
                <?php elseif ($err): ?>
                    <div class="vip_message vip_error"><?php echo $msg; ?></div>
                <?php endif; ?>
                <div class="vip_actions">
                    <div style="width: 80%" class="alignleft">    
                        <label>کار های گروهی:
                            <select id="bulk_action" style="width:30%"  name="action_group">
                                <option value="-1">لطفا انتخاب کنید ...</option>
                                <option value="delete_products">حذف کردن محصولات</option>
                                <option value="change_price">تغییر قیمت محصولات</option>
                            </select>
                        </label>
                        <input type="text" name="txt_price" id="txt_price" placeholder="قیمت  محصول .." >
                        <input name="submit_action_group" type="submit" class="button" value="انجام بده">
                    </div>
                    <div class="alignright">
                        <a href="#" id="vip_add_product" title="اضافه کردن محصول جدید">
                            <img src="<?php echo VIP_IMG_URL . 'add.png'; ?>">
                        </a>
                    </div>
                </div>
                <div class="vip_actions" id="vip_panel" style="display: none;"></div>
                <table class="widefat">
                    <tr>
                        <th class="vip_center">
                            <input type="checkbox" id="select_all">
                        </th>
                        <th class="vip_center">عنوان طرح</th>
                        <th class="vip_center">قیمت طرح</th>
                        <th class="vip_center">تعداد روز</th>
                        <th class="vip_center">تخفیف</th>
                        <th class="vip_center">وضعیت</th>
                        <th class="vip_center">
                            عملیات
                        </th>
                    </tr>
                    <?php if (count($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="vip_center">
                                    <input type="checkbox"  name="selected[]" value="<?php echo $product->product_ID; ?>">
                                </td>
                                <td class="vip_center">
                                    <?php echo $product->product_title; ?>
                                </td >
                                <td class="vip_center" >
                                    <?php echo number_format($product->product_price); ?>
                                </td>
                                <td class="vip_center">
                                    <?php echo $product->product_days; ?>
                                </td>
                                <td class="vip_center">
                                    <?php echo $product->product_discount . ' %'; ?>
                                </td>
                                <td class="vip_center">
                                    <?php echo vip_get_status($product->product_status); ?>
                                </td>
                                <td class="vip_center">
                                    <a href="#" class="vip_delete_product" data-id="<?php echo $product->product_ID; ?>" title="حذف کردن محصول">
                                        <img src="<?php echo VIP_IMG_URL . 'delete.png'; ?>">
                                    </a>
                                    <a href="#" class="vip_change_product_status" data-id="<?php echo $product->product_ID; ?>" title="تغییر وضعیت">
                                        <img src="<?php echo VIP_IMG_URL . 'change.png'; ?>">
                                    </a>
                                    <a href="#" class="vip_product_discount" data-id="<?php echo $product->product_ID; ?>" title="تغییر تخفیف">
                                        <img src="<?php echo VIP_IMG_URL . 'discount.png'; ?>">
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>
        </form>
    </div>
    <?php
}

function vip_users_page() {
    global $wpdb, $table_prefix;
    $where = " WHERE 1 ";
    if (isset($_GET['query']) && !empty($_GET['query'])) {
        $query = $wpdb->escape($_GET['query']);
        $where.="AND user_login LIKE '%{$query}%'";
    }
    if (isset($_POST['users_bulk_submit'])) {
        $bulk = $_POST['user_bulk_action'];
        switch ($bulk) {
            case 'vip_user_create':vip_bulk_add_user();
                break;
            case 'vip_user_up':vip_bulk_user_up();
                break;
            case 'vip_user_down':vip_bulk_user_down();
                break;
            case 'vip_user_delete':vip_bulk_user_delete();
                break;
        }
    }
    $sql = "SELECT {$wpdb->users}.user_login,{$wpdb->users}.ID,{$wpdb->usermeta}.* FROM {$wpdb->users} LEFT JOIN {$wpdb->usermeta} ON ({$wpdb->users}.ID={$wpdb->usermeta}.user_id AND meta_key='vip_plan_id'){$where}";
    $users = $wpdb->get_results($sql);
    $plans = $wpdb->get_results("SELECT product_ID,product_name,product_title FROM {$table_prefix}vip_products");
    ?>
    <div class="wrap">
        <h3>مدیریت کاربران</h3>
        <div class="vip_container">
            <div id="vip_loader">لطفا صبر کنید ...</div>
            <div class="vip_actions">
                <div class="alignright">
                    <form action="" method="get">
                        <input type="hidden" name="page" value="vip_users">
                        <label for="query">جستجو با نام کاربری :
                            <input type="text" name="query">
                            <input class="button" type="submit" value="جستجو...">
                        </label>

                    </form>
                </div>
            </div>
            <form action="" method="post">
                <div class="vip_actions">
                    <label for="user_bulk_action">عملیات گروهی :
                        <select style="width: 300px;" id="user_bulk_action" name="user_bulk_action">
                            <option value="-1">انتخاب کنید ...</option>
                            <option value="vip_user_create">ایجاد کاربر ویژه</option>
                            <option value="vip_user_up">اضافه کردن اعتبار روزانه</option>
                            <option value="vip_user_down">کم کردن اعتبار روزانه</option>
                            <option value="vip_user_delete">حذف کردن کاربر ویژه</option>
                        </select>
                        <select id="vip_plan" name="vip_plan">
                            <?php foreach ($plans as $product): ?>
                                <option value="<?php echo $product->product_ID; ?>" ><?php echo $product->product_title; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" id="txt_days" name="txt_days" placeholder="تعداد روز  ...">
                        <input type="submit" name="users_bulk_submit" value="انجام بده" class="button">
                    </label>
                </div>
                <table class="widefat">
                    <tr>
                        <th class="vip_center">
                            <input type="checkbox" id="select_all">
                        </th>
                        <th class="vip_center">
                            نام کاربری
                        </th>
                        <th class="vip_center">
                            نوع عضویت
                        </th>
                        <th class="vip_center">
                            تاریخ انقضاء
                        </th>
                        <th class="vip_center">
                            تعداد روز
                        </th>
                        <th class="vip_center">
                            عملیات
                        </th>
                    </tr>
                    <?php if (count($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="vip_center" >
                                    <input type="checkbox" name="selected[]" value="<?php echo $user->ID; ?>">
                                </td>
                                <td class="vip_center" >
                                    <?php echo $user->user_login; ?>
                                </td>
                                <td class="vip_center" >
                                    <?php echo get_user_plan_title($user->user_id); ?>
                                </td>
                                <td class="vip_center" >
                                    <?php echo vip_get_persian_date(get_user_expire($user->user_id)); ?>
                                </td>
                                <td class="vip_center" >
                                    <?php echo get_user_remain_day($user->user_id); ?>
                                </td>
                                <td class="vip_center" >
                                    <?php if ($user->user_id): ?>
                                        <a href="#" class="add_user_credit" data-id="<?php echo $user->user_id; ?>" title="اضافه کردن اعتبار به کاربر">
                                            <img src="<?php echo VIP_IMG_URL . 'up.png'; ?>">
                                        </a>
                                        <a href="#" class="sub_user_credit" data-id="<?php echo $user->user_id; ?>" title="اضافه کردن اعتبار به کاربر">
                                            <img src="<?php echo VIP_IMG_URL . 'down.png'; ?>">
                                        </a>
                                        <a href="#" class="delete_user_vip" data-id="<?php echo $user->user_id; ?>" title="اضافه کردن اعتبار به کاربر">
                                            <img src="<?php echo VIP_IMG_URL . 'delete.png'; ?>">
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="vip_center" >
                                هیچ کاربری یافت نشد
                            </td> 
                        </tr>
                    <?php endif; ?>
                </table>
            </form>
        </div>
    </div>
    <?php
}

function vip_transactions_page() {
    global $wpdb, $table_prefix;
    $where = " WHERE 1 ";
    if (isset($_GET['query']) && !empty($_GET['query'])) {
        $query = $wpdb->escape($_GET['query']);
        $where.="AND user_login LIKE '%{$query}%'";
    }
    if (isset($_POST['trans_bulk_submit'])) {
        $bulk = $_POST['trans_bulk_action'];
        switch ($bulk) {
            case 'confirm_transaction':vip_bulk_confirm_transaction();
                break;
            case 'delete_transaction':vip_bulk_delete_transaction();
                break;
        }
    }
    $sql = "SELECT {$wpdb->users}.user_login,{$table_prefix}vip_transactions.* FROM {$wpdb->users} JOIN {$table_prefix}vip_transactions ON ({$wpdb->users}.ID={$table_prefix}vip_transactions.user_id){$where}";
    $transactions = $wpdb->get_results($sql);
    $products = $wpdb->get_results("SELECT product_ID,product_title FROM {$table_prefix}vip_products");
    $product = array();
    foreach ($products as $p) {
        $product[$p->product_ID] = $p->product_title;
    }
    ?>
    <div class="wrap">
        <h3>مدیریت تراکنش ها</h3>
        <div class="vip_container">
            <div id="vip_loader">لطفا صبر کنید ...</div>
            <div class="vip_actions">
                <div class="alignright">
                    <form action="" method="get">
                        <input type="hidden" name="page" value="vip_transactions">
                        <label for="query">جستجو با نام کاربری :
                            <input type="text" name="query">
                            <input class="button" type="submit" value="جستجو...">
                        </label>

                    </form>
                </div>
            </div>
            <form action="" method="post">
                <div class="vip_actions">
                    <label for="trans_bulk_action">عملیات گروهی :
                        <select style="width: 300px;" id="trans_bulk_action" name="trans_bulk_action">
                            <option value="-1">انتخاب کنید ...</option>
                            <option value="confirm_transaction">تایید کردن ترکنش ها</option>
                             <option value="delete_transaction">حذف کردن تراکنش ها</option>
                        </select>
                        <input type="submit" name="trans_bulk_submit" value="انجام بده" class="button">
                    </label>
                </div>
                <table class="widefat">
                    <tr>
                        <th class="vip_center">
                            <input type="checkbox" id="select_all">
                        </th>
                        <th class="vip_center">
                            نام کاربری
                        </th>
                        <th class="vip_center">
                            محصول
                        </th>
                        <th class="vip_center">
                            بانک
                        </th>
                        <th class="vip_center">
                            شماره سفارش
                        </th>
                        <th class="vip_center">
                            شماره مرجع
                        </th>
                        <th class="vip_center">
                            مبلغ
                        </th>
                        <th class="vip_center">
                            تاریخ
                        </th>
                        <th class="vip_center">
                            وضعیت
                        </th>
                        <th class="vip_center">
                            عملیات
                        </th>
                    </tr>
                    <?php if (count($transactions)): ?>
                        <?php foreach ($transactions as $trans): ?>
                            <tr>
                                <td class="vip_center" >
                                    <input type="checkbox" name="selected[]" value="<?php echo $trans->id; ?>">
                                </td>
                                <td class="vip_center" >
                                    <?php echo $trans->user_login; ?>
                                </td>
                                <td class="vip_center" >
                                    <?php echo $product[$trans->product_id]; ?>
                                </td>
                                <td class="vip_center" >
                                    <?php echo vip_get_bank_title($trans->bank); ?>
                                </td>
                                <td class="vip_center" >
                                    <?php echo $trans->order_id; ?>
                                </td>
                                <td class="vip_center" >
                                    <?php echo $trans->ref_id; ?>
                                </td>
                                <td class="vip_center" >
                                    <?php echo $trans->amount; ?>
                                </td>
                                <td class="vip_center" >
                                    <?php echo $trans->date; ?>
                                </td>
                                </td>
                                <td class="vip_center" >
                                    <?php echo vip_get_trans_status($trans->status); ?>
                                </td>
                                <td class="vip_center" >
                                    <a href="#" class="delete_transactions" data-id="<?php echo $trans->id; ?>">
                                        <img src="<?php echo VIP_IMG_URL . 'delete.png' ?>">
                                    </a>
                                    <?php if (!$trans->status): ?>
                                        <a href="#" class="confirm_transactions" data-id="<?php echo $trans->id; ?>">
                                            <img src="<?php echo VIP_IMG_URL . 'confirm.png' ?>">
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <?php endif; ?>
                </table>
            </form>
        </div>
    </div>
    <?php
}
