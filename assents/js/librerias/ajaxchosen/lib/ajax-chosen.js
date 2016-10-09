
/*
ajax-chosen
A complement to the jQuery library Chosen that adds ajax autocomplete
Contributors:
https://github.com/jobvite/ajax-chosen
https://github.com/bicouy0/ajax-chosen
*/

(function() {
//console.log('chosen!');
  (function($) {
    return $.fn.ajaxChosen = function(options, callback) {
       
      var clickSelector, container, defaultedOptions, field, inputSelector, multiple, search, select,
        _this = this;

        defaultedOptions = {
        minLength: 3,
        queryLimit: 10,
        delay: 100,
        chosenOptions: {},
        searchingText: "Searching...",
        noresultsText: "No results.",
        initialQuery: false
      };
      
        $.extend(defaultedOptions, options);
        
        defaultedOptions.chosenOptions.no_results_text = defaultedOptions.searchingText;
        
        select = this;
        
        multiple = select.attr('multiple') != null;
      
        if (multiple) {
          
        inputSelector = ".search-field > input";
        clickSelector = ".chosen-choices";
      } else {
         
        inputSelector = ".chosen-search > input";
        clickSelector = ".chosen-single";
      }
      
      select.chosen(defaultedOptions.chosenOptions);
      
      container = select.next('.chosen-container');
      
      field = container.find(inputSelector);
      
      if (defaultedOptions.initialQuery) {
          
        field.on('focus', function(evt) {
         
          if (this.previousSearch || !container.hasClass('chosen-container-active')) {
            return;
          }
          return search(evt);
        });
      }
      
    field.on('keyup', function(evt) {
    
         if(evt.which!=13)
         {
           
                    if (this.previousSearch) clearTimeout(this.previousSearch);
                      return this.previousSearch = setTimeout((function() {
                      return search(evt);
                    }), defaultedOptions.delay);
             
         }
       
      });
      
      
   select.on('change', function(evt) {
          
        if(select.val()==null){
                    
              if (this.previousSearch) clearTimeout(this.previousSearch);
                            return this.previousSearch = setTimeout((function() {
                            return search(evt);
                    }), defaultedOptions.delay);
         }
              
         
          
        
      });
      
   return search = function(evt) {
        
        var clearSearchingLabel, currentOptions, prevVal, response, val, _ref;
        val =field.val();
        
        prevVal = (_ref = field.data('prevVal')) != null ? _ref : false;
        field.data('prevVal', val);
        clearSearchingLabel = function() {
          var resultsDiv;
          if (multiple) {
            
            resultsDiv = field.parent().parent().siblings();
          } else {
           
            resultsDiv = field.parent().parent();
          }
          return resultsDiv.find('.no-results').html(defaultedOptions.noresultsText);
        };
        if (val === prevVal || (val.length < defaultedOptions.minLength && evt.type === 'keyup')) {
            
          clearSearchingLabel();
          
          return false;
        }
        currentOptions = select.find('option');
        defaultedOptions.term = val;
        response = function(items, success) {
            //console.log('SUCCESS',items);
          var currentOpt, keydownEvent, latestVal, newOpt, newOptions, noResult, _fn, _fn2, _i, _j, _len, _len2;
          if (!field.is(':focus') && evt.type === 'keyup') return;
          newOptions = [];
          $.each(items, function(value, text) {
              //alert('!');
            var newOpt;
            newOpt = $('<option>');
            newOpt.attr('value', value).html(text);
            return newOptions.push($(newOpt));
          });
          _fn = function(currentOpt) {
            var $currentOpt, newOption, presenceInNewOptions;
            $currentOpt = $(currentOpt);
            if ($currentOpt.attr('selected') && multiple) return;
            if ($currentOpt.attr('value') === '' && $currentOpt.html() === '' && !multiple) {
              return;
            }
            presenceInNewOptions = (function() {
              var _j, _len2, _results;
              _results = [];
              for (_j = 0, _len2 = newOptions.length; _j < _len2; _j++) {
                newOption = newOptions[_j];
                if (newOption.val() === $currentOpt.val()) {
                  _results.push(newOption);
                }
              }
              return _results;
            })();
            if (presenceInNewOptions.length === 0) return $currentOpt.remove();
          };
          for (_i = 0, _len = currentOptions.length; _i < _len; _i++) {
            currentOpt = currentOptions[_i];
            _fn(currentOpt);
          }
          currentOptions = select.find('option');
          _fn2 = function(newOpt) {
            var currentOption, presenceInCurrentOptions, _fn3, _k, _len3;
            presenceInCurrentOptions = false;
            _fn3 = function(currentOption) {
              if ($(currentOption).val() === newOpt.val()) {
                return presenceInCurrentOptions = true;
              }
            };
            for (_k = 0, _len3 = currentOptions.length; _k < _len3; _k++) {
              currentOption = currentOptions[_k];
              _fn3(currentOption);
            }
            if (!presenceInCurrentOptions) return select.append(newOpt);
          };
          for (_j = 0, _len2 = newOptions.length; _j < _len2; _j++) {
            newOpt = newOptions[_j];
            _fn2(newOpt);
          }
          latestVal = field.val();
          if ($.isEmptyObject(items)) {
             //alert('no hay resultado');
            //noResult = $('<option>');
            //noResult.addClass('no-results');
           // noResult.html(defaultedOptions.noresultsText + " '" + latestVal + "'").val('');
            
            //$('.no-results').html('no va');
            clearSearchingLabel();
            //select.append(noResult);
          } else {
            
            //select.change();
            select.trigger("chosen:updated");
            
          }
          //select.trigger("chosen:updated");
          $('.no-results').removeClass('active-result');
          field.val(latestVal);
          if (!$.isEmptyObject(items)) {
            keydownEvent = $.Event('keydown');
            keydownEvent.which = 40;
            field.trigger(keydownEvent);
          }
          if (success) return success(items);
        };
        return callback(defaultedOptions, response, evt);
      };
    
    };
  })(jQuery);

}).call(this);
