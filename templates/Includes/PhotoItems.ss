
       <% if PaginatedPhotos %>
          <ul id="album-photos">
          <% loop PaginatedPhotos %>
            <li class="album-photo">
              <a href="$PhotoSized(800,800).URL" rel="prettyPhoto[gallery]"><img src="$PhotoCropped(125,125).URL" alt="$Caption" /></a>
            </li>
          <% end_loop %>
          </ul>
          
          <% if PaginatedPhotos.MoreThanOnePage %>
             <% if PaginatedPhotos.NotFirstPage %>
                 <a class="prev" href="$PaginatedPhotos.PrevLink">Prev</a>
             <% end_if %>
             <% loop PaginatedPhotos.Pages %>
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
             <% if PaginatedPhotos.NotLastPage %>
                 <a class="next" href="$PaginatedPhotos.NextLink">Next</a>
             <% end_if %>
         <% end_if %>
          
         <% else %>
          <p>There are no photos in this album.</p> 
        <% end_if %>