###
ajax-chosen
A complement to the jQuery library Chosen that adds ajax autocomplete
Contributors:
https://github.com/jobvite/ajax-chosen
https://github.com/bicouy0/ajax-chosen
###

(($) ->
 $.fn.ajaxChosen = (options, callback) ->

    defaultedOptions = {
      minLength: 3,
      queryLimit: 10,
      delay: 100,
      chosenOptions: {},
      searchingText: "Searching...",
      noresultsText: "No results.",
      initialQuery: false
    }

    $.extend(defaultedOptions, options)

    #by design, chosen only has one state for when you 
    #don't have matching items: No Results. 
    #However, we need two states, searching
    #and no results. 
    #
    #TODO: you accidentally lose any user defined no_results_test
    defaultedOptions.chosenOptions.no_results_text = defaultedOptions.searchingText

    # grab a reference to the select box
    select = this

    # determining whether this allows
    # multiple results will affect both the selector
    # and the ways previously selected results are cleared (line 88) 
    multiple = select.attr('multiple')?
    
    #the box where someone types has a different selector 
    #based on the type
    if multiple
      inputSelector = ".search-field > input"
      clickSelector = ".chzn-choices"
    else
      inputSelector = ".chzn-search > input"
      clickSelector = ".chzn-single"

    # initialize chosen
    select.chosen(defaultedOptions.chosenOptions)

    # Now that chosen is loaded normally, we can
    container = select.next('.chzn-container')
    # Grab a reference to the input field
    field = container.find(inputSelector)
    # Attach an initial query when chosen expands
    if defaultedOptions.initialQuery
      field.bind 'focus', (evt)->
        return if @previousSearch || !container.hasClass('chzn-container-active')
        search(evt)

    # a keyup event to the input field.
    field.bind 'keyup', (evt)->
      #we wrap our search in a short Timeout so that if
      #a person is typing we do not get race conditions with
      #multiple searches happening simultaneously
      clearTimeout(@previousSearch) if @previousSearch

      @previousSearch = setTimeout (->
        search(evt)
      ),
      defaultedOptions.delay

    #wrap the search functionality in a function
    #so that it can be put inside a timeout
    search = (evt)=> 

      # Retrieve the current value of the input form
      val = $.trim field.attr('value')

      # Retrieve the previous value of the input form
      prevVal = field.data('prevVal') ? false

      # store the current value in the element
      field.data('prevVal', val)

      #our hack above changes the No Results text to 'Searching...'
      #we should change it back in the case there are no results
      #within a native chosen search
      clearSearchingLabel = =>
          if multiple
            resultsDiv = field.parent().parent().siblings()
          else
            resultsDiv = field.parent().parent()
          #chosen does a fancy regex when matching, so 
          #we use the raw field value (e.g. not trimmed)
          #in case it's terminal spaces preventing the match
          resultsDiv.find('.no-results').html(defaultedOptions.noresultsText + " '" + $(this).attr('value') + "'")


      # Checking minimum search length and dupliplicate value searches
      # to avoid excess ajax calls.
      if val is prevVal or (val.length < defaultedOptions.minLength and evt.type is 'keyup')
        clearSearchingLabel()
        return false;

      #grab the items that are currently in the matching field list
      currentOptions = select.find('option')

      #add the search parameter to the ajax request data
      defaultedOptions.term = val

      # Create our own response callback
      response = (items, success) ->

        #note: sometimes a person will leave the input
        #      before success happens. In this case, jettison the results
        #
        return if not field.is(':focus') and evt.type is 'keyup'

        # use value => text pairs to build <option> tags
        newOptions = []

        $.each items, (value, text) ->
          newOpt = $('<option>')
          newOpt.attr('value', value).html(text)
          newOptions.push $(newOpt)

        #remove any of the current options that aren't in the the 
        #new options block 
        for currentOpt in currentOptions
          do (currentOpt) -> 
            $currentOpt = $(currentOpt)
            return if $currentOpt.attr('selected') and multiple
            return if $currentOpt.attr('value') is '' and $currentOpt.html() is ''  and !multiple #the deselect feature requires an empty entry
            presenceInNewOptions = (newOption for newOption in newOptions when newOption.attr('value') is $currentOpt.attr('value'))
            if presenceInNewOptions.length is 0
              $currentOpt.remove()

        #get the new, trimmed currentOptions
        #so the next loop doesn't do unnecessary loops
        currentOptions = select.find('option')

        # select.append newOption for newOption in newOptions
        for newOpt in newOptions
          do (newOpt) ->
            presenceInCurrentOptions = false
            for currentOption in currentOptions
              do (currentOption) -> 
                if $(currentOption).attr('value') is newOpt.attr('value')
                  presenceInCurrentOptions = true
            if !presenceInCurrentOptions
              select.append newOpt

        #even with setting call backs, we may
        #get race conditions on a search
        #this is to fix that
        latestVal = field.val()

        #this may seem to come late, but... 
        #if we actually have found nothing on the server, 
        #we display a custom no results tag
        #if there are no results on the server
        #add a no results tag. 
        if $.isEmptyObject(items)
          noResult = $('<option>')
          noResult.addClass('no-results')
          noResult.html(defaultedOptions.noresultsText + " '" + latestVal + "'").attr('value', '')
          select.append(noResult)
        else
          select.change()


        # Tell chosen that the contents of the <select> input have been updated
        # This makes chosen update its internal list of the input data.
        select.trigger "liszt:updated"

        #our hack no-result classes will have too many
        #classes associated with them, so those must be removed
        $('.no-results').removeClass('active-result')

        # Chosen contents of the input field get removed once you
        # call trigger above so we add the value the user was typing back into
        # the input field.
        #
        field.val(latestVal)

        if !$.isEmptyObject(items) 
          #to mimic the chosen winnowing behavior, 
          #we highlight the first result with a keydown event
          keydownEvent = $.Event('keydown')
          keydownEvent.which = 40 #the down arrow
          field.trigger(keydownEvent)

        # Finally, call the user supplied callback (if it exists)
        if success
          success(items) 

        #end of response function

      # Execute the callback to get autocomplete data
      # callback must call response with items as first argument
      # and can provide a success callback as second argument
      callback(defaultedOptions, response, evt)

      #end of search function
)(jQuery)
