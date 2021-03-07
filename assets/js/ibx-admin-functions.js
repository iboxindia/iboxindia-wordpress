function refreshModalInfo() {
  var modal = jQuery('#install-update-dialog');  
  jQuery( '.header, .name', modal ).text('');
  jQuery( '.author', modal ).text('');
  jQuery( '.type', modal ).text('');
  jQuery( '.version', modal ).text('');
  jQuery( '.downloaded', modal ).text('');
  jQuery( '.install-package', modal ).hide();
  showPackageInfo(jQuery( 'button', modal )[0].dataset.slug);
}

function getFormData($form){
  var unindexed_array = $form.serializeArray();
  var indexed_array = {};

  jQuery.map(unindexed_array, function(n, i){
      indexed_array[n['name']] = n['value'];
  });

  return indexed_array;
}
function reloadPage() {
  window.location.reload();
}
jQuery(function($) {
  $('select').formSelect();
  $('.tabs').tabs();
  $('.ajax-form').on('submit', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $this = $(this);
    var link = $this.attr('action');
    var method = $this.attr('method');
    var successCallback = eval($this.data('success-callback'));
    var data = getFormData($this);

    jQuery.ajax({
      type : method,
      dataType : "json",
      url : link,
      data: data,
      success: function(response) {
        if(successCallback) {
          successCallback(response);
        }
        console.log(response.data);
      },
      error: function(resp) {
        console.log('error - ' + resp);
      }
    });
  })
})
/** return 1 if v1 is greater that v2 */
function versionCompare(v1, v2, options) {
  var lexicographical = options && options.lexicographical,
      zeroExtend = options && options.zeroExtend,
      v1parts = v1.split('.'),
      v2parts = v2.split('.');

  function isValidPart(x) {
      return (lexicographical ? /^\d+[A-Za-z]*$/ : /^\d+$/).test(x);
  }

  if (!v1parts.every(isValidPart) || !v2parts.every(isValidPart)) {
      return NaN;
  }

  if (zeroExtend) {
      while (v1parts.length < v2parts.length) v1parts.push("0");
      while (v2parts.length < v1parts.length) v2parts.push("0");
  }

  if (!lexicographical) {
      v1parts = v1parts.map(Number);
      v2parts = v2parts.map(Number);
  }

  for (var i = 0; i < v1parts.length; ++i) {
      if (v2parts.length == i) {
          return 1;
      }

      if (v1parts[i] == v2parts[i]) {
          continue;
      }
      else if (v1parts[i] > v2parts[i]) {
          return 1;
      }
      else {
          return -1;
      }
  }

  if (v1parts.length != v2parts.length) {
      return -1;
  }

  return 0;
}
function loadPackages(type) {
  link = iboxindiaConfig.ajaxUrl + "?action=iboxindia_packages&type=" + type
  jQuery.ajax({
    type : "post",
    dataType : "json",
    url : link,
    success: function(response) {
      renderPackages(response.data, type);
    },
    error: function(resp) {
      console.log(resp);
    }
  });
};
function showPackageInfo(slug) {
  if(!slug) {
    slug = jQuery(this).data('slug');
  }
  var modal = jQuery('#install-update-dialog');
  link = iboxindiaConfig.ajaxUrl + "?action=iboxindia_package_info&slug=" + slug;
  jQuery.ajax({
    type : "post",
    dataType : "json",
    url : link,
    data : { slug: slug },
    success: function(response) {
      jQuery( '.header, .name', modal ).text(response.data.name);
      jQuery( '.author', modal ).text(response.data.author);
      jQuery( '.type', modal ).text(response.data.type);
      jQuery( '.version', modal ).text(response.data.latest_version);
      jQuery( '.downloaded', modal ).text(response.fileExists ? 'check_circle' : 'dangerous');
      
      if(response.fileExists) {
        jQuery( '.install-package', modal ).show();
      }
    }
  });
}
function downloadPackage(slug) {
  if(!slug) {
    slug = jQuery(this).data('slug');
  }
  var modal = jQuery('#install-update-dialog');
  link = iboxindiaConfig.ajaxUrl + "?action=iboxindia_download_package&slug=" + slug;
  jQuery.ajax({
    type : "post",
    dataType : "json",
    url : link,
    data : { slug: slug },
    success: function(response) {
      jQuery( '.info', modal ).text(JSON.stringify(response.data));
      if(response.data) {
        jQuery( '.install-package', modal ).show();
      }
    }
  });
}
function installDownloadedPackage(slug, type) {
  if(!slug) {
    slug = jQuery(this).data('slug');
  }
  var modal = jQuery('#install-update-dialog');
  link = iboxindiaConfig.ajaxUrl + "?action=iboxindia_install_package&slug=" + slug + "&type=" + type;
  jQuery.ajax({
    type : "post",
    dataType : "json",
    url : link,
    data : { slug: slug, type: type },
    success: function(response) {
      jQuery( '.info', modal ).text(JSON.stringify(response.data));
    }
  });
}

function installPackage() {
  var modal = jQuery('#install-update-dialog');
  var slug = jQuery(this).closest('.ibx-item').data('slug');
  var type = jQuery(this).closest('.ibx-item').data('type');
  jQuery('button', modal).attr('data-slug', slug);
  jQuery('button', modal).attr('data-type', type);
  modal.modal( 'open' );
};
function updatePackage() {
  console.log(this);
};


function renderPackages(packages, type) {
  existingItems = iboxindiaConfig.existingItems[type];
  var wrap = jQuery('.ibx-items-browser .ibx-items');
  var dummyItem = jQuery('.dummy', wrap);
  jQuery.each(packages, function (index, package) {
    var item = dummyItem.clone(true);
    item.removeClass('dummy');
    jQuery('.ibx-item', item).attr('data-slug', package.slug);
    jQuery('.ibx-item', item).attr('data-type', package.type);
    var existingItem = existingItems[package.slug];
    jQuery('.ibx-item-version', item).text(package.latest_version);
    jQuery('img', item).attr('src', package.thumbnail_url);
    jQuery('img', item).attr('alt', package.name);
    jQuery('.ibx-item-name', item).attr('id', package.slug).text(package.name);

    if(!existingItem) {
      jQuery('.update-message', item).remove();
      jQuery('.button.update-button', item).remove();
    } else {
      jQuery('.button.install-button', item).remove();
      if ( versionCompare( package.latest_version, existingItem.Version ) >= 1 ) { //requires update
        jQuery('.button.update-button', item).attr('data-existing-ver', existingItem.Version);
        jQuery('.button.update-button', item).attr('data-current-ver', package.latest_version);
      } else {
        jQuery('.ibx-item-actions', item).remove();
      }
    }
    wrap.append(item);
  });
}