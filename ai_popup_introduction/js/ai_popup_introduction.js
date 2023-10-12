(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.aiPopupIntro = {
    attach: function (context) {
      if (typeof drupalSettings.ai_popup_video_settings !== 'undefined') {
        $("#total_duration").hide();
        $("#watched_duration").hide();
        //hide the block after user visit
        var values = drupalSettings.ai_popup_video_settings
        if (!values.display_popup) {
          $("#spb-block-homepagevideoblock").remove();
          return true
        }
        // Declaring variable inside foreach - so it will not global.
        var modal_class = '',
                block_id = values.identifier,
                css_identity = '',
                spb_popup_id = '',
                modal_close_class = '',
                modal_minimize_class = '',
                modal_minimized_class = '',
                layout_class = '',
                class_exists = false,
                delays = '',
                browser_close_trigger = true
        // Set css selector
        css_identity = '.'
        if (values.css_selector == 1) {
          css_identity = '#'
        }

        // Assign dynamic css classes
        spb_popup_id = 'spb-' + block_id
        modal_class = block_id + '-modal'
        modal_close_class = block_id + '-modal-close'
        modal_minimize_class = block_id + '-modal-minimize'
        modal_minimized_class = block_id + '-modal-minimized'
        layout_class = '.' + modal_class + ' .spb-popup-main-wrapper'

        // Hide the popup initially
        $('.' + modal_class).hide()
        // remove the duplicate ids
        if (!$('#' + spb_popup_id).length) {
          // Wrap arround elements
          $(css_identity + block_id).
                  wrap($('<div class="' + modal_class + '"></div>'))
          // Wrap remaining elements
          $(css_identity + block_id).
                  wrap($('<div class="spb-popup-main-wrapper"></div>'))
          $('.' + modal_class).
                  wrap('<div id="' + spb_popup_id +
                          '" class="simple-popup-blocks-global"></div>')
          $(css_identity + block_id).
                  before($('<div class="spb-controls"></div>'))

          // Minimize button wrap
          if (values.minimize === "1") {
            $("#" + spb_popup_id + " .spb-controls").
                    prepend($('<span class="' + modal_minimize_class +
                            ' spb_minimize">-</span>'))
            $('.' + modal_class).
                    before($('<span class="' + modal_minimized_class +
                            ' spb_minimized"></span>'))
          }
          // Close button wrap
          if (values.close == 1) {
            $("#" + spb_popup_id + " .spb-controls").
                    prepend($('<span class="' + modal_close_class +
                            ' spb_close">&times;</span>'))
          }
          // Overlay
          if (values.overlay == 1) {
            $('.' + modal_class).addClass('spb_overlay')
          }
        }
        // Skip code for non popup pages.
        class_exists = $('#' + spb_popup_id).
                hasClass('simple-popup-blocks-global')
        if (!class_exists) {
          return true
        }

        // Inject layout class.
        switch (values.layout) {
          // Top left.
          case '0':
            $(layout_class).addClass('spb_top_left')
            $(layout_class).css({
              'width': values.width,
            })
            break
            // Top right.
          case '1':
            $(layout_class).addClass('spb_top_right')
            $(layout_class).css({
              'width': values.width,
            })
            break
            // Bottom left.
          case '2':
            $(layout_class).addClass('spb_bottom_left')
            $(layout_class).css({
              'width': values.width,
            })
            break
            // Bottom right.
          case '3':
            $(layout_class).addClass('spb_bottom_right')
            $(layout_class).css({
              'width': values.width,
            })
            break
            // Center.
          case '4':
            $(layout_class).addClass('spb_center')
            $(layout_class).css({
              'width': values.width,
            })
            break
            // Top Center.
          case '5':
            $(layout_class).addClass('spb_top_center')
            $(layout_class).css({})
            break
            // Top bar.
          case '6':
            $(layout_class).addClass('spb_top_bar')
            $(layout_class).css({})
            break
            // Right bar.
          case '7':
            $(layout_class).addClass('spb_bottom_bar')
            $(layout_class).css({})
            break
            // Bottom bar.
          case '8':
            $(layout_class).addClass('spb_left_bar')
            $(layout_class).css({
              'width': values.width,
            })
            break
            // Right bar.
          case '9':
            $(layout_class).addClass('spb_right_bar')
            $(layout_class).css({
              'width': values.width,
            })
            break
        }

        // Automatic trigger with delay
        if (values.trigger_method == 0 && values.delay > 0) {
          delays = values.delay * 1000
          $('.' + modal_class).delay(delays).fadeIn('slow')
          if (values.overlay == 1) {
            setTimeout(stopTheScroll, delays)
          }
        }
        // Automatic trigger without delay
        else if (values.trigger_method == 0) {
          $('.' + modal_class).show()
          $(css_identity + block_id).show()
          if (values.overlay == 1) {
            stopTheScroll()
          }
        }
        // Manual trigger
        else if (values.trigger_method == 1) {
          $(values.trigger_selector).click(function () {
            $('.' + modal_class).show()
            $(css_identity + block_id).show()
            if (values.overlay == 1) {
              stopTheScroll()
            }
            return false;
          })
        }
        // Browser close trigger
        else if (values.trigger_method == 2) {
          $(_html).mouseleave(function (e) {
            // Trigger only when mouse leave on top view port
            if (e.clientY > 20) {
              return
            }
            // Trigger only once per page
            if (!browser_close_trigger) {
              return
            }
            browser_close_trigger = false
            $('.' + modal_class).show()
            $(css_identity + block_id).show()
            if (values.overlay == 1) {
              stopTheScroll()
            }
          })
        }
        // Trigger for close button click
        $('.' + modal_close_class).click(function () {
          $('.' + modal_class).remove()
          startTheScroll()
        })
        // Trigger for minimize button click
        $('.' + modal_minimize_class).click(function () {
          $('.' + modal_class).hide()
          startTheScroll()
          $('.' + modal_minimized_class).show()
        })
        // Trigger for minimized button click
        $('.' + modal_minimized_class).click(function () {
          $('.' + modal_class).show()
          $(css_identity + block_id).show()
          if (values.overlay == 1) {
            stopTheScroll()
          }
          $('.' + modal_minimized_class).hide()
        })
        // Trigger for ESC button click
        if (values.escape == 1) {
          $(document).keyup(function (e) {
            if (e.keyCode == 27) { // Escape key maps to keycode `27`.
              //$('.' + modal_class).hide()
              startTheScroll()
              $('.' + modal_minimized_class).show()
            }
          })
        }
        // Remove the scrolling while overlay active
        function stopTheScroll() {
          $('body').css({
            'overflow': 'hidden',
          })
        }

        // Hide the close button on popup load for the duration of 30 sec.
        $(".block-homepagevideoblock-modal-close.spb_close").css('display', 'none');

        // Enable the scrolling while overlay inactive
        function startTheScroll() {
          $('body').css({
            'overflow': '',
          })
        }

        function setCustomCookie(usrclk) {

          form_data = {"case": usrclk, total_duration: $("#total_duration").text(), mini_time_duration: 60, watched_duration: $("#watched_duration").text()};
          $.ajax({
            url: values.callbackUrl,
            dataType: 'json',
            type: "POST",
            data: form_data,
            success: function (data) {
              $("#spb-block-homepagevideoblock").hide();
            },
            beforeSend: function (xhr) {
            },
            complete: function (data) {
            }
          });
        }
      }
    }
  }
})(jQuery, Drupal, drupalSettings);
// Remove the scrolling while overlay active
