<div class="typography">
	<% if Menu(2) %>
		<% include SideBar %>
		<div id="Content">
	<% end_if %>
			
	<% if Level(2) %>
	  	<% include BreadCrumbs %>
	<% end_if %>
	
		<h2>$Title</h2>
	
		$Content
		$Form
		
		<% if PhotoAlbums %>
	<% control PhotoAlbums %>
			<div class="third<% if IteratorPos(3) %> endrow<% end_if %>">
				<a href="$Link" title="View the $Name gallery">
					<% if PhotoCropped %>
						<img src="$PhotoCropped(230,170).URL" alt="$Name" />
					<% else %>
						<img src="$BaseHref/mysite/code/photo_gallery/images/defualt-album-cover.jpg" width="230" height="170" alt="$Name" />
					<% end_if %>
				</a>
				<h4><a href="$Link" title="View the $Name gallery">$Name</a></h4>
				<p>$Description</p>
			</div>
			<% if IteratorPos(3) %><div class="cf"></div><% end_if %>
	<% end_control %>
	
	<% if PhotoAlbums.MoreThanOnePage %>
		<ul id="pagination">		
			<% if PhotoAlbums.NotFirstPage %>
				<li class="previous"><a title="View the previous page" href="$PhotoAlbums.PrevLink">&larr; Prev</a></li>				
			<% else %>	
				<li class="previous-off">&larr; Prev</li>
			<% end_if %>
			<% control PhotoAlbums.PaginationSummary(5) %> 
			   <% if CurrentBool %> 
			      <li class="active page-$PageNum">$PageNum</li>
			   <% else %> 
			      <% if Link %> 
			         <li class="page-$PageNum">
				         <a href="$Link" title="<% _t('GOTOPAGE', 'Go to page') %> $PageNum"> 
				            $PageNum 
				         </a>
			         </li> 
			      <% else %> 
			         <li class="pagination-break">…</li>
			      <% end_if %> 
			   <% end_if %> 
			<% end_control %>
			<% if PhotoAlbums.NotLastPage %>
				<li class="next"><a title="View the next page" href="$PhotoAlbums.NextLink">Next &rarr;</a></li>
			<% else %>
				<li class="next-off">Next &rarr;</li>				
			<% end_if %>
		</ul> 		
	<% end_if %>
	
	
<% else %>
	
	<% if PhotoItems %>
		<ul class="photo-album">
		<% control PhotoItems %>
			<li<% if IteratorPos(5) %> class="last"<% end_if %>>
				<a href="$PhotoSized(800,800).URL" rel="prettyPhoto[gallery]"><img src="$PhotoCropped(125,125).URL" alt="$Caption" /></a>
			</li>
		<% end_control %>
		</ul>
		
		
		
		<% if PhotoItems.MoreThanOnePage %>
			<ul id="pagination">		
				<% if PhotoItems.NotFirstPage %>
					<li class="previous"><a title="View the previous page" href="$PhotoItems.PrevLink">&larr; Prev</a></li>				
				<% else %>	
					<li class="previous-off">&larr; Prev</li>
				<% end_if %>
				<% control PhotoItems.PaginationSummary(5) %> 
				   <% if CurrentBool %> 
				      <li class="active page-$PageNum">$PageNum</li>
				   <% else %> 
				      <% if Link %> 
				         <li class="page-$PageNum">
					         <a href="$Link" title="<% _t('GOTOPAGE', 'Go to page') %> $PageNum"> 
					            $PageNum 
					         </a>
				         </li> 
				      <% else %> 
				         <li class="pagination-break">…</li>
				      <% end_if %> 
				   <% end_if %> 
				<% end_control %>
				<% if PhotoItems.NotLastPage %>
					<li class="next"><a title="View the next page" href="$PhotoItems.NextLink">Next &rarr;</a></li>
				<% else %>
					<li class="next-off">Next &rarr;</li>				
				<% end_if %>
			</ul> 		
		<% end_if %>
		
		
	<% else %>
		<p>There are not photos in this album.</p>
		
	<% end_if %>
	
<% end_if %>
		
		$PageComments
	<% if Menu(2) %>
		</div>
	<% end_if %>
</div>


