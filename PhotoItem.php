<?php

class PhotoItem extends DataObject { 
	
	static $db = array (
		"Caption" => "Text"
	);
	
	static $has_one = array (
		"PhotoGallery" => "PhotoGallery",
		"PhotoAlbum" => "PhotoAlbum",
		"Photo" => "Image"
	);
	
	public function getCMSFields() {
		$imagefield = new ImageUploadField('Photo');
		
		$albums = DataObject::get("PhotoAlbum","PhotoGalleryID=" . $this->getComponent('PhotoGallery')->ID,"Created DESC");
		$map = $albums ? $albums->toDropDownMap('ID', 'Name') : array();
		if($map) {
			$albums_dd = new DropdownField('PhotoAlbumID','Photo Album', $map);
		}
		else {
			$albums_dd = new DropdownField('PhotoAlbumID','Photo Album', $map);
			$albums_dd->setEmptyString("There are no photo albums created yet"); 
		}
		
		return new FieldSet(
			$albums_dd,
			$imagefield,
			new TextField('Caption')
		);
	}
	
	public function Thumbnail() {
		$Image = $this->Photo();
		if ( $Image ) 
			return $Image->CMSThumbnail();
		else 
			return null;
	}
	
	public function getPhotoAlbum() {
		return $this->getComponent('PhotoAlbum')->Name;	
	}
	
	public function PhotoCropped($x=120,$y=120) {
		 return $this->Photo()->CroppedImage($x,$y);
	}
	
	public function PhotoSized($x=700,$y=700) {
		 return $this->Photo()->SetRatioSize($x,$y);
	}
	
	public function IteratorPos($val=5){
		return ($this->iteratorPos + 1) % $val == 0;
	}
	
}

?>