/**
 * counter.js
 */
jQuery(function ($) {

     /**
     * Display after window loaded
     */
     $(window).ready(function () {
          $('.str_counter').each(function () {
               var count = $(this).val().length;
               $(this).parent().find('.show-count').text(count);
          });
     });

     /**
     * Counter
     */
     $(function () {
          $('.str_counter').keyup(function () {
               var count = $(this).val().length;
               $(this).parent().find('.show-count').text(count);
          });
     });

     /**
     * Disabled save button
     */
     $(function () {
          $('.str_counter').keyup(function () {
               var count = $(this).val().length;
               var target = $(this).parent().find('.show-count');
               if (count > 160) {
                    target.addClass('over');
                    $('#submit_btn').prop('disabled', true);
               } else {
                    target.removeClass('over');
                    var flg = 0;
                    $('.str_counter').each(function () {
                         if ($(this).parent().find('.show-count').hasClass('over')) {
                              flg = 1;
                         }
                    });
                    if (!flg) {
                         $('#submit_btn').prop('disabled', false);
                    }
               }
          });
     });
});
