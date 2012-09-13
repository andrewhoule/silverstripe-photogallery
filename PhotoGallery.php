<?php

class PhotoGallery extends Page {
 
 	static $db = array(
	   "AlbumsPerPage" => "Int",
	   "PhotosPerPage" => "Int"
	);
 	
    static $has_many = array (
		"PhotoAlbums" => "PhotoAlbum",
		"PhotoItems" => "PhotoItem"
	);
	
	public static $defaults = array(
      "AlbumsPerPage" => '6',
      "PhotosPerPage" => '20'
   );
	
	static $icon = "photogallery/images/photogallery";
	
	function getCMSFields() {
      $fields = parent::getCMSFields();
      $AlbumsGridFieldConfig = GridFieldConfig::create()->addComponents(
         new GridFieldToolbarHeader(),
         new GridFieldAddNewButton('toolbar-header-right'),
         new GridFieldSortableHeader(),
         new GridFieldDataColumns(),
         new GridFieldPaginator(10),
         new GridFieldEditButton(),
         new GridFieldDeleteAction(),
         new GridFieldDetailForm(),
         new GridFieldSortableRows("SortID")
      );
      $AlbumsGridField = new GridField("PhotoAlbums", "Photo Album", $this->PhotoAlbums(), $AlbumsGridFieldConfig);
      $fields->addFieldToTab("Root.Photos", $AlbumsGridField);
      $fields->addFieldToTab("Root.Config", new TextField('AlbumsPerPage','Number of Albums Per Page'));
      $fields->addFieldToTab("Root.Config", new TextField('PhotosPerPage','Number of Photos Per Page'));
		return $fields;
   }
 
}
 
class PhotoGallery_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::CSS('photogallery/prettyPhoto/css/prettyPhoto.css');
		Requirements::CSS('photogallery/css/photogallery.css');
	}
	
	static $allowed_actions = array(
      'album'
   ); 
    
   public function getAlbum() {
      $Params = $this->getURLParams();
      if ( is_numeric($Params['ID']) && $Album = PhotoAlbum::get()->byID((int)$Params['ID']) ) {  
         return $Album;
      }  
   }
    
   public function album() {      
      if($PhotoAlbum = $this->getAlbum()) {
         $Data = array('PhotoAlbum' => $PhotoAlbum);
         return $this->Customise($Data);
      }
      else {
         return $this->httpError(404, 'Sorry that photo album could not be found');
      }
   }
   
   public function PhotoAlbums() {
      $PhotoAlbums = PhotoAlbum::get()->filter('PhotoGalleryID',$this->ID)->exclude('PhotoID','0');
		$PhotoAlbumSet = new ArrayList();
		if($PhotoAlbums->exists()) {
   		foreach($PhotoAlbums as $PhotoAlbum) {
   		   if($PhotoAlbum->getComponents('PhotoItems')->exists())
      		   $PhotoAlbumSet -> push($PhotoAlbum); 
   		}
		}
		return $PhotoAlbumSet;
	}
	
	public function PaginatedAlbums() {
      $paginatedalbums = new PaginatedList($this->PhotoAlbums(), $this->request);
      $paginatedalbums->setPageLength($this->AlbumsPerPage);
      return $paginatedalbums;
   }
   
   public function Photos() {
      return PhotoItem::get()->filter('PhotoAlbumID',$this->getAlbum()->ID);
   }
   
   public function PaginatedPhotos() {
      $paginatedphotos = new PaginatedList($this->Photos(), $this->request);
      $paginatedphotos->setPageLength($this->PhotosPerPage);
      return $paginatedphotos;
   }
   
   public function OtherAlbums() {
      $OtherAlbums = PhotoAlbum::get()->exclude('ID',$this->getAlbum()->ID)->limit('10');
      $OtherAlbumSet = new ArrayList();
		if($OtherAlbums->exists()) {
   		foreach($OtherAlbums as $OtherAlbum) {
   		   if($OtherAlbum->getComponents('PhotoItems')->exists())
      		   $OtherAlbumSet -> push($OtherAlbum); 
   		}
		}
		return $OtherAlbumSet;
   }
 
}

?>
