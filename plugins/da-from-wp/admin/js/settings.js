/**
 * Settings page JS.
 *
 * JS for the settings page in WP admin.
 */

/**
 * Internationalization for JavaScript
 * @link https://make.wordpress.org/core/2018/11/09/new-javascript-i18n-support-in-wordpress/
 */
const { __, _x, _n, _nx } = wp.i18n;

/**
 * Choose API - Hide / Show the matching textarea
 */
jQuery(document).ready(function($) {
  $(
    'input[name="clanroyale_settings[clanroyale_settings_api_choose]"]:checked'
  ).each(function() {
    $('.clanroyale-settings-api-' + $(this).val() + '-token').show();
  });
  $('input[name="clanroyale_settings[clanroyale_settings_api_choose]"]').click(
    function() {
      $('.clanroyale-settings-api-token').hide();
      $('.clanroyale-settings-api-' + $(this).val() + '-token').show();
    }
  );
});

/**
 * Test API connection.
 */
jQuery(document).ready(function($) {
  $('#clanroyale-settings-api-test-button').click(function() {
    var element = $(this);
    $(element).addClass('loading');
    data = {
      action: 'clanroyale_settings_api_test_ajax'
    };

    $.post(ajaxurl, data, function(reponse) {
      //Todo: Check response and send message accordingly.
      $(element)
        .removeClass('loading')
        .addClass('success');
      setTimeout(function() {
        $(element).removeClass('success');
      }, 3000);
    });

    return false;
  });
});

/**
 * Use Ajax to clear cache and the related form elements.
 */
jQuery(document).ready(function($) {
  $('#clanroyale-settings-form-button-clearcache').click(function() {
    var element = $(this);
    $(element).addClass('loading');
    data = {
      action: 'clanroyale_clear_api_requests_cache'
    };

    $.post(ajaxurl, data, function(reponse) {
      $('#clanroyale-settings-form-textarea-transient-keys').val('');
      $(element)
        .removeClass('loading')
        .addClass('success')
        .prop('disabled', true);
    });

    return false;
  });
});

/**
 * Confirm modal on reset button
 */
jQuery(document).ready(function($) {
  $('#clanroyale-settings-reset').click(function() {
    return confirm(
      __(
        'Are you sure? This will reset all settings and the cache.',
        'clanroyale'
      )
    );
  });
});
