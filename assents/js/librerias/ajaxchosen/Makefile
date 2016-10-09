COFFEESCRIPT = coffee --lint
GOOGCLOSURE  = google-closure

all: lib/ajax-chosen.js lib/ajax-chosen.min.js

lib/%.js: src/%.coffee
	$(COFFEESCRIPT) -o lib/ -c src/ 

lib/%.min.js: lib/%.js
	$(GOOGCLOSURE) --js $<  --js_output_file $@

clean:
	@rm -f lib/*.js

