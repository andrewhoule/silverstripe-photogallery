<?php

class PhotoAlbum extends DataObject { 
	
	static $db = array (
		"Name" => "Text",
		"Description" => "Text"
	);
	
	static $has_one = array (
		"PhotoGallery" => "PhotoGallery",
		"Photo" => "Image"
	);
	
	static $has_many = array (
		"PhotoItems" => "PhotoItem"
	);
	
	public function getCMSFields() {
		$imagefield = new ImageUploadField('Photo');
		$imagefield->removeFolderSelection();
		$imagefield->setUploadFolder('photo_gallery');
		
		return new FieldSet(
			new TextField('Name'),
			new TextField('Description'),
			$imagefield
		);
	}
	
	public function Thumbnail() {
		$Image = $this->Photo();
		if ( $Image ) 
			return $Image->CMSThumbnail();
		else 
			return null;
	}
	
	public function PhotoCropped($x=120,$y=120) {
		 return $this->Photo()->CroppedImage($x,$y);
	}
	
	public function Link() {
        if($PhotoGallery = $this->PhotoGallery()) {
            $Action = 'album/' . $this->ID;
            return $PhotoGallery->Link($Action);   
        }
    }
    
    public function Photos() {
		if(!isset($_GET['start']) || !is_numeric($_GET['start']) || (int)$_GET['start'] < 1) $_GET['start'] = 0;
     		$SQL_start = (int)$_GET['start'];
			return DataObject::get("PhotoItem", "PhotoAlbumID=$this->ID", null, null, $_GET['start'] . ",20");
	}
	
	public function OtherAlbums() {
		return DataObject::get("PhotoAlbum","ID != $this->ID");
	}
	
	public function IteratorPos($val=3){
		return ($this->iteratorPos + 1) % $val == 0;
	}
	
}

?>