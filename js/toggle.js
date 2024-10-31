/**
 * toggle.js
 */
jQuery(function ($) {

     /**
     * Display after window loaded
     */
     var flg = 0;
     if (('localStorage' in window) && (window.localStorage !== null)) {
          if (localStorage.getItem('toggleState')) {
               var statusRaw = JSON.parse(localStorage.getItem('toggleState'));
               $('.trigger').each(function (i) {
                    var cptName = $(this).attr('id');
                    var status = statusRaw[cptName];
                    if (status === 'none') {
                         $(this).next('.contents').css('display', 'none');
                    } else {
                         $(this).addClass('active');
                    }
               });
               flg = 1;
          }
     }
     if (!flg) {
          $('.trigger').each(function () {
               $(this).addClass('active');
          });
     }

     /**
     * Toggle class and display
     */
     $(function () {
          $('.trigger').on('click', function () {
               $(this).toggleClass('active');
               $(this).next('.contents').slideToggle();
          });
     });

     /**
     * Save status to localstorage before unload
     */
     $(window).on('beforeunload', function (e) {
          if (('localStorage' in window) && (window.localStorage !== null)) {
               var statusArray = {};
               $('.trigger').each(function () {
                    var cptName = $(this).attr('id');
                    if ($(this).hasClass('active')) {
                         statusArray[cptName] = "block";
                    } else {
                         statusArray[cptName] = "none";
                    }
               });
               localStorage.setItem('toggleState', JSON.stringify(statusArray));
          }
     });
});
