<ul>
<li><strong>ID:</strong> $ID</li>
<li><strong>Class name:</strong> $ClassName</li>
<li><strong>Has nesting above?:</strong> <% if isUpNested %>yes<% else %>no<% end_if %></li>
<% if isUpNested %>
<li>
  <ul>
    <% loop NestedChildren %>
      <h5>$Relationship</h5>
      <ul>
        <%-- <% loop List %>
          <li><a href="#">a</a></li>
        <% end_loop %> --%>
      </ul>
    <% end_loop %>
  </ul>
</li>
<% end_if %>
<li><strong>Has nesting below?:</strong> <% if isDownNested %>yes<% else %>no<% end_if %></li>
<% if isDownNested %>
<li>
  <ul>
    <% loop NestedParents %>
      <h5>$Relationship</h5>
      <ul>
        <%-- <% loop List %>
          <li><a href="#">a</a></li>
        <% end_loop %> --%>
      </ul>
    <% end_loop %>
  </ul>
</li>
<% end_if %>
</ul>
