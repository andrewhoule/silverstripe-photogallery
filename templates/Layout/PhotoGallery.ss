<h1 class="photo-gallery-name">$Title</h1>
<% if $Content %>$Content<% end_if %>
<% if AlbumCount > 1 %>
    <% if PaginatedAlbums %>
        <ul id="photo-albums">
            <% loop PaginatedAlbums %>
                <li class="photo-album">
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
                </li><!-- photo-album -->
            <% end_loop %>
        </ul><!-- photo-albums -->
        <% if PaginatedAlbums.MoreThanOnePage %>    
            <div class="pagination">
                <div class="page-numbers">
                    <ul>
                        <% if PaginatedAlbums.NotFirstPage %>
                            <li class="previous"><a title="<% _t('VIEWPREVIOUSPAGE','View the previous page') %>" href="$PaginatedAlbums.PrevLink"><% _t('PREVIOUS','&larr;') %></a></li>                
                        <% else %>  
                            <li class="previous-off"><% _t('PREVIOUS','&larr;') %></li>
                        <% end_if %>
                        <% loop PaginatedAlbums.Pages %>
                            <% if CurrentBool %>
                                <li class="active">$PageNum</li>
                            <% else %>
                                <li><a href="$Link" title="<% sprintf(_t('VIEWPAGENUMBER','View page number %s'),$PageNum) %>">$PageNum</a></li>                
                            <% end_if %>
                        <% end_loop %>
                        <% if PaginatedAlbums.NotLastPage %>
                            <li class="next"><a title="<% _t('VIEWNEXTPAGE', 'View the next page') %>" href="$PaginatedAlbums.NextLink"><% _t('NEXT','&rarr;') %></a></li>
                        <% else %>
                            <li class="next-off"><% _t('NEXT','&rarr;') %> </li>                
                        <% end_if %>
                    </ul>
                </div><!-- page-numbers -->       
            </div><!-- .pagination -->
        <% end_if %>
    <% end_if %>
<% end_if %>
<% if AlbumCount == 1 %>
    <% loop PaginatedAlbums %>
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
    