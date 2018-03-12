
<h1 class="photogallery__title">$Title</h1>
<% if $Content %>$Content<% end_if %>

<% if $PaginatedAlbums.Count() > 1 %>
  <section class="photo-albums">
    <% loop $PaginatedAlbums %>
      <div class="photo-album">
        <% if $AlbumThumb %>
          <figure class="photo-album__thumb">
            <a href="$Link" title="View the {$Name} gallery" class="photo-album__cover__link">
              <img src="$AlbumThumb.FillMax($Up.AlbumThumbnailWidth,$Up.AlbumThumbnailHeight).URL" alt="$Name" class="photo-album__thumb__img">
            </a><!-- .photo-album__thumb__link -->
          </figure><!-- .photo-album__thumb -->
        <% end_if %>
        <div class="photo-album__info">
          <h4 class="photo-album__title"><a href="$Link" class="photo-album__info__link" title="View the $Name gallery">$Name</a> <span class="photo-album__count">($Photos.Count())</span></h4>
          <p class="photo-album__excerpt">$DescriptionExcerpt(300)</p>
        </div><!-- .photo-album__info -->
      </div><!-- .photo-album -->
    <% end_loop %>
  </section><!-- .photo-albums -->
  <% if $PaginatedAlbums.MoreThanOnePage %>
    <div class="pagination">
      <% if $PaginatedAlbums.NotFirstPage %>
        <div class="pagination__direction-nav">
          <a href="$PaginatedAlbums.PrevLink" class="pagination__link pagination__direction-nav__link pagination__direction-nav__link--prev">&larr; Prev</a>
        </div><!-- .pagination__direction-nav -->
      <% end_if %>
      <ul class="pagination__items">
        <% loop $PaginatedAlbums.PaginationSummary %>
          <% if $Link %>
            <li class="pagination__item<% if $CurrentBool %> pagination__item--active<% end_if %>">
              <a href="$Link" class="pagination__link">$PageNum</a>
            </li>
          <% else %>
            <li class="pagination__item pagination__item--disabled">...</li>
          <% end_if %>
        <% end_loop %>
      </ul><!-- .pagination__items -->
      <% if $PaginatedAlbums.NotLastPage %>
        <div class="pagination__direction-nav">
          <a href="$PaginatedAlbums.NextLink" class="pagination__link pagination__direction-nav__link pagination__direction-nav__link--next">Next &rarr;</a>
        </div><!-- .pagination__direction-nav -->
      <% end_if %>
    </div><!-- pagination -->
  <% end_if %>
<% end_if %>

<% if $PaginatedAlbums.Count() == 1 %>
  <% loop $PaginatedAlbums %>
    <div class="photo-album__info">
      <h4 class="photo-album__title">$Name</h4>
      <p class="photo-album__excerpt">$DescriptionExcerpt(300)</p>
    </div><!-- photo-album-info -->
  <% end_loop %>
  <% include AndrewHoule\PhotoGallery\Pages\PhotoItems %>
<% end_if %>

<% if $PaginatedAlbums.Count() < 1 %>
  <p class="photo-album__catch">There are no albums to view yet.</p>
<% end_if %>

