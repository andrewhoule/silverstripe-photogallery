
<h1 class="photo-album__title">$PhotoAlbum.Name</h1>

<% if $PhotoAlbum.Description %>
  <p class="photo-album__description">$PhotoAlbum.Description</p>
<% end_if %>

<% include AndrewHoule\PhotoGallery\Pages\PhotoItems %>

<% if OtherAlbums %>
	<div class="other-albums-wrap">
		<h3 class="other-albums__title">Other Albums</h3>
	  <ul class="other-albums">
	    <% loop $OtherAlbums %>
        <li class="other-album">
          <a href="$Link" class="other-album__link">$Name</a>
        </li><!-- .other-album -->
	    <% end_loop %>
	  </ul><!-- .other-albums -->
	</div><!-- .other-albums-wrap -->
<% end_if %>
