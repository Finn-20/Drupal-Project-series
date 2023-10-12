(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.aiPopupIntroDuration = {
    attach: function () {
      // Get the video element with id="popupVideo"
      if (document.getElementById("popupVideo") !== null) {
        var vid = document.getElementById("popupVideo");
        // Assign an ontimeupdate event to the video element, and
        // execute a function if the current playback position has changed
        var isCustomCookieSet = false;
        vid.ontimeupdate = function () {
          popupVideoDetails()
        };
        function popupVideoDetails() {
          // Display the current position of the video in a <p> element with id="total_duration"
          $("#total_duration").innerHTML = vid.duration;
          $("#watched_duration").innerHTML = vid.currentTime;
          // Display the close button after the duration of 30 sec.
          if (vid.currentTime >= 20 && !isCustomCookieSet) {
            isCustomCookieSet = true;
            $(".block-homepagevideoblock-modal-close.spb_close").css('display', 'block');
          }
          // Display the close button after the duration of 60 sec.
          if (vid.currentTime == vid.duration) {
            setCustomCookie('autoplay');
          }
        }
        //Handling the event of the custom event for the video played.
        $("#videowatchlater").unbind().click(function () {
          setCustomCookie('watch_later')
        });
        $(".block-homepagevideoblock-modal-close.spb_close").unbind().click(function () {
          setCustomCookie('close')
        });
        function setCustomCookie(usrclk) {
          form_data = {"case": usrclk, total_duration: vid.duration, mini_time_duration: 60, watched_duration: vid.currentTime};
          $.ajax({
            url: drupalSettings.ai_popup_video_settings.callbackUrl,
            dataType: 'json',
            type: "POST",
            data: form_data,
            success: function (data) {
              if (usrclk != "autoplay") {
                $("#spb-block-homepagevideoblock").remove();
                $('body').css({
                  'overflow': '',
                })
              }
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