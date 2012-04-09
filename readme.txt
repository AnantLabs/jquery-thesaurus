Thesaurus
===================

Version 4.0
---------------------------

Copyright (C) 2006-2012 Dmitry Sheiko

Requirements:
	PHP version 5.x+ (http://www.php.net)
	Works with UNIX and Windows both


You can apply the plugin for content areas on your site pages and it will highlight all the terms 
from the dictionary (by default: sample.csv). When hovering a term, visitors of your site will get 
definition of the term on a tooltip. Content of the appeared tooltip is being processed as well. 
So if any terms are encountered , they get definition-tooltips attached. If you switch to DB 
data-source, you will have visits and click statistics collected.

You can use this plugin to create intelligent advertisement, tooltip to show portal news 
description, interactive reference book and so on and so forth. 

Usage and API

Step 1
Unpack the package into a folder on your server. Include the plugin script after 
jQuery (requires jQuery 1.4+):

<script src="./js/jquery.thesaurus.js" type="text/javascript"></script>


Step 2

Now let's apply the plugin on a set of elements when document is ready. Here any article 
elements on the page will be parsed for thesaurus terms:

<script type="text/javascript"> 
<!-- 
$(document).ready(
    function(){ 
        $('article').Thesaurus({ 
            effect: 'fade', 
            caseSensitive: false 
        }); 
}); 
// --> 
</script>

Available Options

caseSensitive
    defines if the plugin does case sensitive search for the terms
effect
    transition effect for tooltip: fade or any manual 
pushStats
    states if the plugins collects tooltip click and view statistics into queue and uploads the 
queue to the server every 5 sec (if it is not empty).

Back-end configuration

In ./app/Config/config.php you will find a CSV file is set up as data source. If you want to 
use DB, just uncomments another configuration section.

Manual transition effects making

You can make your own effect for Thesaurus using CSS3. Just describe the initial state of 
tooltip overlay and final state. Name those CSS classes by the pattern thesaurus-EFFECTNAME-start 
and thesaurus-EFFECTNAME-end:

.thesaurus-scale-start {
    opacity: 0; 
    -moz-transform: scale(0.1);
}    
.thesaurus-scale-end {
    -webkit-transition: all 1s ease-in-out;
        -moz-transition: all 1s ease-in-out;
        -o-transition: all 1s ease-in-out;
            transition: all 1s ease-in-out; 
    opacity: 1 !important;
    -moz-transform: scale(1);
}

Now you can just specify the effect (EFFECTNAME) in the plugin options.

REFERENCES
Source code is available to download at http://code.google.com/p/jquery-thesaurus/downloads/list
Project page and SVN http://code.google.com/p/jquery-thesaurus/
Live demo http://demo.dsheiko.com/thesaurus/

Yours sincerely,
Dmitry Sheiko,
http://dsheiko.com

