<?php

class PhotoItem extends DataObject { 
	
	private static $db = array (
	   	"SortID" => "Int",
		"Caption" => "Text"
	);
	
	private static $has_one = array (
		"PhotoGallery" => "PhotoGallery",
		"PhotoAlbum" => "PhotoAlbum",
		"Photo" => "Image"
	);

	private static $summary_fields = array (
      	"Thumbnail" => "Photo",
      	"CaptionExcerpt" => "Caption"
   	);

	public function canCreate($Member = null) { return true; }
	public function canEdit($Member = null) { return true; }
	public function canView($Member = null) { return true; }
	public function canDelete($Member = null) { return true; }
	
	private static $default_sort = "SortID Asc";
	private static $singular_name = "Photo";
	private static $plural_name = "Photos";
	
	public function getCMSFields() {
		if($this->ID == 0) {
			$albumsdropdown = TextField::create('AlbumDisclaimer')->setTitle('Album')->setDisabled(true)->setValue('You can assign an album once you have saved the record for the first time.');
		}
		else {
			$albums = PhotoAlbum::get()->sort("Name ASC");
			$map = $albums ? $albums->map("ID", "Name", "Please Select") : array();
			if($map) {
				$albumsdropdown = new DropdownField("PhotoAlbumID","Photo Album", $map);
				$albumsdropdown->setEmptyString("-- Please Select --");
			}
			else {
				$albumsdropdown = new DropdownField("PhotoAlbumID","Photo Album", $map);
				$albumsdropdown->setEmptyString("There are no photo albums created yet"); 
			}
		}
		$imgfield = UploadField::create("Photo");
		$imgfield->folderName = "PhotoGallery"; 
      	$imgfield->getValidator()->allowedExtensions = array("jpg","jpeg","gif","png");
		return new FieldList(
			$albumsdropdown,
		   	$imgfield,
			TextField::create("Caption")
		);
	}
	
	public function Thumbnail() {
		$Image = $this->Photo();
		if ($Image) 
			return $Image->CMSThumbnail();
		else 
			return null;
	}
	
	public function CaptionExcerpt($length = 75) {
		$text = strip_tags($this->Caption);
		$length = abs((int)$length);
		if(strlen($text) > $length) {
			$text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1 ...', $text);
		}
		return $text;
	}
	
	public function getAlbums() {
		$albums = PhotoAlbum::get()->sort('Created DESC');
		if($albums->Exists()) {
		 	return $albums->map("ID", "Name", "Please Select");
		}
		else { 
			return array("No albums found");
		}
	}
	
	public function PhotoCropped($x=120,$y=120) {
		return $this->Photo()->CroppedImage($x,$y);
	}
	
	public function PhotoSized($x=700,$y=700) {
		return $this->Photo()->SetRatioSize($x,$y);
	}

	public function AlbumTitle() {
		if($this->getComponent("PhotoAlbum")->exists())
			return $this->getComponent("PhotoAlbum")->Name;
	}

	public function getTitle() {
    	return $this->Caption;
    }
	
}

?>