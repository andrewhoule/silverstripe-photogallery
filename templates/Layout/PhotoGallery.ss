<h1 class="photo-gallery-name">$Title</h1>
<% if $Content %>$Content<% end_if %>
<% if $AlbumCount > 1 %>
  <% if $PaginatedAlbums %>
    <ul id="photo-albums">
      <% loop $PaginatedAlbums %>
        <li class="photo-album">
          <div class="photo-album-cover">
            <a href="$Link" title="View the $Name gallery">
              <% if $PhotoCropped %>
                <img src="$PhotoCropped(230,170).URL" alt="$Name" />
              <% end_if %>
            </a>
          </div><!-- photo-album-cover -->
          <div class="photo-album-info">
            <h4><a href="$Link" title="View the $Name gallery">$Name</a> <span class="photo-count">($PhotoCount)</span></h4>
            <p>$DescriptionExcerpt(300)</p>
          </div><!-- photo-album-info -->
        </li><!-- photo-album -->
      <% end_loop %>
    </ul><!-- photo-albums -->
    <% if $PaginatedAlbums.MoreThanOnePage %>
      <ul class="pagination">
        <% loop $PaginatedAlbums.PaginationSummary %>
          <% if $Link %>
            <li <% if $CurrentBool %>class="active"<% end_if %>><a href="$Link">$PageNum</a></li>
          <% else %>
            <li>...</li>
          <% end_if %>
        <% end_loop %>
      </ul><!-- pagination -->
    <% end_if %>
  <% end_if %>
<% end_if %>
<% if $AlbumCount == 1 %>
  <% loop $PaginatedAlbums %>
    <div class="photo-album-info">
      <h4>$Name</h4>
      <p>$DescriptionExcerpt(300)</p>
    </div><!-- photo-album-info -->
  <% end_loop %>
  <% include PhotoItems %> 
<% end_if %>
<% if AlbumCount < 1 %>
    No albums to view yet.
<% end_if %>
    