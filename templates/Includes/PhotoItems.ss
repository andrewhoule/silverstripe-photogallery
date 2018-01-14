<% if $PaginatedPhotos %>
  <ul id="album-photos" class="album__photos">
    <% loop $PaginatedPhotos %>
      <li class="album__photo">
        <a href="$Photo.FitMax($Up.PhotoFullWidth,$Up.PhotoFullHeight).URL" title="$Caption">
          <img src="$Photo.Fill($Up.PhotoThumbnailWidth,$Up.PhotoThumbnailHeight).URL" alt="$Caption">
        </a>
      </li><!-- .album__photo -->
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
