(function() {  
    tinymce.create('tinymce.plugins.cpm_email_sc', {  
        init : function(ed, url) {  
            ed.addButton('cpm_email_sc', {  
                title : 'Add an email shortcode',    
                onclick : function() {  
                     //ed.selection.setContent('{{order_id}}'); 
                     //$('#cpm_email_shortcode_popup_trig').trigger('click');
                    var sc_popup_trig = document.getElementById('cpm_email_shortcode_popup_trig'),
                        sc_popup = document.getElementById('cpm_email_shortcode')[0];
                    
                    sc_popup_trig.click();
                    jQuery('.sc-list').one('click', 'li', function(e){
                        e.preventDefault();
                        tb_remove();
                        ed.selection.setContent(jQuery(this).data('sc'));
                        return false;
                    });
                }  
            });
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('cpm_email_sc', tinymce.plugins.cpm_email_sc);  
})();