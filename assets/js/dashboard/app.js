import Vue from 'vue'
import '../../css/app.scss';
import '../sidebar'
import 'datatables'
import 'jquery-form/dist/jquery.form.min.js';

// require jQuery normally
const $ = require('jquery');

// create global $ and jQuery variables
global.$ = global.jQuery = $;

global.Vue = Vue;
import transFilter from 'vue-trans';
global.Vue.use(transFilter);

global.MSG_DELETE_ITEM = 'Do you want to delete this item?';

(function() {
    'use strict'
    $(function() {
        
        $('[data-toggle=checkbox]').on('change', function () {
            var self = $(this),
                target = self.data('target'),
                checked = self.is(':checked');
                
            $(target).prop('checked', checked);
        });
        
        if ($.listen) {
            $.listen('parsley:field:error', function (fieldInstance) {
                $('form').find('[type=submit],submit').prop('disabled', false);
                var arrErrorMsg = ParsleyUI.getErrorsMessages(fieldInstance),
                    errorMsg = arrErrorMsg.join('; ');
                fieldInstance.$element
                    .popover({
                        container: 'body',
                        placement: 'top',
                        content: errorMsg,
                        trigger: 'focus'
                    })
                    .parent()
                        .find('.parsley-errors-list')
                        .remove()
                ;
                var topSiteOffset = 200;
                 
                var error = $(':input.parsley-error:first'), tabs;
                if (error.length) {
                    $(window).scrollTop(error.offset().top - topSiteOffset);
                    // caso o campo esteja dentro de uma aba, muda para ela
                    tabs = error.parents('.tab-pane');
                    if (tabs.length) {
                        $("[href='#" + tabs.prop('id') + "']").tab('show');
                    }
                    error.focus();
                }
            });
            
            $.listen('parsley:field:success', function (fieldInstance) {
//                fieldInstance.$element.popover('destroy');
                setTimeout(function() {
                    checkTabErrors('.parsley-error:input');
                }, 100);
            });
            
            $.listen('parsley:form:error', function() {
                setTimeout(function() {
                    checkTabErrors('.parsley-error:input');
                    
                    $('div.modal.in').each(function() {
                        var modal = $(this);
                        if (modal.find('.parsley-error').length === 0) {
                            modal.modal('hide');
                        }
                    });
                }, 100);
            });
        }
        
        // form tab error count
        function checkTabErrors(errorSelector) {
            $('.tab-pane').each(function() {
                var tab = $(this);
                var errors = tab.find(errorSelector).length;
                var link = $('.nav-tabs a[href="#' + tab.prop('id') + '"]');
                link.find('.badge').remove();
                if (errors > 0) {
                    link.append(' <span class="badge" style="background:black; color:white">' + errors + '</span>');
                }
            });
        }
        
        // symfony error
        checkTabErrors('.has-error:input');
        
        $('form').on('submit', function() {
            // desabilitando o botoes de submit para evitar multiplos envios
        //    $(this).find('[type=submit],submit').prop('disabled', true);
        });
        
    });
    
})();