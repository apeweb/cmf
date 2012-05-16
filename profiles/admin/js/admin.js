// Check and uncheck all checkboxes
function checkAllCheckbox() {
  $('.checkall').click(function(){
    $(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);

    if (this.checked) {
      $(this).parents('fieldset:eq(0)').find('tbody tr').addClass('selected');
    }
    else {
      $(this).parents('fieldset:eq(0)').find('tbody tr').removeClass('selected');
    }
  });

  $('table :checkbox:not(.checkall)').click(function() {
    if (this.checked) {
      $(this).closest('tbody tr').addClass('selected');
    }
    else {
      $(this).closest('tbody tr').removeClass('selected');
    }

    if ($(this).closest('table').find(':checkbox').filter(':not(:checked):not(.checkall)').length === 0) {
      $(this).closest('table').find('.checkall').attr('checked', true);
    }
    else {
      $(this).closest('table').find('.checkall').attr('checked', false);
    }
  });
}

function addTableRowDragHandles () {
  $('.order_rows thead tr:not(.nodrag)').each(function () {
    $(this).addClass('nodrag');
    $(this).prepend('<th width="13" class="not_sortable">&nbsp;</th>');
  });

  $('.order_rows tbody tr').each(function() {
    if (!$(this).children('td:first').hasClass('dragHandle')) {
      $(this).prepend('<td class="dragHandle">&nbsp;</td>');
    }
  });

  $('.order_rows').tableDnD({
    onDragClass: 'table_drag_row',
    dragHandle: 'dragHandle'
  });
}

$(function(){
  // paging results per page
  $('.paging_results').each(function() {
    var select=$(document.createElement('select')).insertBefore($(this));
    $('> a', this).each(function() {
      option=$(document.createElement('option')).appendTo(select).val(this.href).html($(this).html());
    });
    $(this).find('a').remove();
    select.change(function(){
      window.location.href = this.value;
    })
  });

  // table headers
  $('.data').fixedtableheader();

  // inputs with labels as values
  $('.title_is_label').addClass('blur');

  $('.title_is_label').each(function() {
    if ($(this).attr('title') !== undefined) {
      $(this).val($(this).attr('title'));
      $(this).removeAttr('title');
    }
  });

  $('.title_is_label').focus(function(){
    $(this).removeClass('blur');

    if ($(this).attr('original_value') === undefined) {
      $(this).attr('original_value', $(this).val());
    }

    if ($(this).val() == $(this).attr('original_value')) {
      $(this).val('');
    }
  });

  $('.title_is_label').blur(function(){
    if ($(this).val() == '') {
      $(this).addClass('blur');
      $(this).val($(this).attr('original_value'));
    }
  });

  addTableRowDragHandles();

  // Menu
  function showMenu () {
    $(this).addClass('hovering');
  }

  function hideMenu () {
    $(this).removeClass('hovering');
  }

  var hoverConfig = {
       interval: 100,
       sensitivity: 4,
       over: showMenu,
       timeout: 100,
       out: hideMenu
  };

  $('.hover').hoverIntent(hoverConfig);

  $('.hover').click(function(){
    $(this).addClass('hovering');
  });

  $('.drop_down a').each(function() {
    if ($(this).attr('title') !== undefined) {
      $(this).append('<br /><span>' + $(this).attr('title') + '</span>');
      $(this).removeAttr('title');
    }
  });

  // Tables
  $('.data').tableHover();
  $('.resizable').colResizable({
    liveDrag: true,
    gripInnerHtml: '<div class="grip"></div>'
  });

  $('.sortable').tablesorter({
    ajax: true,
    ajaxRequestAppend: 'format=json'
  });

  checkAllCheckbox();

  // Tabs and panels
  $('.panel:not(.active)').hide();

  $('.product_tabs li').click(function(){
    $('.product_tabs li.active').removeClass('active');
    $(this).addClass('active');

    $('.panel').hide();
    $($(this).find('a').attr('href')).show();

    return false;
  });

  // Make a tree
  $('ul.tree, ol.tree').tree();

  // Add first and last classes for elements for styling
  $(':first-child').addClass('first');
  $(':last-child').addClass('last');
});