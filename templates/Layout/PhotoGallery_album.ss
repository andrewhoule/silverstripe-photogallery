<h1 class="photo-album-name">$PhotoAlbum.Name</h1>
<% if PhotoAlbum.Description %><p>$PhotoAlbum.Description</p><% end_if %>
<% include PhotoItems %>        
<% if OtherAlbums %>
  	<h3>Other Albums</h3>
  	<ul class="button-list">
    	<% loop OtherAlbums %>
        	<li><a href="$Link">$Name</a></li>
    	<% end_loop %>
  	</ul>
<% end_if %>