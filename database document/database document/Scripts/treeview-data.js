$(document).ready(function () {
var data = [{"text":"Cover Page","icon":"images/folder.svg","href":"startpage.html","target":"DATA"},{"text":"Servers","icon":"images/folder.svg","href":"Servers\\Servers.html","target":"DATA"},{"text":"persian_inkedin.127.0.0.1","icon":"images/folder.svg","href":"Servers\\persian_inkedin.127.0.0.1\\persian_inkedin.127.0.0.1.html","target":"DATA","nodes":[{"text":"Databases","icon":"images/folder.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\Databases.html","target":"DATA","nodes":[{"text":"persian_inkedin","icon":"images/database.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\persian_inkedin.html","target":"DATA","nodes":[{"text":"Tables","icon":"images/folder.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\Tables.html","target":"DATA","nodes":[{"text":"plnk_avatar","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_avatar.html","target":"DATA"},{"text":"plnk_block","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_block.html","target":"DATA"},{"text":"plnk_connections","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_connections.html","target":"DATA"},{"text":"plnk_contact","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_contact.html","target":"DATA"},{"text":"plnk_country","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_country.html","target":"DATA"},{"text":"plnk_file","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_file.html","target":"DATA"},{"text":"plnk_like","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_like.html","target":"DATA"},{"text":"plnk_login","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_login.html","target":"DATA"},{"text":"plnk_message","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_message.html","target":"DATA"},{"text":"plnk_old_password","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_old_password.html","target":"DATA"},{"text":"plnk_person","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_person.html","target":"DATA"},{"text":"plnk_post","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_post.html","target":"DATA"},{"text":"plnk_post_view","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_post_view.html","target":"DATA"},{"text":"plnk_profile_view","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_profile_view.html","target":"DATA"},{"text":"plnk_report","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_report.html","target":"DATA"},{"text":"plnk_user","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_user.html","target":"DATA"},{"text":"plnk_user_item","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_user_item.html","target":"DATA"},{"text":"plnk_user_option","icon":"images/table.svg","href":"Servers\\persian_inkedin.127.0.0.1\\Databases\\persian_inkedin\\Tables\\plnk_user_option.html","target":"DATA"}]}]}]}]}];
$('#tree').treeview({levels: 3,data: data,enableLinks: true,injectStyle: false,highlightSelected: true,collapseIcon: 'images/tree-node-expanded.svg',expandIcon: 'images/tree-node-collapsed.svg'});
});
var loadEvent = function () {

  $('#btn-expand-nodes').on('click', function (e) {
    $('#tree').treeview('expandAll', { silent: true });
  });
  $('#btn-collapse-nodes').on('click', function (e) {
    $('#tree').treeview('collapseAll', { levels:3, silent: true });
  });
  
  var searchTimeOut;
  $('#input-search').on('input', function() {
    if(searchTimeOut != null)
      clearTimeout(searchTimeOut);
    searchTimeOut = setTimeout(function(){
      var pattern = $('#input-search').val();
      var tree = $('#tree');
      tree.treeview('collapseAll', { levels:3, silent: true });
      var options = { ignoreCase: true, exactMatch: false, revealResults: true };
      var results = tree.treeview('search', [pattern, options]);
    }, 500);
  });
  
  $('#tree').on('nodeSelected', function(event, data) {
    // navigate to link
    window.open (data.href, 'DATA', false)
  });
  // select first node.
  $('#tree').treeview('selectNode', [0, { silent: false }]);
}

if (window.addEventListener) {
  window.addEventListener('load', loadEvent, false);
}
else if (window.attachEvent) {
  window.attachEvent('onload', loadEvent);
}