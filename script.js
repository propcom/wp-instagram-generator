(function ($) {
    $('.js-checkbox').on('click', function () {
        $('.js-next').prop("disabled", this.checked);
    }).change();

    $('.js-searchtype').on('click', function () {
        $('.js-checktype').val(this.value);
    }).change();
})(jQuery);