jQuery.each(param_obj, function (index, value) {
    if (!isNaN(value)) {
        param_obj[index] = parseInt(value);
    }
});

function Portfolio_Gallery_Toggle_Up_Down(id) {
    var _this = this;
    _this.container = jQuery('#' + id + '.view-toggle-up-down');
    _this.hasLoading = _this.container.data("show-loading") == "on";
    _this.optionsBlock = _this.container.parent().find('div[id^="huge_it_portfolio_options_"]');
    _this.filtersBlock = _this.container.parent().find('div[id^="huge_it_portfolio_filters_"]');
    _this.content = _this.container.parent();
    _this.element = _this.container.find('.portelement');
    _this.defaultBlockHeight = param_obj.ht_view0_block_height;
    _this.defaultBlockWidth = param_obj.ht_view0_block_width;
    _this.optionSets = _this.optionsBlock.find('.option-set');
    _this.optionLinks = _this.optionSets.find('a');
    _this.sortBy = _this.optionsBlock.find('#sort-by');
    _this.dropdownable = _this.element.find('div.dropdownable');
    _this.titleBlock = _this.element.find('.title-block');
    _this.filterButton = _this.filtersBlock.find('ul li');
    _this.imageBehaiour = _this.content.data('image-behaviour');
    if (_this.container.data('show-center') == 'on' && ( ( !_this.content.hasClass('sortingActive') && !_this.content.hasClass('filteringActive') )
        || ( _this.optionsBlock.data('sorting-position') == 'top' && _this.filtersBlock.data('filtering-position') == 'top' ) ||
        ( _this.optionsBlock.data('sorting-position') == 'top' && !_this.content.hasClass('filteringActive') ) || ( !_this.content.hasClass('sortingActive') && _this.filtersBlock.data('filtering-position') == 'top' ) )) {
        _this.isCentered = _this.container.data("show-center");
    }
    _this.documentReady = function () {
        _this.container.hugeitmicro({
            itemSelector: _this.element,
            masonry: {
                columnWidth: _this.defaultBlockWidth + 20 + param_obj.ht_view0_element_border_width * 2,
            },
            masonryHorizontal: {
                rowHeight: 300 + 20
            },
            cellsByRow: {
                columnWidth: 300 + 20,
                rowHeight: 240
            },
            cellsByColumn: {
                columnWidth: 300 + 20,
                rowHeight: 240
            },
            getSortData: {
                symbol: function ($elem) {
                    return $elem.attr('data-symbol');
                },
                category: function ($elem) {
                    return $elem.attr('data-category');
                },
                number: function ($elem) {
                    return parseInt($elem.find('.number').text(), 10);
                },
                weight: function ($elem) {
                    return parseFloat($elem.find('.weight').text().replace(/[\(\)]/g, ''));
                },
                id: function ($elem) {
                    return $elem.find('.id').text();
                }
            }
        });
    };

    _this.manageLoading = function () {
        if (_this.hasLoading) {
            _this.container.css({'opacity': 1});
            _this.optionsBlock.css({'opacity': 1});
            _this.filtersBlock.css({'opacity': 1});
            _this.content.find('div[id^="huge-it-container-loading-overlay_"]').css('display', 'none');
        }
    };

    _this.showCenter = function () {
        if (_this.isCentered) {
            var count = _this.element.length;
            var elementwidth = _this.defaultBlockWidth + 10 + param_obj.ht_view0_element_border_width * 2;
            var enterycontent = _this.content.width();
            var whole = ~~(enterycontent / (elementwidth));
            if (whole > count) whole = count;
            if (whole == 0) {
                return false;
            }
            else {
                var sectionwidth = whole * elementwidth + (whole - 1) * 20;
            }
            _this.container.width(sectionwidth).css({
                "margin": "0px auto",
                "overflow": "hidden"
            });
            setInterval(function(){
                _this.container.hugeitmicro('reLayout');
            });
            console.log(elementwidth + " " + enterycontent + " " + whole + " " + sectionwidth);
        }
    };


    _this.addEventListeners = function () {
        _this.optionLinks.on('click', _this.optionsClick);
        _this.optionsBlock.find('#shuffle a').on('click',_this.randomClick);
        _this.titleBlock.on('click', _this.dropdownableClick);
        _this.dropdownable.on('click', _this.dropdownableClick);
        _this.filterButton.on('click', _this.filtersClick);
        jQuery(window).resize(_this.resizeEvent);


    };
    _this.resizeEvent = function(){
        _this.showCenter();
    };
    _this.optionsClick = function () {
        var $this = jQuery(this);

        if ($this.hasClass('selected')) {
            return false;
        }
        var $optionSet = $this.parents('.option-set');
        $optionSet.find('.selected').removeClass('selected');
        $this.addClass('selected');


        var options = {},
            key = $optionSet.attr('data-option-key'),
            value = $this.attr('data-option-value');

        value = value === 'false' ? false : value;
        options[key] = value;
        if (key === 'layoutMode' && typeof changeLayoutMode === 'function') {

            changeLayoutMode($this, options)
        } else {

            _this.container.hugeitmicro(options);
        }

        return false;
    };
    _this.randomClick = function () {
        _this.container.hugeitmicro('shuffle');
        _this.sortBy.find('.selected').removeClass('selected');
        _this.sortBy.find('[data-option-value="random"]').addClass('selected');
        return false;
    };
    _this.dropdownableClick = function () {
        var strheight = 0;
        jQuery(this).parents('.portelement').find('.wd-portfolio-panel > div').each(function () {
            strheight += parseInt(jQuery(this).outerHeight()) + 10;
        });
        strheight += _this.defaultBlockHeight + 20 + 2 * param_obj.ht_view0_element_border_width + param_obj.ht_view0_title_font_size;
        if (jQuery(this).parents('.portelement').hasClass("large")) {
            jQuery(this).parents('.portelement').animate({
                height: _this.defaultBlockHeight + 20 + param_obj.ht_view0_title_font_size
            }, 300, function () {
                jQuery(this).removeClass('large');
                _this.container.hugeitmicro('reLayout');
            });
            _this.element.removeClass("active");
            return false;
        }
        jQuery(this).parents('.portelement').css({height: strheight});
        jQuery(this).parents('.portelement').addClass('large');

        _this.container.hugeitmicro('reLayout');
        jQuery(this).parents('.portelement').css({height: _this.defaultBlockHeight + 45 + 2 * param_obj.ht_view0_element_border_width + param_obj.ht_view0_title_font_size + "px"});

        jQuery(this).parents('.portelement').animate({
            height: strheight + "px"
        }, 300, function () {
            _this.container.hugeitmicro('reLayout');
        });
        return false;
    };
    _this.filtersClick = function () {
        _this.filterButton.each(function () {
            jQuery(this).removeClass('active');
        });
        jQuery(this).addClass('active');
        // get filter value from option value
        var filterValue = jQuery(this).attr('rel');
        // use filterFn if matches value
        _this.container.hugeitmicro({filter: filterValue});
    };

    _this.imagesBehavior = function(){
        _this.container.find('.portelement .image-block img').each(function( i, img ){
            var naturalRatio = jQuery(this).prop('naturalWidth')/jQuery(this).prop('naturalHeight');
            var defaultRatio = _this.defaultBlockWidth/_this.defaultBlockHeight;console.log(defaultRatio+'aaa'+naturalRatio);
            if(naturalRatio<=defaultRatio){console.log(0);
                jQuery(img).css({
                    position: "absolute",
                    width: '100%',
                    top: '50%',
                    transform: 'translateY(-50%)'
                });
            }else {console.log(1);
                jQuery(img).css({
                    position: "absolute",
                    height:'100%',
                    left: '50%',
                    transform: 'translateX(-50%)'
                });
            }
        });
    };

    _this.init = function () {
        _this.showCenter();
        jQuery(window).load(_this.manageLoading);
        _this.documentReady();
        _this.addEventListeners();
        if( _this.imageBehaiour == 'crop'){
            _this.imagesBehavior();
        }
    };

    this.init();
}
var portfolios = [];
jQuery(document).ready(function () {
    jQuery(".huge_it_portfolio_container.view-toggle-up-down").each(function (i) {
        var id = jQuery(this).attr('id');
        portfolios[i] = new Portfolio_Gallery_Toggle_Up_Down(id);
    });
});