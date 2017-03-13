jQuery(document).ready(function(){
    jQuery('.js-checkbox').change(function () {
        jQuery('.js-next').prop("disabled", this.checked);
    }).change()
});