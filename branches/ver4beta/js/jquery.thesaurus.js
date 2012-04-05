/*
* Thesaurus
*
* @package thesaurus
* @author sheiko 
* @version jquery.thesaurus.js, v 4.0 
* @license GNU
* @copyright (c) Dmitry Sheiko http://dsheiko.com
*/

(function( $ ) {
    var VERSION = "4.0b",
        TPL_TAG_OPEN = '~~',
        TPL_TAG_CLOSE = '~~',
        ESCAPERS = '[\\s!;,%\"\'\\(\\)\\{\\}]',
        SERVER_LOC = 'server.php',
        UNAPPROPRIATE_TAGS = ['SCRIPT', 'BASE', 'LINK', 'META', 'STYLE', 'TITLE', 'APPLET', 'OBJECT'],
        CSS_TPL =
        'div.thesaurus { font-size: 12px; font-family: Arial; position: absolute; width: 300px; z-index: auto; box-shadow: 0 0 5px #000000; -moz-box-shadow: 0 0 5px #000000; -webkit-box-shadow: 0 0 5px #000000; -o-box-shadow: 0 0 5px #000000; -ie-box-shadow: 0 0 5px #000000; border-radius: 5px; opacity: 0; }' + 
        'div.thesaurus .thesaurus-header { padding: 5px;  background-color: #3C5F87; border-radius: 5px 5px 0 0; -moz-border-radius: 5px 5px 0 0; -webkit-border-radius: 5px 5px 0 0;  -o-border-radius: 5px 5px 0 0;  -ie-border-radius: 5px 5px 0 0; }' +
        'div.thesaurus .thesaurus-header a { color: white; font-weight: bold; }' +
        'div.thesaurus .thesaurus-header a.reference { position: absolute; right: 5px; z-index: auto; display: block; }' +
        'div.thesaurus .thesaurus-body { padding: 5px;  border: 1px solid #3C5F87; background-color: #fff; border-radius: 0 0 5px 5px; -moz-border-radius: 0 0 5px 5px; -webkit-border-radius: 0 0 5px 5px; -o-border-radius: 0 0 5px 5px; -ie-border-radius: 0 0 5px 5px; }' +
        'dfn.thesaurus { text-decoration: none; font-style: inherit; border-bottom: 1px dashed black; cursor: pointer; }',
        TOOLTIP_TPL = '<div class="thesaurus"><div class="thesaurus-header"><a class="reference" target="_blank" href="http://dsheiko.com/freeware/">Thesaurus v.' + VERSION +'</a><a class="term"></a></div><div class="thesaurus-body">Loading...</div></div>',
        TOOLTIP_HIDE_TIMEOUT = 100,
        Repository = {
            terms: {} // Cache of term list 
        },
        Tooltip = function(settings, parent) {
            var _settings = settings,
                _parent = parent,
                _boundingBox = null,
                _hideTimer = null,
                _termCache = {},
                /**
                 * Encode strings with spaces correctly
                 * @param string text
                 */
                _urlEncode = function(text) {
                    return encodeURIComponent(text.replace(/ /g, "+"));
                },
                /**
                 * Adjusts position (top/left) of the tooltip overlay relatively to term element
                 */
                _adjustPositionByTarget = function(targetEl) {
                    var top = targetEl.offset().top - 5 - _boundingBox.height(),
                        left = targetEl.offset().left + targetEl.width() / 2;

                    top = top  < 0 ? targetEl.offset().top + targetEl.height()
                        + 5 : top;
                    left = left > $(window).width() - _boundingBox.width() ?
                        targetEl.offset().left - _boundingBox.width() + targetEl.width() / 2 : left;

                    _boundingBox
                            .css("top", Math.floor(top))
                            .css("left", Math.floor(left))
                },
                /**
                 * Fetches definitiion of the provided term by XMLHttpRequest or from cache
                 */
                _fetchDefinition = function(term, callback){
                    if (typeof _termCache[term] !== "undefined") {
                        callback(_termCache[term]);
                        return;
                    }
                    $.getScript(SERVER_LOC + _urlEncode("?action=termDef&term=" + term 
                        + "&caseSensitive=" + (_settings.caseSensitive ? 1 : 0)), function() {
                        _termCache[term] = $.callbackData.payload;
                        callback(_termCache[term]);
                    });
                };
            return {
                init: function() {
                    this.syncUI();
                },
                /**
                 * Subscribes handlers on hover events on the terms elements in DOM
                 */
                syncUI : function() {
                    settings.nodes.find('dfn.thesaurus').unbind().bind("mouseenter", this, function(e){
                        e.data.show($(this));
                    }).bind('mouseleave', this, function(e){
                        e.data.hide();
                    });

                },
                /** 
                 * Renders tooltip overlay
                 * @param jQuery Node
                 **/
                show: function(targetEl) {
                    var term = targetEl.text(), scope = this;
                    // Happens when mouse cursor moves from overlay to the link
                    if ($(_boundingBox).hasClass('thesaurus-visible')) {
                        this.cancelHiding();
                        return;
                    }
                    $(_boundingBox).remove();
                    // Renders tooltip with Loading...
                    _boundingBox = $(TOOLTIP_TPL).appendTo('body');
                    _boundingBox.find('a.term').text(term);

                    _boundingBox.addClass('thesaurus-visible').unbind().bind("mouseenter", this, function(e){
                        e.data.cancelHiding();
                    }).bind('mouseleave', this, function(e){
                        e.data.hide();
                    }).bind('click', this, function(e){
                        $.getScript(SERVER_LOC + _urlEncode("?action=onclick&term=" + term));
                    })
                    _adjustPositionByTarget(targetEl);
                    // Fetches and appends definition text into the tooltip
                    _fetchDefinition(term, function(def){
                        _boundingBox.find('div.thesaurus-body').html(def).Thesaurus(_settings, scope);
                        _adjustPositionByTarget(targetEl);
                    });
                    _boundingBox.animate({opacity: 0.9});                    
                },
                /**
                 * Cancel destroying
                 */
                cancelHiding: function() {
                    window.clearTimeout(_hideTimer);
                    if (typeof _parent !== 'undefined') {
                        _parent.cancelHiding();
                    }
                },
                /**
                 * Destroys tooltip overlay defferedly
                 */
                hide: function() {
                    if (typeof _parent !== 'undefined') {
                        _parent.hide();
                    }
                    window.clearTimeout(_hideTimer);
                    _hideTimer = window.setTimeout(function(){
                        _boundingBox.fadeOut('fast', function(){
                            $(this).removeClass('thesaurus-visible').remove();
                        });
                    }, TOOLTIP_HIDE_TIMEOUT);
                }
            }
        },
        Thesaurus = function(settings, parent) {
        var _settings = $.extend({
                caseSensitive: false,
                effect: null
            }, settings),
            _tooltip = new Tooltip(settings, parent),
            /**
            * Since I know no way to insert an ElementNode into a TextNode, here the found term
            * is marked with special text tags, to be found and replaced aftewards within DOM
            *
            * @param string line
            * @param string term
            */
            _markTermInTextNodeText = function(line, term) {
                var modifier = _settings.caseSensitive ? "g" : "gi";
                // Only term in nodeValue
                if(term == line) {
                    return TPL_TAG_OPEN + line + TPL_TAG_CLOSE;
                }
                //term" ....
                var re = new RegExp("^("+term+")(" + ESCAPERS + ")", modifier);
                line = line.replace(re, TPL_TAG_OPEN + "$1" + TPL_TAG_CLOSE + "$2");
                //... "term
                re = new RegExp("(" + ESCAPERS + ")("+term+")$", modifier);
                line = line.replace(re, "$1" + TPL_TAG_OPEN + "$2" + TPL_TAG_CLOSE);
                // .. "term" ..
                re = new RegExp("(" + ESCAPERS + ")("+term+")(" + ESCAPERS + ")", modifier);
                line = line.replace(re, "$1" + TPL_TAG_OPEN +"$2" + TPL_TAG_CLOSE + "$3");
                return line;
            },
            /**
            * Mark terms in TextNodes of the given parent nodes
            * @param jQuery nodes
            */
            _markTermsInDOM = function(nodes) {
               nodes.contents().filter(function() {
                    // If it is an element, look for text nodes inside recursively
                    if (this.nodeType === 1) {
                        _markTermsInDOM($(this));
                    }
                    // Only not empty text nodes
                    return this.nodeType === 3 && $.trim($(this).text()).length
                        && $.inArray(this.tagName, UNAPPROPRIATE_TAGS) === -1;
               })
               .each(function(){
                   var node = this;
                   $.each(Repository.terms, function(id, term){
                       node.nodeValue = _markTermInTextNodeText(node.nodeValue, term);
                   })
               });
            },
            /**
            * Turn found terms into elements responsible to hover event
            * @param jQuery nodes
            */
            _wrapTermsInDOM = function(nodes) {
                nodes.find('script').detach();
                nodes.html(function(inx, html){
                    var re = new RegExp(TPL_TAG_OPEN + "(.*?)" + TPL_TAG_OPEN, 'g');
                    return html.replace(re, '<dfn class=\"thesaurus\">$1</dfn>');
               });
            };

        return {
            init: function() {
               this.renderUI();
               this.loadTerms(function() {
                   // Method is invoked as soon as the terms are loaded
                   _markTermsInDOM(settings.nodes);
                   _wrapTermsInDOM(settings.nodes);
                   _tooltip.init();
               });
               return this;
            },
            /**
             * Loads terms map {id : term} from the data source
             * @param function callback
             */
            loadTerms: function(callback) {
                var scope = this;
                // Term list is already cached
                if (Repository.terms.length) {
                    callback.call(scope);
                    return;
                }
                $.getScript(SERVER_LOC + "?action=termList", function(){
                    Repository.terms = $.callbackData.payload;
                    callback.call(scope);
                });
            },
            /**
             * Adding Thesaurus stylesheet into DOM
             */
            renderUI : function() {
                // Append CSS
                $('body').append('<style type="text/css">' + CSS_TPL + '</style>');
            }

    }};
    /**
     * @param object settings
     * @param Thesaurus parent - required only when Thesaurus instatiated to parse tooltip's content
     */
    $.fn.Thesaurus = function(settings, parent) {
        settings.nodes = $(this);
        var instace = new Thesaurus(settings, parent);
        return instace.init();
    };

}( jQuery ));