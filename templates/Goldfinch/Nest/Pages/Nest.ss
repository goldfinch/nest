<h1>$Title</h1>

<% if NestedList %>
  <ul>
    <% loop NestedList %>
    <li><a href="{$Link}">$Title</a></li>
    <% end_loop %>
  </ul>
<% else %>
  <p>Sorry, there are no items in this list</p>
<% end_if %>
