(function ($) {

    $(function () {

        // prevent edit action
//        $('.row-title').on('click', function (e) {
//            e.preventDefault();
//            e.stopPropagation();
//            return false;
//        });

        console.log("loaded");

        // remove manage-column sorting
        $('.manage-column .sorting-indicator').remove();
        $('.manage-column a').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        });

        $('#addtag .term-subtitle-wrap').prependTo('#addtag');
        $('#addtag .term-name-wrap').prependTo('#addtag');

        $(document).ajaxSuccess(function (event, xhr, settings) {
            if (settings.data.match(/action=add-tag/) && !xhr.responseText.match(/wp_error/)) {
                $('#term-url').val('');
                $('#term-category_id').val(-1);
                $('input[name="tax_input[cmra_tag][]"]').removeAttr('checked');
                $('#term-show-checkbox-hidden').val('');
                $('#term-show-checkbox').removeAttr('checked');
            }
        });

        // filter dropdown
        var filter = $('<div class="alignleft actions"></div>');
        $(filter).append($('#term-category_id')
                .clone()
                .attr('id', 'filter-category_id')
                .attr('name', 'cmra_category'));
        $(filter).find('#filter-category_id option').first().text('All categories');
        if (getParameterByName('cmra_category') !== null) {
            $(filter).find('#filter-category_id').val(parseInt(getParameterByName('cmra_category')));
        }
        $(filter).append('<input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">')
        $('.bulkactions').first().after(filter);

        $('#post-query-submit').on('click', function (e) {
            e.stopPropagation();
            var val = parseInt($('#filter-category_id option:selected').val());
            if (val <= 0) {
                val = null;
            }
            location.href = UpdateQueryString('cmra_category', val);
            return false;
        });

    });

    // http://stackoverflow.com/a/901144
    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    // http://stackoverflow.com/a/11654596
    function UpdateQueryString(key, value, url) {
        if (!url)
            url = window.location.href;
        var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
                hash;
        if (re.test(url)) {
            if (typeof value !== 'undefined' && value !== null)
                return url.replace(re, '$1' + key + "=" + value + '$2$3');
            else {
                hash = url.split('#');
                url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
                if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                    url += '#' + hash[1];
                return url;
            }
        } else {
            if (typeof value !== 'undefined' && value !== null) {
                var separator = url.indexOf('?') !== -1 ? '&' : '?';
                hash = url.split('#');
                url = hash[0] + separator + key + '=' + value;
                if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                    url += '#' + hash[1];
                return url;
            } else
                return url;
        }
    }

})(jQuery);

validateForm = function (form) {
    return !jQuery(form)
            .find('.form-required')
            .filter(function () {
                return jQuery('input:visible', this).val() === '' || jQuery('select:visible', this).val() === '-1';
            })
            .addClass('form-invalid')
            .find('input:visible, select:visible')
            .change(function () {
                jQuery(this).closest('.form-invalid').removeClass('form-invalid');
            })
            .length;
};

inlineEditTax.cmraedit = inlineEditTax.edit;

inlineEditTax.edit = function (id) {
    var tag_id = id;
    if (typeof (tag_id) === 'object') {
        tag_id = this.getId(tag_id);
    }
    var val = jQuery('td.cmra_url', '#tag-' + tag_id).text();
    jQuery(':input[name="cmra_url"]', '.inline-edit-row').val(val);
    jQuery(':input[name="slug"]', '.inline-edit-row').closest('label').hide();
    return inlineEditTax.cmraedit(id);
};
