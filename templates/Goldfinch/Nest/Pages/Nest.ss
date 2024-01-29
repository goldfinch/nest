<h1>$Title</h1>

<% if NestedList %>

  <ul>
    <% loop NestedList %>
    <li><a href="{$Link}">$Title</a></li>
    <% end_loop %>
  </ul>

  <% if $NestedList.MoreThanOnePage %>
    <% if $NestedList.NotFirstPage %>
        <a class="prev" href="$NestedList.PrevLink">Prev</a>
    <% end_if %>
    <% loop $NestedList.PaginationSummary %>
        <% if $CurrentBool %>
            $PageNum
        <% else %>
            <% if $Link %>
                <a href="$Link">$PageNum</a>
            <% else %>
                ...
            <% end_if %>
        <% end_if %>
    <% end_loop %>
    <% if $NestedList.NotLastPage %>
        <a class="next" href="$NestedList.NextLink">Next</a>
    <% end_if %>
<% end_if %>

<% else %>
  <p>Sorry, there are no items in this list</p>
<% end_if %>
