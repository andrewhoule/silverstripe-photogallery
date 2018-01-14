<h1 class="photo-album-name album__title">$PhotoAlbum.Name</h1>
<% if PhotoAlbum.Description %><p>$PhotoAlbum.Description</p><% end_if %>
<% include PhotoItems %>
<% if OtherAlbums %>
	<div class="other-albums albums__other">
		<h3>Other Albums</h3>
	  <ul class="button-list">
	    <% loop OtherAlbums %>
        <li><a href="$Link">$Name</a></li>
	    <% end_loop %>
	  </ul>
	</div><!-- .other-albums albums__other -->
<% end_if %>
