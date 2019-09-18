(function ($) {
    "use strict";
    var options = cmraOptions ? cmraOptions : {};
    var masonryOptions = {
        itemSelector: '.cmra-category-box:not(.cmra-hidden)'
    };

    function cmra(config) {
        this.cache = {};
        this.cmra = config.cmra;
        this.options = config.options;
        this.masonryOptions = config.masonryOptions;
        this.init();
        this.filterInit();
        this.searchInit();
        this.tagInit();
        if( options.user_display_categories == '1' ) {
            this.visibleCategoriesInit();
        }
        //if (this.options.showCheckboxes) {
        try {
            this.checkboxInit();
        } catch (e) {
        }
        //}
    }

    cmra.prototype.init = function () {
        var _this = this;
        _this.cache.lastEditItemTime = 0;
        _this.cache.lastCreateItemTime = 0;
        $(this.cmra).find('.cmra-category[data-role="category"] .cmra-link').each(function () {
            if ($(this).data('create-time') && $(this).data('create-time') > _this.cache.lastCreateItemTime) {
                _this.cache.lastCreateItemTime = $(this).data('create-time');
            }
            if ($(this).data('edit-time') && $(this).data('edit-time') > _this.cache.lastEditItemTime) {
                _this.cache.lastEditItemTime = $(this).data('edit-time');
            }
        });
        setTimeout(function () {
            _this.updateJSPlaceholders();
            _this.masonry();
        }, 50);
        $(window).on('resize', debounce(function () {
            _this.masonry();
        }, 500));
        $('img').on('load', _this.cmra, debounce(function () {
            _this.masonry();
        }, 150));
    };

    cmra.prototype.masonry = function () {
        var _this = this;
        var columnWidth = Math.floor(($(_this.cmra).width() - 1) / _this.options.columnsCount);
        for (var i = 0; i < _this.options.columnsCount && columnWidth < 200; i++)
        {
            columnWidth = Math.floor(($(_this.cmra).width() - 1) / (_this.options.columnsCount - i));
        }
        $(_this.cmra).find('.cmra-content .cmra-category-box').width(columnWidth - 20);
        $(_this.cmra).find('.cmra-content').masonry($.extend({}, _this.masonryOptions, {columnWidth: columnWidth}));
    };

    cmra.prototype.updateJSPlaceholders = function () {
        var _this = this;
        var linksCount = $(_this.cmra).find('.cmra-category .cmra-category-link-list-entry:visible').length;
        var lastUpdateDate = new Date(_this.cache.lastEditItemTime * 1000);
        $(_this.cmra).find('.cmra-js-placeholder').each(function () {
            var html = $(this).data('html');
            html = html.replace('{links-count}', linksCount);
            html = html.replace('{last-update-date}', _this.cache.lastEditItemTime ? lastUpdateDate.toDateString() : '---');
            $(this).html(html);
        });
    };

    cmra.prototype.filter = function (arg) {
        var _this = this;

        $(_this.cmra).find('.cmra-filter li').removeClass('active');
        $(_this.cmra).find('.cmra-category').addClass('cmra-hidden').hide();
        $(_this.cmra).find('.cmra-category-link-list-entry').show();

        arg.search && _this.filterSearch(arg);
        arg.category && _this.filterCategory(arg);
        arg.tag && _this.filterTag(arg);

        _this.updateJSPlaceholders();
        _this.masonry();
    };

    cmra.prototype.highlight = function (s) {
        var _this = this;
        $(_this.cmra).find('.cmra-header').highlight(s, {className: 'cmra-highlight'});
        $(_this.cmra).find('.cmra-link').highlight(s, {className: 'cmra-highlight'});
    };

    cmra.prototype.unhighlight = function () {
        var _this = this;
        $(_this.cmra).find('.cmra-header').unhighlight({className: 'cmra-highlight'});
        $(_this.cmra).find('.cmra-link').unhighlight({className: 'cmra-highlight'});
    };

    cmra.prototype.filterSearch = function (arg) {
        var _this = this;
        $(_this.cmra).find('.cmra-category[data-role="category"] .cmra-category-link-list-entry').hide();
        $(_this.cmra).find('.cmra-category[data-role="category"]').each(function () {
            var category = this;
            if ($(category).find('.cmra-header').text().toLowerCase().indexOf(arg.search.toLowerCase()) > -1) {
                $(category).find('.cmra-category-link-list-entry').show();
                $(category).removeClass('cmra-hidden').show();
                $(_this.cmra).find('.cmra-filter .cmra-filter-list-entry[data-id={id}]'.replace('{id}', $(category).data('id'))).addClass('active');
            } else {
                $(category).find('.cmra-category-link-list-entry').each(function () {
                    var link = this;
                    if ($(link).text().toLowerCase().indexOf(arg.search.toLowerCase()) > -1) {
                        $(link).show();
                        $(category).removeClass('cmra-hidden').show();
                    }
                });
            }
        });
        _this.highlight(arg.search);
    }

    cmra.prototype.filterCategory = function (arg) {
        var _this = this;
        $(_this.cmra).find('.cmra-filter li').not('.cat-item-all').each(function () {
            if ($.trim($(this).data('name').toLowerCase()) === $.trim(arg.category.toLowerCase())) {
                $(this).addClass('active');
            }
            $(_this.cmra).find('.cmra-filter li.active').each(function () {
                $(_this.cmra).find('.cmra-category[data-id="{id}"][data-role="category"]'
                        .replace('{id}', $(this).data('id')))
                        .removeClass('cmra-hidden')
                        .show();
                $(_this.cmra).find('.cmra-category[data-id="{id}"][data-role="category"] .cmra-category'
                        .replace('{id}', $(this).data('id')))
                        .removeClass('cmra-hidden')
                        .show();
            });
        });
    };

    cmra.prototype.filterTag = function (arg) {
        var _this = this;
        $(_this.cmra).find('.cmra-category[data-role="tag"]').each(function () {
            if ($.trim($(this).find('.cmra-header').text().toLowerCase()) === $.trim(arg.tag.toLowerCase())) {
                $(this).removeClass('cmra-hidden').show();
            }
        });
    };

    cmra.prototype.filterInit = function () {
        var _this = this;
        $(_this.cmra).find('.cmra-filter .cat-item-all').html('All');
        if( options.user_display_categories == '1' ) {
            $(_this.cmra).find('.cmra-filter .cat-item-all').prepend('<input type="checkbox" name="display_category[]" checked="checked" /> ');
        }
        $(_this.cmra).find('.cmra-filter').on('click', 'li:not(.disabled-filter, .cat-item-all)', function () {
            $(_this.cmra).find('.cmra-search-input')
                    .val('category: {name}'.replace('{name}', $(this).data('name')))
                    .trigger('input')
                    .trigger('change');
        });
        $(_this.cmra).find('.cmra-filter .cat-item-all').on('click', function () {
            $(_this.cmra).find('.cmra-search-input').val('').trigger('change');
        });
    };

    cmra.prototype.searchInit = function () {
        var _this = this;
        $(_this.cmra).find('.cmra-search-input').on('change keyup paste', function () {
            _this.unhighlight();
            var s = $(this).val();
            if (!s.length) {
                $(_this.cmra).find('.cmra-filter li').removeClass('active');
                $(_this.cmra).find('.cmra-category').addClass('cmra-hidden').hide();
                $(_this.cmra).find('.cmra-category[data-role="category"]').removeClass('cmra-hidden').show();
                $(_this.cmra).find('.cmra-category[data-role="category"] .cmra-category-link-list-entry').show();
                _this.updateJSPlaceholders();
                _this.masonry();
                return;
            }
            if (s.match(/^category:/)) {
                _this.filter({category: s.slice(9)});
            } else if (s.match(/^tag:/)) {
                _this.filter({tag: s.slice(4)});
            } else {
                _this.filter({search: s});
            }
        });
    };

    cmra.prototype.tagInit = function () {
        var _this = this;
        $(_this.cmra).find('.cmra-tag').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(_this.cmra).find('.cmra-search-input')
                    .val('tag: {name}'.replace('{name}', $(this).text()))
                    .trigger('input')
                    .trigger('change');
            return false;
        });
    };

    cmra.prototype.visibleCategoriesInit = function() {
        var _this = this,
        storage_key = 'cmra_hidden_categories',
        storage = null,
        id;

        try {
            storage = JSON.parse(localStorage.getItem(storage_key));
        } catch (e) {
        }

        if( !Array.isArray( storage ) ) {
            storage = [];
            localStorage.setItem(storage_key, JSON.stringify(storage));
        }

        for( var i in storage ) {
            id = storage[i];
            $(_this.cmra).find('.cmra-filter li[data-id="' + id + '"]').addClass('disabled-filter')
                .find('input[type="checkbox"]').prop( 'checked', false );
            $(_this.cmra).find('.cmra-category[data-id="' + id + '"][data-role="category"]')
                    .addClass('cmra-hidden-category').find('.cmra-category').addClass('cmra-hidden-category');
        }
        $(_this.cmra).find('.cmra-filter li.cat-item-all input[type="checkbox"]')
            .prop('checked', $(_this.cmra).find('.cmra-filter li:not(.cat-item-all) input[type="checkbox"]:checked').length ==
            $(_this.cmra).find('.cmra-filter li:not(.cat-item-all) input[type="checkbox"]').length );
        _this.masonry();

        $(_this.cmra).find('.cmra-filter li:not(.cat-item-all) input[type="checkbox"]').on('click changed-value', function(e) {
            e.stopPropagation();
            var $li = $(this).parent(),
                id = $li.data('id');
            storage = JSON.parse(localStorage.getItem(storage_key));

            if ( storage.indexOf(id) > -1 && $(this).is(':checked')) {
                storage.splice(storage.indexOf(id), 1);
            }
            if (storage.indexOf(id) === -1 && !$(this).is(':checked')) {
                storage.push(id);
            }
            localStorage.setItem(storage_key, JSON.stringify(storage));

            if( $(this).is(':checked') ) {
                $li.removeClass('disabled-filter');
                $(_this.cmra).find('.cmra-category[data-id="' + id + '"][data-role="category"]')
                    .removeClass('cmra-hidden-category').find('.cmra-category').removeClass('cmra-hidden-category');
            } else {
                $li.addClass('disabled-filter');
                $(_this.cmra).find('.cmra-category[data-id="' + id + '"][data-role="category"]')
                    .addClass('cmra-hidden-category').find('.cmra-category').addClass('cmra-hidden-category');
            }
            _this.masonry();

            $(_this.cmra).find('.cmra-filter li.cat-item-all input[type="checkbox"]')
                .prop('checked', $(_this.cmra).find('.cmra-filter li:not(.cat-item-all) input[type="checkbox"]:checked').length ==
                $(_this.cmra).find('.cmra-filter li:not(.cat-item-all) input[type="checkbox"]').length );
        });

        $(_this.cmra).find('.cmra-filter li.cat-item-all input[type="checkbox"]').on('click', function(e) {
            e.stopPropagation();
            var is_checked = $(this).is(':checked');
            $(_this.cmra).find('.cmra-filter li:not(.cat-item-all) input[type="checkbox"]').each(function() {
                if( is_checked ) {
                    $(this).prop('checked', true).triggerHandler('changed-value');
                } else {
                    $(this).prop('checked', false).triggerHandler('changed-value');
                }
            });
        });
    };

    cmra.prototype.checkboxInit = function () {
        var _this = this;
        var lsKey = 'wp-cmra-selected-links';
        var data = null;
        try {
            data = JSON.parse(localStorage.getItem(lsKey));
        } catch (e) {
        }
        if (data == null || data.data == null) {
            data = {data: []};
            localStorage.setItem(lsKey, JSON.stringify(data));
        }
        $(data.data).each(function (k, v) {
            $(_this.cmra).find('.cmra-link-checkbox input[data-id="{id}"]'.replace('{id}', v)).attr('checked', 'checked');
        });
        $(_this.cmra).find('.cmra-link-checkbox input').on('change', function () {
            data = JSON.parse(localStorage.getItem(lsKey));
            var id = $(this).data('id');
            var $cbx = $('.cmra-link-checkbox input[data-id="{id}"]'.replace('{id}', id));
            $(this).is(':checked') ? $cbx.attr('checked', 'checked') : $cbx.removeAttr('checked');
            if (data.data.indexOf(id) > -1 && !$(this).is(':checked')) {
                data.data.splice(data.data.indexOf(id), 1);
            }
            if (data.data.indexOf(id) === -1 && $(this).is(':checked')) {
                data.data.push(id);
            }
            localStorage.setItem(lsKey, JSON.stringify(data));
        });
    }

    $(function () {

        Opentip.styles.cmra = {
            className: 'cmra-tooltip'
        };
        if (options.tooltipBackgroundColor) {
            Opentip.styles.cmra.background = options.tooltipBackgroundColor;
        }
        if (options.tooltipBorderColor) {
            Opentip.styles.cmra.borderColor = options.tooltipBorderColor;
        }

        $('.cmra').each(function () {
            var _this = this;

            // remove empty categories
            $(_this).find('.cmra-category').each(function () {
                if (!$(this).find('a').length) {
                    $(_this).find('.cmra-filter li[data-id="{id}"]'.replace('{id}', $(this).data('id'))).remove();
                    $(this).remove();
                }
            });

            // run plugin
            new cmra({
                cmra: _this,
                options: options,
                masonryOptions: masonryOptions
            });
        });

        //tooltips
        $('.cmra-link, .cmra-filter li').each(function () {
            var title = $(this).attr('title');
            if (title) {
                $(this).opentip(title, {style: 'cmra'});
                $(this).removeAttr('title');
            }
        });

        // block empty links
        $('a.cmra-link[href=""]').addClass('cmra-link-disabled');
        $('a.cmra-link[href=""]').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        });

        // search input "x"
        $(document).on('input', '.cmra-clearable', function () {
            $(this)[tog(this.value)]('x');
        }).on('mousemove', '.x', function (e) {
            $(this)[tog(this.offsetWidth - 18 < e.clientX - this.getBoundingClientRect().left)]('onX');
        }).on('touchstart click', '.onX', function (ev) {
            ev.preventDefault();
            $(this).removeClass('x onX').val('').change();
        });
        //$('.cmra-clearable').trigger('input');

        function tog(v) {
            return v ? 'addClass' : 'removeClass';
        }

    });

    //https://davidwalsh.name/javascript-debounce-function
    function debounce(func, wait, immediate) {
        var timeout;
        return function () {
            var context = this, args = arguments;
            var later = function () {
                timeout = null;
                if (!immediate)
                    func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow)
                func.apply(context, args);
        };
    }

})(jQuery);
