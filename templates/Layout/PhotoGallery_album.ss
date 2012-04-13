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
		
		<% control PhotoAlbum %>
	<% if Photos %>
		<ul class="photo-album">
		<% control Photos %>
			<li<% if IteratorPos(5) %> class="last"<% end_if %>>
				<a href="$PhotoSized(800,800).URL" rel="prettyPhoto[gallery]"><img src="$PhotoCropped(125,125).URL" alt="$Caption" /></a>
			</li>
		<% end_control %>
		</ul>
		
		
		
		<% if Photos.MoreThanOnePage %>
			<ul id="pagination">		
				<% if Photos.NotFirstPage %>
					<li class="previous"><a title="View the previous page" href="$Photos.PrevLink">&larr; Prev</a></li>				
				<% else %>	
					<li class="previous-off">&larr; Prev</li>
				<% end_if %>
				<% control Photos.PaginationSummary(5) %> 
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
				<% if Photos.NotLastPage %>
					<li class="next"><a title="View the next page" href="$Photos.NextLink">Next &rarr;</a></li>
				<% else %>
					<li class="next-off">Next &rarr;</li>				
				<% end_if %>
			</ul> 		
		<% end_if %>
		
		
	<% else %>
		<p>There are no photos in this album.</p>
		
	<% end_if %>
	
	<% if OtherAlbums %>
			<h3>Other Albums</h3>
			<ul class="button-list">
			<% control OtherAlbums %>
				<li><a href="$Link">$Name</a></li>
			<% end_control %>
			</ul>
		<% end_if %>
<% end_control %>
		
		$PageComments
	<% if Menu(2) %>
		</div>
	<% end_if %>
</div>


