window.matchMedia||(window.matchMedia=function(){"use strict";var e=window.styleMedia||window.media;if(!e){var t=document.createElement("style"),i=document.getElementsByTagName("script")[0],n=null;t.type="text/css";t.id="matchmediajs-test";i.parentNode.insertBefore(t,i);n="getComputedStyle"in window&&window.getComputedStyle(t,null)||t.currentStyle;e={matchMedium:function(e){var i="@media "+e+"{ #matchmediajs-test { width: 1px; } }";if(t.styleSheet){t.styleSheet.cssText=i}else{t.textContent=i}return n.width==="1px"}}}return function(t){return{matches:e.matchMedium(t||"all"),media:t||"all"}}}());
;
/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function (Drupal, debounce, CKEDITOR, $, displace, AjaxCommands) {
  Drupal.editors.ckeditor = {
    attach: function attach(element, format) {
      this._loadExternalPlugins(format);

      format.editorSettings.drupal = {
        format: format.format
      };

      var label = $('label[for=' + element.getAttribute('id') + ']').html();
      format.editorSettings.title = Drupal.t('Rich Text Editor, !label field', {
        '!label': label
      });

      return !!CKEDITOR.replace(element, format.editorSettings);
    },
    detach: function detach(element, format, trigger) {
      var editor = CKEDITOR.dom.element.get(element).getEditor();
      if (editor) {
        if (trigger === 'serialize') {
          editor.updateElement();
        } else {
          editor.destroy();
          element.removeAttribute('contentEditable');
        }
      }
      return !!editor;
    },
    onChange: function onChange(element, callback) {
      var editor = CKEDITOR.dom.element.get(element).getEditor();
      if (editor) {
        editor.on('change', debounce(function () {
          callback(editor.getData());
        }, 400));

        editor.on('mode', function () {
          var editable = editor.editable();
          if (!editable.isInline()) {
            editor.on('autoGrow', function (evt) {
              var doc = evt.editor.document;
              var scrollable = CKEDITOR.env.quirks ? doc.getBody() : doc.getDocumentElement();

              if (scrollable.$.scrollHeight < scrollable.$.clientHeight) {
                scrollable.setStyle('overflow-y', 'hidden');
              } else {
                scrollable.removeStyle('overflow-y');
              }
            }, null, null, 10000);
          }
        });
      }
      return !!editor;
    },
    attachInlineEditor: function attachInlineEditor(element, format, mainToolbarId, floatedToolbarId) {
      this._loadExternalPlugins(format);

      format.editorSettings.drupal = {
        format: format.format
      };

      var settings = $.extend(true, {}, format.editorSettings);

      if (mainToolbarId) {
        var settingsOverride = {
          extraPlugins: 'sharedspace',
          removePlugins: 'floatingspace,elementspath',
          sharedSpaces: {
            top: mainToolbarId
          }
        };

        var sourceButtonFound = false;
        for (var i = 0; !sourceButtonFound && i < settings.toolbar.length; i++) {
          if (settings.toolbar[i] !== '/') {
            for (var j = 0; !sourceButtonFound && j < settings.toolbar[i].items.length; j++) {
              if (settings.toolbar[i].items[j] === 'Source') {
                sourceButtonFound = true;

                settings.toolbar[i].items[j] = 'Sourcedialog';
                settingsOverride.extraPlugins += ',sourcedialog';
                settingsOverride.removePlugins += ',sourcearea';
              }
            }
          }
        }

        settings.extraPlugins += ',' + settingsOverride.extraPlugins;
        settings.removePlugins += ',' + settingsOverride.removePlugins;
        settings.sharedSpaces = settingsOverride.sharedSpaces;
      }

      element.setAttribute('contentEditable', 'true');

      return !!CKEDITOR.inline(element, settings);
    },
    _loadExternalPlugins: function _loadExternalPlugins(format) {
      var externalPlugins = format.editorSettings.drupalExternalPlugins;

      if (externalPlugins) {
        Object.keys(externalPlugins || {}).forEach(function (pluginName) {
          CKEDITOR.plugins.addExternal(pluginName, externalPlugins[pluginName], '');
        });
        delete format.editorSettings.drupalExternalPlugins;
      }
    }
  };

  Drupal.ckeditor = {
    saveCallback: null,

    openDialog: function openDialog(editor, url, existingValues, saveCallback, dialogSettings) {
      var $target = $(editor.container.$);
      if (editor.elementMode === CKEDITOR.ELEMENT_MODE_REPLACE) {
        $target = $target.find('.cke_contents');
      }

      $target.css('position', 'relative').find('.ckeditor-dialog-loading').remove();

      var classes = dialogSettings.dialogClass ? dialogSettings.dialogClass.split(' ') : [];
      classes.push('ui-dialog--narrow');
      dialogSettings.dialogClass = classes.join(' ');
      dialogSettings.autoResize = window.matchMedia('(min-width: 600px)').matches;
      dialogSettings.width = 'auto';

      var $content = $('<div class="ckeditor-dialog-loading"><span style="top: -40px;" class="ckeditor-dialog-loading-link">' + Drupal.t('Loading...') + '</span></div>');
      $content.appendTo($target);

      var ckeditorAjaxDialog = Drupal.ajax({
        dialog: dialogSettings,
        dialogType: 'modal',
        selector: '.ckeditor-dialog-loading-link',
        url: url,
        progress: { type: 'throbber' },
        submit: {
          editor_object: existingValues
        }
      });
      ckeditorAjaxDialog.execute();

      window.setTimeout(function () {
        $content.find('span').animate({ top: '0px' });
      }, 1000);

      Drupal.ckeditor.saveCallback = saveCallback;
    }
  };

  $(window).on('dialogcreate', function (e, dialog, $element, settings) {
    $('.ui-dialog--narrow').css('zIndex', CKEDITOR.config.baseFloatZIndex + 1);
  });

  $(window).on('dialog:beforecreate', function (e, dialog, $element, settings) {
    $('.ckeditor-dialog-loading').animate({ top: '-40px' }, function () {
      $(this).remove();
    });
  });

  $(window).on('editor:dialogsave', function (e, values) {
    if (Drupal.ckeditor.saveCallback) {
      Drupal.ckeditor.saveCallback(values);
    }
  });

  $(window).on('dialog:afterclose', function (e, dialog, $element) {
    if (Drupal.ckeditor.saveCallback) {
      Drupal.ckeditor.saveCallback = null;
    }
  });

  $(document).on('drupalViewportOffsetChange', function () {
    CKEDITOR.config.autoGrow_maxHeight = 0.7 * (window.innerHeight - displace.offsets.top - displace.offsets.bottom);
  });

  function redirectTextareaFragmentToCKEditorInstance() {
    var hash = window.location.hash.substr(1);
    var element = document.getElementById(hash);
    if (element) {
      var editor = CKEDITOR.dom.element.get(element).getEditor();
      if (editor) {
        var id = editor.container.getAttribute('id');
        window.location.replace('#' + id);
      }
    }
  }
  $(window).on('hashchange.ckeditor', redirectTextareaFragmentToCKEditorInstance);

  CKEDITOR.config.autoGrow_onStartup = true;

  CKEDITOR.config.autoGrow_maxHeight = 0.7 * window.innerHeight;

  CKEDITOR.timestamp = drupalSettings.ckeditor.timestamp;

  if (AjaxCommands) {
    AjaxCommands.prototype.ckeditor_add_stylesheet = function (ajax, response, status) {
      var editor = CKEDITOR.instances[response.editor_id];

      if (editor) {
        response.stylesheets.forEach(function (url) {
          editor.document.appendStyleSheet(url);
        });
      }
    };
  }
})(Drupal, Drupal.debounce, CKEDITOR, jQuery, Drupal.displace, Drupal.AjaxCommands);;
/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function ($, CKEDITOR) {
  var convertToOffCanvasCss = function convertToOffCanvasCss(originalCss) {
    var selectorPrefix = '#drupal-off-canvas ';
    var skinPath = '' + CKEDITOR.basePath + CKEDITOR.skinName + '/';
    var css = originalCss.substring(originalCss.indexOf('*/') + 2).trim().replace(/}/g, '}' + selectorPrefix).replace(/,/g, ',' + selectorPrefix).replace(/url\(/g, skinPath);
    return '' + selectorPrefix + css;
  };

  var insertCss = function insertCss(cssToInsert) {
    var offCanvasCss = document.createElement('style');
    offCanvasCss.innerHTML = cssToInsert;
    offCanvasCss.setAttribute('id', 'ckeditor-off-canvas-reset');
    document.body.appendChild(offCanvasCss);
  };

  var addCkeditorOffCanvasCss = function addCkeditorOffCanvasCss() {
    if (document.getElementById('ckeditor-off-canvas-reset')) {
      return;
    }

    CKEDITOR.skinName = CKEDITOR.skin.name;

    var editorCssPath = CKEDITOR.skin.getPath('editor');
    var dialogCssPath = CKEDITOR.skin.getPath('dialog');

    var storedOffCanvasCss = window.localStorage.getItem('Drupal.off-canvas.css.' + editorCssPath + dialogCssPath);

    if (storedOffCanvasCss) {
      insertCss(storedOffCanvasCss);
      return;
    }

    $.when($.get(editorCssPath), $.get(dialogCssPath)).done(function (editorCss, dialogCss) {
      var offCanvasEditorCss = convertToOffCanvasCss(editorCss[0]);
      var offCanvasDialogCss = convertToOffCanvasCss(dialogCss[0]);
      var cssToInsert = '#drupal-off-canvas .cke_inner * {background: transparent;}\n          ' + offCanvasEditorCss + '\n          ' + offCanvasDialogCss;
      insertCss(cssToInsert);

      if (CKEDITOR.timestamp && editorCssPath.indexOf(CKEDITOR.timestamp) !== -1 && dialogCssPath.indexOf(CKEDITOR.timestamp) !== -1) {
        Object.keys(window.localStorage).forEach(function (key) {
          if (key.indexOf('Drupal.off-canvas.css.') === 0) {
            window.localStorage.removeItem(key);
          }
        });
        window.localStorage.setItem('Drupal.off-canvas.css.' + editorCssPath + dialogCssPath, cssToInsert);
      }
    });
  };

  addCkeditorOffCanvasCss();
})(jQuery, CKEDITOR);;
/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function ($, Drupal, drupalSettings, storage) {
  var currentUserID = parseInt(drupalSettings.user.uid, 10);

  var secondsIn30Days = 2592000;
  var thirtyDaysAgo = Math.round(new Date().getTime() / 1000) - secondsIn30Days;

  var embeddedLastReadTimestamps = false;
  if (drupalSettings.history && drupalSettings.history.lastReadTimestamps) {
    embeddedLastReadTimestamps = drupalSettings.history.lastReadTimestamps;
  }

  Drupal.history = {
    fetchTimestamps: function fetchTimestamps(nodeIDs, callback) {
      if (embeddedLastReadTimestamps) {
        callback();
        return;
      }

      $.ajax({
        url: Drupal.url('history/get_node_read_timestamps'),
        type: 'POST',
        data: { 'node_ids[]': nodeIDs },
        dataType: 'json',
        success: function success(results) {
          Object.keys(results || {}).forEach(function (nodeID) {
            storage.setItem('Drupal.history.' + currentUserID + '.' + nodeID, results[nodeID]);
          });
          callback();
        }
      });
    },
    getLastRead: function getLastRead(nodeID) {
      if (embeddedLastReadTimestamps && embeddedLastReadTimestamps[nodeID]) {
        return parseInt(embeddedLastReadTimestamps[nodeID], 10);
      }
      return parseInt(storage.getItem('Drupal.history.' + currentUserID + '.' + nodeID) || 0, 10);
    },
    markAsRead: function markAsRead(nodeID) {
      $.ajax({
        url: Drupal.url('history/' + nodeID + '/read'),
        type: 'POST',
        dataType: 'json',
        success: function success(timestamp) {
          if (embeddedLastReadTimestamps && embeddedLastReadTimestamps[nodeID]) {
            return;
          }

          storage.setItem('Drupal.history.' + currentUserID + '.' + nodeID, timestamp);
        }
      });
    },
    needsServerCheck: function needsServerCheck(nodeID, contentTimestamp) {
      if (contentTimestamp < thirtyDaysAgo) {
        return false;
      }

      if (embeddedLastReadTimestamps && embeddedLastReadTimestamps[nodeID]) {
        return contentTimestamp > parseInt(embeddedLastReadTimestamps[nodeID], 10);
      }

      var minLastReadTimestamp = parseInt(storage.getItem('Drupal.history.' + currentUserID + '.' + nodeID) || 0, 10);
      return contentTimestamp > minLastReadTimestamp;
    }
  };
})(jQuery, Drupal, drupalSettings, window.localStorage);;
/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function (window, Drupal, drupalSettings) {
  window.addEventListener('load', function () {
    if (drupalSettings.history && drupalSettings.history.nodesToMarkAsRead) {
      Object.keys(drupalSettings.history.nodesToMarkAsRead).forEach(Drupal.history.markAsRead);
    }
  });
})(window, Drupal, drupalSettings);;
/**
 * @file
 * Attaches is useful rating.
 */

