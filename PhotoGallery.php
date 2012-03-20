<?php

class PhotoGallery extends Page {
 
 	static $db = array(
		
	);
 	
    static $has_many = array (
		"PhotoAlbums" => "PhotoAlbum",
		"PhotoItems" => "PhotoItem"
	);
	
	static $defaults = array(
		
	);
	
	//static $icon = "mysite/images/treeicons/photogallery";
	
	function getCMSFields() {
      	$fields = parent::getCMSFields();
      	$albums = DataObject::get("PhotoAlbum","PhotoGalleryID=$this->ID","Name ASC");
		$AlbumManager = new DataObjectManager(
			$this, // Controller
			'PhotoAlbums', // Source name
			'PhotoAlbum', // Source class
			array('Name' => 'Name','Description' => 'Description', 'Thumbnail' => 'Photo'), // Headings
			'getCMSFields_forPopup' // Detail fields function or FieldSet
		);
		$fields->addFieldToTab("Root.Content.Albums", $AlbumManager);
		$PhotosManager = new FileDataObjectManager(
			$this, // Controller
			'PhotoItems', // Source name
			'PhotoItem', // Source class
			'Photo',
			array('Caption' => 'Caption','Thumbnail' => 'Photo','getPhotoAlbum' => 'Photo Album'), // Headings
			'getCMSFields_forPopup' // Detail fields function or FieldSet
		);
		$PhotosManager->setAddTitle('Photo');
		$PhotosManager->setUploadFolder('photo_gallery');
		$PhotosManager->setDefaultView('list');
		$PhotosManager->setAllowedFileTypes(array('jpg','jpeg','png','gif'));
		$PhotosManager->setUploadLimit('20');
		$PhotosManager->setPerPageMap(array(30,60,90)); 
		if($albums) {
   			$PhotosManager->setFilter(
				'PhotoAlbumID', // Name of field to filter
				'Filter by Album', // Label for filter
				$albums->toDropdownMap('ID', 'Name')
			); 
		}
		$fields->addFieldToTab("Root.Content.Photos", $PhotosManager);
		return $fields;
   	}
   	
   	public function RecentPhotoItems($limit=12) {
		return $this->getComponents("PhotoItems",null,null,null,$limit);
	}
 
}
 
class PhotoGallery_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::CSS('photo_gallery/prettyPhoto/css/prettyPhoto.css');
		Requirements::CSS('photo_gallery/css/photogallery.css');
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript('photo_gallery/prettyPhoto/js/jquery.prettyPhoto.js');
		Requirements::javascript('photo_gallery/js/prettyPhoto_init.js');
	}

	static $allowed_actions = array(
        'album'
    );
     
    public function getAlbum() {
        $Params = $this->getURLParams();
        if(is_numeric($Params['ID']) && $Album = DataObject::get_by_id('PhotoAlbum', (int)$Params['ID'])) {      
            return $Album;
        }
    }
     
    function album() {      
        if($PhotoAlbum = $this->getAlbum()) {
            $Data = array('PhotoAlbum' => $PhotoAlbum);
            return $this->Customise($Data);
        }
        else {
           	return $this->httpError(404, 'Sorry that photo album could not be found');
        }
    }
    
    public function Breadcrumbs() {
        $Breadcrumbs = parent::Breadcrumbs();
        if($PhotoAlbum = $this->getAlbum()) {
            $Parts = explode(SiteTree::$breadcrumbs_delimiter, $Breadcrumbs);
     		$NumOfParts = count($Parts);
            $Parts[$NumOfParts-1] = ('<a href="' . $this->Link() . '">' . $Parts[$NumOfParts-1] . '</a>');
            $Parts[$NumOfParts] = $PhotoAlbum->Name;
     		$Breadcrumbs = implode(SiteTree::$breadcrumbs_delimiter, $Parts);          
        }
 		return $Breadcrumbs;
    }   
    
    public function MenuTitle() {
    	$MenuTitle = $this->MenuTitle;
    	if($PhotoAlbum = $this->getAlbum()) {
            $MenuTitle = $this->MenuTitle . ' – ' . $PhotoAlbum->Name;          
        }
     	return $MenuTitle;
   	}
   	
   	public function PhotoAlbums() {
		if(!isset($_GET['start']) || !is_numeric($_GET['start']) || (int)$_GET['start'] < 1) $_GET['start'] = 0;
     		$SQL_start = (int)$_GET['start'];
			return DataObject::get("PhotoAlbum", "PhotoGalleryID=$this->ID AND PhotoID != 0", null, null, $_GET['start'] . ",9");
	}
 
}

?>