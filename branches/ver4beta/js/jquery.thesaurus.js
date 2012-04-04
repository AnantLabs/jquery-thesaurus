/*
* Thesaurus
*
* @package thesaurus
* @author $Author: sheiko $
* @version $Id: jquery.thesaurus.js, v 3.0 $
* @license GNU
* @copyright (c) Dmitry Sheiko http://www.cmsdevelopment.com
*/

(function( $ ) {
    var VERSION = "4.0b",
        TPL_TAG_OPEN = '~~',
        TPL_TAG_CLOSE = '~~',
        ESCAPERS = '[\\s!;,%\"\'\\(\\)\\{\\}]',
        SERVER_LOC = 'server.php',
    
        Thesaurus = function(settings) {
        var _settings = settings,
            _terms = [],
            
            /**
            * Since I can't apply any HTML working with textNodes, just mark them to be able then
            * parse them in document HTML
            
            * @param string line
            * @param string term
            * @param int id
            * @see _markup
            */
            _highlightTermInTextNodeText = function(line, term) {
                var modifier = _settings.caseSensitive=="on"?"g":"gi";
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
            * Traverses configured nodes for all their children textNodes
            * @param HTMLNode node
            */
            _markTermsInDOM = function(nodes) {               
               nodes.contents().filter(function() {
                    // If it is an element, look for text nodes inside recursively
                    if (this.nodeType === 1) {
                        _markTermsInDOM($(this));
                    }
                    // Only not empty text nodes
                    return this.nodeType === 3 && $.trim($(this).text()).length;
               })
               .each(function(){
                   var node = this;
                   $.each(_terms, function(id, term){
                        node.textContent = _highlightTermInTextNodeText(node.textContent, term);                        
                   })                   
               });                
            },
            _wrapTermsInDOM = function(nodes) {
                nodes.find('script').detach();
                nodes.html(function(inx, html){
                    var id = 1, re = new RegExp(TPL_TAG_OPEN + "(.*?)" + TPL_TAG_OPEN, 'g');                
                    return html.replace(re, '<dfn class=\"thesaurus\" data-id=\"' + id + '\">$1</dfn>'); 
               });
            };
        
        return {
            init: function() {
               this.loadTerms(function() {
                   // Method is invoked as soon as the terms are loaded
                   _markTermsInDOM(settings.nodes);
                   _wrapTermsInDOM(settings.nodes);
                   
               });
               return this; 
            },
            // Load terms map {id : term} from the data source 
            loadTerms: function(callback) {
                var scope = this;
                $.getScript(SERVER_LOC, function(){
                    _terms = $.callbackData.payload;
                    callback.call(scope);
                });
            }
        
    }};
    
    $.fn.Thesaurus = function(settings) {
        settings.nodes = $(this);
        var instace = new Thesaurus(settings);
        return instace.init();
    };

}( jQuery ));