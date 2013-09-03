jQuery(function($){ //make sure DOM is loaded and pass $ for use
    $('#post').submit(function(){ // the form submit function
         $('.required').each(function(){
           if( $(this).val == '-1' || $(this).val == '' ){ // checks if empty or has a predefined string
             //insert error handling here. eg $(this).addClass('error');
             return false; //return false if empty and stop submit event
           }
         })
    });

    $('select[name="cu_pr_order_status"]').chosen();

    var item_select = $("select#cu_pr_item_id"),
        order_select= $("select#cu_pr_order_id"),
        item_uid    = $('input[name="cu_pr_item_uid"]'),
        qty_msg     = $('.cu-pr-qty-msg');

    order_select.chosen().change(function(){
        var data = {
            action : 'cu_pr_order_selected',
            order : $(this).val()
        };
        jQuery.post(ajaxurl, data, function(ret){
            if(ret.count > 1){
                item_select.siblings('.select-notify').html('The order has more than one product, chose one product from below.');
                item_select.html('<option>Select One product.</option>');
            }else{
                item_select.siblings('.select-notify').html('One Product Selected.');
                item_select.html('');
            }

            $.each(ret.items, function(i, obj){
                item_select.append('<option value="'+ i +'">'+ obj.name +'</option>');
            });

            item_select.parent('.item-select').slideDown(function(){
                item_select.chosen();
                item_select.trigger("liszt:updated");
                item_uid.trigger('new_item');
            });
        }, 'json');
    });

    item_select.chosen().change(function(){
        item_uid.trigger('new_item');
    });

    item_uid.on('new_item', function(){
        var $this = $(this),
            data = {
                action : 'cu_pr_item_selected',
                item   : item_select.val(),
                order  : order_select.val()
            };
        if(parseInt(data.item) > 0){
            jQuery.post(ajaxurl, data, function(ret){
                qty_msg.html(ret.msg);
                $this.val(ret.item_uid);
            }, 'json');
        }else{
             qty_msg.html('');
             $this.val('');
        }
    });

    //notifying customer action
    $('#notify_customer').click(function(e){
        e.preventDefault();
        var $this = $(this),
            data = {
                action : 'cu_pr_notify_customer',
                post_id   : $('#post_ID').val(),
                order  : order_select.val()
            };

        PostForm.showSpinner($this.parent());
        
        jQuery.post(ajaxurl, data, function(ret){
           if(ret.status == 'true'){
                $this.siblings('.spinner').delay(1300).remove();
                $this.parent().append('<span class="msg">Email Sent!</span>');
            }
        }, 'json');
    });

    $('#submit_privacy_tpls').click(function(e){
        e.preventDefault();

        PostForm.init('cu_pr_tpl_update', 'form[name="privacy_tpl_form"]');
    });

    $('#submit_conditions_tpls').click(function(e){
    	e.preventDefault();

        PostForm.init('cu_pr_tpl_update', 'form[name="conditions_tpl_form"]');
    });

    $('#submit_customer_email_tpls').click(function(e){
        e.preventDefault();

        PostForm.init('cu_pr_tpl_update', 'form[name="customer_email_tpl_form"]');
    });

    $('#submit_admin_email_tpls').click(function(e){
        e.preventDefault();

        PostForm.init('cu_pr_tpl_update', 'form[name="admin_email_tpl_form"]');
    });
});

PostForm = {
    init: function(action, form){
        tinyMCE.triggerSave();
        var data = {
            action: action,
            form_data: jQuery(form).serialize()
        };

        PostForm.showSpinner(jQuery(form).find('p.submit'));

        jQuery.post(ajaxurl, data, function(ret) {
            if(ret.status == 'true'){
                jQuery(form).find('p.submit .spinner').delay(1300).remove();
                jQuery(form).find('p.submit').append('<span class="msg">Saved Successfully!</span>');
            }
        }, 'json');
    },
    showSpinner: function(container){
        container.append(
            '<span class="spinner" style="float:left;"></span>'
        );
        container.find('.msg').remove();
        container.find('.spinner').show();
    }
};