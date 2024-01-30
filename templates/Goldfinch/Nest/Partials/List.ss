<% if NestedList %>
  <ul>
    <% loop NestedList %>
    <li><a href="{$Link}">$Title</a></li>
    <% end_loop %>
  </ul>
  <% include Goldfinch/Nest/Partials/Pagination %>
<% else %>
  <p>Sorry, there are no items that match your request</p>
<% end_if %>
