# Initialization #

When the page is requested and generated (DOM is ready), Thesaurus requests the server for term list

```
/thesaurus/server.php?action=termList
```

That's JSONP request, which expects following sort of response:
```
 $.callbackData = {
        status : 'ok',
        errorMsg : '',
        payload : ['term', 'term',...]
 }
```

payload is just an array of all terms

# Term definition retrieval #

After that Thesaurus parses configured nodes (or body by default) against the received list of terms and modifies HTML of matched instances (terms) in the text, so make the script know about.

When any of matched terms now hovered, event fired and listener function sends new request like
```
/thesaurus/server.php?action=termDef&term=Synchronization&caseSensitive=0
```

and expects response like
```
 $.callbackData = {
        status : 'ok',
        errorMsg : '',
        payload : 'Definition for the term...'
 }
```
The obtained definition gets displayed.