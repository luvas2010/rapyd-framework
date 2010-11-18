
//$(document).ready(function() { $('.dataform').formTabs({ propery: 'value'}); });

(function($) {

    $.fn.formTabs = function(settings) {

	settings = jQuery.extend({
			//property: 'value',
		}, settings);

        this.find('.df_body').prepend('<ul></ul>');
        var ul = this.find("ul");

        //console.log('custom property: ', settings.propery);
        this.find('fieldset').each(function() {
            $(this).css('border','none');
            $(this).find('legend').css('display','none');
            $(this).wrap('<div id="tab_'+$(this).attr('id')+'" />');
            $(ul).append('<li><a href="#tab_'+$(this).attr('id')+'"><span>'+$(this).find('legend').text()+'</span></a></li>');

        });
        this.find('.df_body').tabs();
    };


})(jQuery);