<h1 class="photo-gallery-name gallery__title">$Title</h1>
<% if $Content %>$Content<% end_if %>
<% if $AlbumCount > 1 %>
  <% if $PaginatedAlbums %>
    <ul id="photo-albums" class="albums">
      <% loop $PaginatedAlbums %>
        <li class="photo-album album">
          <div class="photo-album-cover album__thumb">
            <a href="$Link" title="View the $Name gallery">
              <% if $AlbumCover %>
                <img src="$AlbumCover.CroppedImage($Up.AlbumThumbnailWidth,$Up.AlbumThumbnailHeight).URL" alt="$Name">
              <% end_if %>
            </a>
          </div><!-- photo-album-cover album__ thumb -->
          <div class="photo-album-info album__info">
            <h4><a href="$Link" title="View the $Name gallery">$Name</a> <span class="photo-count">($PhotoCount)</span></h4>
            <p>$DescriptionExcerpt(300)</p>
          </div><!-- .photo-album-info album__thumb -->
        </li><!-- .photo-album albums -->
      <% end_loop %>
    </ul><!-- #photo-albums .albums -->
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

