jQuery(document).ready(function($){
    //products
    $('.vip_delete_product').on('click',function(){
        if(!confirm("برای حذف کردن این محصول اطیمان دارید؟")){return false;}
        var el=$(this);
        var pid=el.data('id');
        var loader=$('#vip_loader');
        loader.fadeIn(300);
        $.ajax({
            url:wpvip.ajaxurl,
            type:'post',
            data:{
                action:'vip_delete_product',
                pid:pid
            },
            success:function(response){
                loader.fadeOut(300);
                alert(response);
            },
            error:function(){
                
            }
        });
        return false;
    });
    $('.vip_change_product_status').on('click',function(){
        var el=$(this);
        var pid=el.data('id');
        var loader=$('#vip_loader');
        loader.fadeIn(300);
        $.ajax({
            url:wpvip.ajaxurl,
            type:'post',
            data:{
                action:'vip_change_product_status',
                pid:pid
            },
            success:function(response){
                loader.fadeOut(300);
                alert(response);
            },
            error:function(){
                
            }
        });
        return false;
    });
    $('.vip_product_discount').on('click',function(){
        var discount=prompt("مقدار تخفیف را وارد کنید");
        if(discount==null || isNaN(discount) || discount==""){
            return false;
        }
        var el=$(this);
        var pid=el.data('id');
        var loader=$('#vip_loader');
        loader.fadeIn(300);
        $.ajax({
            url:wpvip.ajaxurl,
            type:'post',
            data:{
                action:'vip_product_discount',
                pid:pid,
                off:discount
            },
            success:function(response){
                loader.fadeOut(300);
                alert(response);
            },
            error:function(){
                
            }
        });
        return false;
    });
    $('#vip_add_product').on('click',function(){
        var panel=$('#vip_panel');
        var loader=$('#vip_loader');
        loader.fadeIn(300);
        $.ajax({
            url:wpvip.ajaxurl,
            type:'post',
            data:{
                action:'vip_add_product',
            },
            success:function(response){
                loader.fadeOut(300);
                panel.html(response).slideDown(300);
            },
            error:function(){
                
            }
        });
        return false;
    });
    $('#cancel').live('click',function(){
       $('#vip_panel').slideUp(300);
        return false;
    });
    //users
    $('.add_user_credit').on('click',function(){
        var days=prompt("تعداد روز برای افزایش اعتبار را وارد کنید:");
        if(days==="" || isNaN(days) || days===null){
            return false;
        }
        var el=$(this);
        var uid=el.data('id');
        var loader=$('#vip_loader');
        loader.fadeIn(300);
        $.ajax({
            url:wpvip.ajaxurl,
            type:'post',
            data:{
                action:'add_user_credit',
                uid:uid,
                days:days
            },
            success:function(data){
                alert(data);
                loader.fadeOut(300);
            }
        });
        return false;
    });
    $('.sub_user_credit').on('click',function(){
        var days=prompt("تعداد روز برای کاهش اعتبار را وارد کنید:");
        if(days==="" || isNaN(days) || days===null){
            return false;
        }
        var el=$(this);
        var uid=el.data('id');
        var loader=$('#vip_loader');
        loader.fadeIn(300);
        $.ajax({
            url:wpvip.ajaxurl,
            type:'post',
            data:{
                action:'sub_user_credit',
                uid:uid,
                days:days
            },
            success:function(data){
                
                alert(data);
                loader.fadeOut(300);
            }
        });
        return false;
    });
    $('.delete_user_vip').on('click',function(){
        if(!confirm("برای حذف این کاربر ویژه اطمینان دارید؟")){return false;}
        var el=$(this);
        var uid=el.data('id');
        var loader=$('#vip_loader');
        loader.fadeIn(300);
        $.ajax({
            url:wpvip.ajaxurl,
            type:'post',
            dataType:'json',
            data:{
                action:'delete_user_vip',
                uid:uid,
            },
            success:function(data){
                if(data.stat){
                    el.closest('tr').remove();
                    alert(data.msg);
                }else{
                    alert(data.msg);
                }
                loader.fadeOut(300);

            }
        });
        return false;
    });
    $('#user_bulk_action').on('change',function(){
        var el=$(this);
        if(el.val()==="vip_user_create"){
            $('#vip_plan').fadeIn(300);
        }else{
             $('#vip_plan').fadeOut(300);
        }
         if(el.val()==="vip_user_up" || el.val()==="vip_user_down"){
            $('#txt_days').fadeIn(300);
        }else{
              $('#txt_days').fadeOut(300);
        }
    });
    //tansactions
    $('.delete_transactions').on('click',function(evt){
        evt.preventDefault();
        if(!confirm("برای حذف کردن این تراکنش اطمینان دارید؟")){return false;}
        var el=$(this);
        var tid=el.data('id');
        var loader=$('#vip_loader');
        loader.fadeIn(300);
        $.ajax({
            url:wpvip.ajaxurl,
            type:'post',
            dataType:'json',
            data:{
                action:'vip_delete_transaction',
                tid:tid
            },
            success:function(response){
                loader.fadeOut(300);
               if(response.stat){
                   alert(response.msg);
               }else{
                   
               }
            },
            error:function(){
                alert("اطلاعاتی دریافت نشد دوباره سعی کنید");
            }
        });
    });
    $('.confirm_transactions').on('click',function(evt){
        evt.preventDefault();
        if(!confirm("برای تایید این تراکنش اطمینان دارید؟")){return false;}
        var el=$(this);
        var tid=el.data('id');
        var loader=$('#vip_loader');
        loader.fadeIn(300);
        $.ajax({
            url:wpvip.ajaxurl,
            type:'post',
            data:{
                action:'vip_confirm_transaction',
                tid:tid
            },
            success:function(response){
                loader.fadeOut(300);
                alert(response);
            },
            error:function(){
                alert("اطلاعاتی دریافت نشد دوباره سعی کنید");
            }
        });
    });
    
    //utility
    $(':checkbox#select_all').on('change',function(){
       $(':checkbox').not(this).prop('checked',this.checked);
    });
    $('#bulk_action').on('change',function(){
        var el=$(this);
        if(el.val()=='change_price'){
            $('#txt_price').fadeIn(300);
        }else{
            $('#txt_price').fadeOut(300);
        }
    });
});