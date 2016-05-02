$(function(){
    $.nette.init();

    $.nette.ext('open-modal', {
        success: function(payload, status, jqXHR, settings) {
            if (!settings.nette) {
                return;
            }

            var modal = settings.nette.el.data('open-modal');
            if (modal) {
                $(modal).modal('show');
            }
        }
    });
});
