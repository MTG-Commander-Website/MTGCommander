(function ($) {

    $(function () {
      $submit = $('#submit');
      $tagName = $('#tag-name');
      $tagDesc = $('#tag-description');
      $tagSlug = $('#tag-slug');
      $theList = $('#the-list');
        $(document).ajaxSuccess(function(e, request, settings){
            var result = {};
            settings.data.split('&').forEach(function(x){
                var arr = x.split('=');
                arr[1] && (result[arr[0]] = arr[1]);
            });

            if (result.action == 'delete-tag') {
              $submit.attr('disabled', false);
              $tagName.attr('disabled', false);
              $tagSlug.attr('disabled', false);
              $tagDesc.attr('disabled', false);
            }
            if (result.action == 'add-tag' && count == 1) {
              $submit.attr('disabled', true);
              $tagName.attr('disabled', true);
              $tagSlug.attr('disabled', true);
              $tagDesc.attr('disabled', true);
            }

        });
        var count = $theList.children().length;
        if (count == 1 && $theList.not(':has(.no-items)').length == 1) {
          $submit.attr('disabled', true);
          $tagName.attr('disabled', true);
          $tagSlug.attr('disabled', true);
          $tagDesc.attr('disabled', true);
        }
    })
})(jQuery);
