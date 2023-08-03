<h1>Nest page</h1>

<ul>
<% loop NestedList %>
  <li><a href="{$Link}">$Title</a></li>
<% end_loop %>
</ul>
