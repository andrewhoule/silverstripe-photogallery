<% if $PaginatedPhotos %>
  <section class="photo-items">
    <% loop $PaginatedPhotos %>
      <div class="photo-item">
        <a href="$Photo.FitMax($Up.PhotoFullWidth,$Up.PhotoFullHeight).URL" title="$Caption" class="photo-item__link">
          <img src="$Photo.FillMax($Up.PhotoThumbnailWidth,$Up.PhotoThumbnailHeight).URL" alt="$Caption" class="photo-item__thumb">
        </a><!-- .photo-item__link -->
      </div><!-- .photo-item -->
    <% end_loop %>
  </section><!-- .photo-items -->

  <% if $PaginatedPhotos.MoreThanOnePage %>
    <div class="pagination">
      <% if $PaginatedPhotos.NotFirstPage %>
        <div class="pagination__direction-nav">
          <a href="$PaginatedPhotos.PrevLink" class="pagination__link pagination__direction-nav__link pagination__direction-nav__link--prev">&larr; Prev</a>
        </div><!-- .pagination__direction-nav -->
      <% end_if %>
      <ul class="pagination__items">
        <% loop $PaginatedPhotos.PaginationSummary %>
          <% if $Link %>
            <li class="pagination__item<% if $CurrentBool %> pagination__item--active<% end_if %>">
              <a href="$Link" class="pagination__link">$PageNum</a>
            </li>
          <% else %>
            <li class="pagination__item pagination__item--disabled">...</li>
          <% end_if %>
        <% end_loop %>
      </ul><!-- .pagination__items -->
      <% if $PaginatedPhotos.NotLastPage %>
        <div class="pagination__direction-nav">
          <a href="$PaginatedPhotos.NextLink" class="pagination__link pagination__direction-nav__link pagination__direction-nav__link--next">Next &rarr;</a>
        </div><!-- .pagination__direction-nav -->
      <% end_if %>
    </div><!-- pagination -->
  <% end_if %>

<% else %>
  <p class="photo-items__catch">There are no photos in this album.</p>
<% end_if %>
