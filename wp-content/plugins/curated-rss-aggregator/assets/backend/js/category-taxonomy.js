(function ($) {

    $(function () {
        $(document).ajaxSuccess(function (event, xhr, settings) {
            if (typeof settings.data != "undefined" && settings.data.match(/action=add-tag/) && !xhr.responseText.match(/wp_error/)) {
                $('input[name="tax_input[cmra_list][]"]').removeAttr('checked');
                $('#term-color').wpColorPicker('color', '#FFFFFF');
                $('#term-color').val('');
                $('#term-feed-url').val('');
                $('#term-interval').val('3hours');
                $('#term-delete-after').val('604800');
                $('#term-show-favicons').val('0');
                $('#parent').val('-1');
                $('#term-advanced-subtitle-customization').removeAttr('checked');
                $('#term-advanced-subtitle').hide();
                $('#term-advanced-subtitle-namespace').val('SIMPLEPIE_NAMESPACE_RSS_20');
                $('#fix_relative_paths').removeAttr('checked');
                $('#term-user-agent').val('');
            }
        });

        $('#term-advanced-subtitle-customization').on('change', function () {
            $('#term-advanced-subtitle').toggle();
            $('.term-advanced-subtitle').toggle();
            $('#term-advanced-subtitle-namespace').val('SIMPLEPIE_NAMESPACE_RSS_20');
            $('#term-advanced-subtitle-tag').val('');
        });

        $('body').on('click', '.cmra-row-action-refresh', function () {
            var _this = this;
            if ($(this).hasClass('disabled')) {
                return false;
            }
            if (!confirm("Do you want remove all RSS links and fetch them again?\n\nRefreshing usualy takes couple minutes.")) {
                return false;
            }
            $(this).addClass('disabled');
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'cmra_category_refresh',
                    term_id: $(_this).data('term-id')
                }
            }).always(function (data) {
                if (data.status) {
                    $(_this).text('Refresh in proggress');
                } else {
                    $(_this).text('Error - try later');
                }
            })

        });
    });

})(jQuery);