(function ($, Drupal) {
  Drupal.behaviors.usefulRating = {
    attach: function (context, settings) {
     $('body').find('.useful').each(function () {
       var $this = $(this);
       $(this).find('select').once('processed').each(function () {
         $this.find('[type=submit]').hide();
         var $select = $(this);
         $select.after('<div class="useful-rating"><a href="#"><i class="fa fa-thumbs-down"></i></a><a href="#"><i class="fa fa-thumbs-up"></a></i></div>').hide();
         $this.find('.useful-rating a').eq(0).each(function () {
           $(this).bind('click',function (e) {
             e.preventDefault();
             $select.get(0).selectedIndex = 0;
             $this.find('[type=submit]').trigger('click');
             $this.find('a').addClass('disabled');
           })
         })
         $this.find('.useful-rating a').eq(1).each(function () {
           $(this).bind('click',function (e) {
             e.preventDefault();
             $select.get(0).selectedIndex = 1;
             $this.find('[type=submit]').trigger('click');
             $this.find('a').addClass('disabled');
           })
         })
       })
     });
    }
  };
})(jQuery, Drupal);
;
/**
 * @file
 * Attaches fivestar rating.
 */

(function ($, Drupal) {
  Drupal.behaviors.fiveStarRating = {
    attach: function (context, settings) {
     $('body').find('.fivestar').each(function () {
      var $this = $(this);
      var $select = $this.find('select');
      var value = $select.data('default-value');
      if (!value) {
        value = -1;
      }
      var options = {
        theme: ($select.data('style') == 'default') ? 'css-stars' : $select.data('style'),
        showSelectedRating: true,
        initialRating: value,
        allowEmpty: true,
        readonly: ($select.attr('disabled')) ? true : false,
        onSelect: function (value, text) {
          $this.find('select').barrating('readonly', true);
          $this.find('input[type=submit]').trigger('click');
          $this.find('a').addClass('disabled');
        },
      };
      $this.find('select').once('processed').barrating('show', options);
      $this.find('input[type=submit]').hide();
    });
    }
  };
})(jQuery, Drupal);
;