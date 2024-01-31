<form method="GET" id="myform" style="display: block; margin-bottom: 70px">
  <div style="margin-bottom: 10px">
    <input type="text" class="text" name="search" minlength="3" placeholder="Search" value="{$paramGet(search)}">
  </div>
  <input type="submit" value="Search">
  <% if paramGet(search) %><input type="reset" value="Reset" onclick="window.location.search = ''; window.location.href = window.location.href.replace(window.location.search, '')"><% end_if %>
</form>

<% if NestedList && paramGet(search) %>
  <p>$NestedList.Count  <% if $NestedList.Count == 1 %>result has<% else %>results have<% end_if %> been found</p>
<% end_if %>
