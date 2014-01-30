<h1>$Title</h1>
<div class="content">$Content</div>
<% if PaginatedAlbums %>
    <div id="photo-albums">
        <% loop PaginatedAlbums %>
            <div class="photo-album">
                <div class="photo-album-cover">
                    <a href="$Link" title="View the $Name gallery">
                        <% if PhotoCropped %>
                             <img src="$PhotoCropped(230,170).URL" alt="$Name" />
                        <% end_if %>
                    </a>
                </div><!-- photo-album-cover -->
                <div class="photo-album-info">
                    <h4><a href="$Link" title="View the $Name gallery">$Name</a> <span class="photo-count">($PhotoCount)</span></h4>
                    <p>$DescriptionExcerpt(300)</p>
                </div><!-- photo-album-info -->
            </div><!-- photo-album -->
        <% end_loop %>
        <% if PaginatedAlbums.MoreThanOnePage %>
            <% if PaginatedAlbums.NotFirstPage %>
                <a class="prev" href="$PaginatedAlbums.PrevLink">Prev</a>
            <% end_if %>
            <% loop PaginatedAlbums.Pages %>
                <% if CurrentBool %>
                    $PageNum
                <% else %>
                    <% if Link %>
                        <a href="$Link">$PageNum</a>
                    <% else %>
                        ...
                    <% end_if %>
                <% end_if %>
            <% end_loop %>
            <% if PaginatedAlbums.NotLastPage %>
                <a class="next" href="$PaginatedAlbums.NextLink">Next</a>
            <% end_if %>
        <% end_if %>
    </div><!-- photo-albums -->
<% end_if %>