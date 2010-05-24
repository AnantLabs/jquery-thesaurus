Thesaurus
===================

Version 3.0
---------------------------

Copyright (C) 2006-2010 Dmitry Sheiko

Requirements:
	PHP version 5.x+ (http://www.php.net)
	Works with UNIX and Windows both


This toolkit is the simplest way to create own in-text advertising system
using IntelliTXT (IntelliImages/IntelliMedia) technology approach.

You need only to include on your site pages these java scripts and fill sample.csv
with you terms and their descriptions (ads). Now when a user of your site hovers mouse
cursor on a term from your list (sample.csv), he or she will get popup window (tooltip)
with related description. Notice this the script asks server for term description only
when user hovers mouse on the term.

You can use this library to create portal news popup description, interactive reference
book and son on as well.

FEATURES

* Retrieving data though Cross-Domain Request. That allows you to have the same controller for
instaces of Thesaurus on different domains.
* Instancing of tooltips. When the term occures within the tooltip text it will cause
another tooltip when hovering by mouse.
* Effects. Different transition effects for tooltip apearace are available.
* Auto-enable tooltips accoring to the term list received from the server
* Sample package includes 2 data access adapters (CVS and MySQL DB)
* Keeps stats of tooltip visiting and clicking

HOW TO INSTALL

Place all files into a folder (for instance, "/thesaurus/")
Then add to HTML of your page thesaurus component:

<script src = "jquery.thesaurus.js" ></script>

It is supposed jQuery is loaded as well.
So that enough to start. Though if you want configure Thesaurus, please add:

<script type="text/javascript">
    <!--
    $.Thesaurus({
        caseSentitive: true, // Used when matching found terms againstloaded ones
        delay: 250, // Delay before tooltip self-destruction
        containers: ['div.message-box div.body'], // Put here list of selectors for the DOM element
                                                  // you want to analyze for terms
        effect: null, // Can be also fade or slide,
        controller: 'controller.csv.php' // Path to the controller
    });
     // -->
</script>


CONTROLLER

The package includes a simple PHP library containing two data adapters: CSV (by default)
and MySQL DB. Please find examples of use in controller.csv.php and controller.db.php respectively.

In the case of MySQL DB adapter you'll get tooltip visits and clicks statistics reflected into DB.
You can easily write another adapter, which will implement Thesaurus_Adapter_Interface

REFERENCES
Source code is available to download at http://code.google.com/p/jquery-thesaurus/downloads/list
Project page and SVN http://code.google.com/p/jquery-thesaurus/
Live demo http://demo.dsheiko.com/thesaurus/

Yours sincerely,
Dmitry Sheiko,
http://dsheiko.com
