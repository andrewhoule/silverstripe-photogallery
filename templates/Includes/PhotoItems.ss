<% if $PaginatedPhotos %>
  <ul id="album-photos" class="album__photos">
    <% loop $PaginatedPhotos %>
      <li class="album-photo album__photo">
        <a href="$PhotoSized(800,800).URL" title="$Caption">
          <img src="$PhotoCropped(125,125).URL" alt="$Caption">
          <span></span>
        </a>
      </li>
    <% end_loop %>
  </ul>
  <% if $PaginatedPhotos.MoreThanOnePage %>
    <ul class="pagination">
      <% loop $PaginatedPhotos.PaginationSummary %>
        <% if $Link %>
          <li <% if $CurrentBool %>class="active"<% end_if %>><a href="$Link">$PageNum</a></li>
        <% else %>
          <li>...</li>
        <% end_if %>
      <% end_loop %>
    </ul><!-- pagination -->
  <% end_if %>
<% else %>
  <p>There are no photos in this album.</p>
<% end_if %>
