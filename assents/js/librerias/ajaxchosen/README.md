# Ajax-Chosen

This project is an addition to the excellent [Chosen jQuery plugin](https://github.com/harvesthq/chosen) that makes HTML input forms more friendly.  Chosen adds search boxes to `select` HTML elements. This extends those search boxes to work with ajax requests. 

This script bootstraps the existing Chosen plugin without making any modifications to the original code. 

## How to Use

This plugin exposes a new jQuery function named `ajaxChosen` that we call on a `select` element. 

The first argument is an option parameter for the plugin specific parameters

ajaxChosen specific parameters are: 

- minLength: this is the number of characters that need to be typed before search occurs. Default is 3.
- queryLimit: this is the max number of items that your server will ever return, and it is used for client side caching. Default is 10. 
- delay: time to wait between key strokes
- chosenOptions: passed to vanilla chosen
- searchingText: "Searching..."
- noresultsText: "No results."
- initialQuery: boolean of wether or not to fire a query when the chosen is first focused


The second argument is a callback that the plugin uses to get option elements. The callback is passed the set options, the response callback, and the event that triggered the callback (focus or keyup). The callback is expected to make ajax call, it receives as first argument the options of plugin and must call its second argument to pass the values back to the plugin, e.g. if it were a list of states it would be
	
	states[state.id] = state.name;

which would become

	<option value="state.id">state.name</option>

You can use the event parameter to differentiate between initialQuery and subsequent queries to change behavior accordingly

## Example Code

``` js
$("#example-input").ajaxChosen({
	minLength: 3,
	queryLimit: 10,
	delay: 100,
	chosenOptions: {},
	searchingText: "Searching...",
	noresultsText: "No results.",
	initialQuery: false
}, function (options, response, event) {
    $.getJSON("/ajax-chosen/data.php", {q: options.term}, function (data) {

	    var terms = {};
			
	    $.each(data, function (i, val) {
		    terms[i] = val;
	    });
			
	    response(terms);
    });
});
```