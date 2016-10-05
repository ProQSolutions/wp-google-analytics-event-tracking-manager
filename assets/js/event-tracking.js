var ga_event_tracking = (function($) {
  return {
    in_view_elements: [],
    is_debugging: false,
    init: function(events_to_track, is_debugging) {

      this.is_debugging = is_debugging;

      if(!this.ga_defined() && this.is_debugging) {
        console.log('ga not found, still binding events...');
      }

      //Bind all events
      var events_to_track = events_to_track.map(this.bind_ga_event, this);

      //Potentiall initialize in-view events triggered by scrolling
      if(this.is_debugging) {
        console.log("Elements to track in view: ", this.in_view_elements);
      }

      this.init_in_view();

      if(this.is_debugging) {
        var success = events_to_track.filter(function(item) {return item === true}).length;
        var failed = events_to_track.filter(function(item) {return item === false}).length;
        var nothing_found = events_to_track.filter(function(item) {return typeof item === "undefined"}).length;

        console.log("Binding " + events_to_track.length + " events.");

        if(success > 0) {
          console.log("Bound " + success + " events.");
        }
        if(failed > 0) {
          console.log("Failed to bind " + failed + " events.");
        }
        if(nothing_found > 0) {
          console.log("No elements found for " + nothing_found + " events.");
        }
      }

    },

    //Binds a callback function to happen when element in dom becomes visible on screen
    bind_in_view: function(selector, callback) {
      var domElements;

      if(typeof callback !== "function") {
        return;
      }

      try{ //Try catch in case selector is invalid
        domElements = $(selector);
      } catch(exception) {return false; }

      if(!domElements) {
        return;
      }

      //Put callback in element
      $(domElements).bind('in_view', callback);

      return domElements;
    },

    //Sets up scroll throttle and checks if items are visible
    init_in_view: function() {

      var self = this;

      //Used for throttling
      var scroll_changed = false;

      //Throttling
      $(document).scroll(function () {
        scroll_changed = true;
      });

      //Potentially check elements in view every half a second
      setInterval(function() {
        if(scroll_changed) {
          scroll_changed = false;

          var docViewTop = $(window).scrollTop();
          var docViewBottom = docViewTop + $(window).height();

          //Using a filter to accomplish two things: loop through all elements and potentially remove elements in which events have been sent
          self.in_view_elements = self.in_view_elements.filter(function(element) {

            var elemTop = $(element).offset().top;
            var elemBottom = elemTop + $(element).height();

            //If top of element is in view
            if ((elemBottom <= docViewBottom) && (elemTop >= docViewTop)) {
              //Run the callback
              $(element).trigger('in_view');

              return false; //pop item from array in the filter
            }
            return true; //Keep element in array
          });
        }
      }, 500);
    },

    //potentially replaces placeholder text with stuff from attributes
    replace_placeholders: function(str, element) {
      if(typeof str !== "string") {
        return str;
      }

      return str
      .replace('{text}', $(element).text())
      .replace('{href}', $(element).attr('href'))
      .replace('{class}', $(element).attr('class'))
      .replace('{id}', $(element).attr('id'));

    },

    //Prepares args to be sent to ga function
    prepare_ga_event_args: function(eventParams, DOMelement) {
      var event_args = [];

      event_args.eventCategory = this.replace_placeholders(eventParams.eventCategory, DOMelement);
      event_args.eventAction = this.replace_placeholders(eventParams.eventAction, DOMelement);


      if(eventParams.eventLabel) {
        event_args.eventLabel = this.replace_placeholders(eventParams.eventLabel, DOMelement);
      }

      if(eventParams.eventValue) {
        event_args.eventValue = this.replace_placeholders(eventParams.eventValue, DOMelement);
      }

      return event_args;
    },

    //binds an event
    bind_ga_event: function(eventParams) {

      var self = this;

      switch(eventParams.bindEvent) {

        //When element becomes in view on screen
        case 'in_view':

          var new_dom_elements = this.bind_in_view(eventParams.selector, function() {
            self.track_event(self.prepare_ga_event_args(eventParams, $(this)));
          });

          if(new_dom_elements === false) {
            return false; //Failed, console log maybe
          }

          this.in_view_elements = this.in_view_elements.concat(new_dom_elements);

          break;

        //Format outbount links
        case 'outbound_link':

          //update event to select outbound links
          eventParams = {
            selector: eventParams.selector + ' a[href^="http://"]:not[href*="' + window.location.hostname + '"]',
            bindEvent: 'click',
            eventCategory: eventParams.eventCategory,
            eventAction: eventParams.eventAction,
            eventLabel: '{href}',
          };

        default:

          var domElements;

          try{ //Try catch in case selector is invalid
            domElements = $(eventParams.selector);
          } catch(exception) { return false; } //console log this maybe

          if(!domElements) {
            return; //no elements found on page
          }

          //Bind normal events
          $(eventParams.selector).bindFirst(eventParams.bindEvent, function(e) {

            var event_args = self.prepare_ga_event_args(eventParams, e.target);
            //If a previous event has not prevented default, then we can assume we need to take the user to the href url
            if(typeof $(e.target).attr('href') !== "undefined" && $(e.target).attr('href').length && eventParams.bindEvent === 'click' && !e.isDefaultPrevented()) {

              //Don't navigate right away
              e.preventDefault();

              //Set callback for loading new page
              event_args.hitCallback = function() {
                document.location = e.target.href;
              };
              //Set a timeout so if Google doesn't repsond user is still navigated
              setTimeout(event_args.hitCallback, 1000);
            }

            //Now send event
            self.track_event(event_args);

          });
          break;
      }

      return true; //Successfully bound!

    },

    ga_defined: function() {
      return (typeof(ga) == "function");
    },

    track_event: function(eventParams) {

      if(this.is_debugging) {
        console.log('tracking event: ',eventParams);
      }

      if(this.ga_defined()) {


        eventParams.hitType = 'event';
        return ga('send', eventParams);

      } else {
        return false;
      }
    }

  };
})(jQuery);

// Array.map polyfill
if (Array.prototype.map === undefined) {
  Array.prototype.map = function(fn) {
    var rv = [];

    for(var i=0, l=this.length; i<l; i++)
      rv.push(fn(this[i]));

    return rv;
  };
}

// Array.filter polyfill
if (Array.prototype.filter === undefined) {
  Array.prototype.filter = function(fn) {
    var rv = [];

    for(var i=0, l=this.length; i<l; i++)
      if (fn(this[i])) rv.push(this[i]);

    return rv;
  };
}

if(typeof proq_ga_events !== "undefined") {
  var is_debugging = proq_ga_events.is_debugging;
  ga_event_tracking.init(proq_ga_events.events, is_debugging);
}
