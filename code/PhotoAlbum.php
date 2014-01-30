<?php

class PhotoAlbum extends DataObject { 
	
	private static $db = array (
	   	"SortID" => "Int",
		"Name" => "Text",
		"Description" => "HTMLText"
	);
	
	private static $has_one = array (
		"PhotoGallery" => "PhotoGallery",
		"Photo" => "Image"
	);
	
	private static $has_many = array (
		"PhotoItems" => "PhotoItem"
	);
	
	private static $summary_fields = array (
		"Thumbnail" => "Cover Photo",
		"Name" => "Name",
		"DescriptionExcerpt" => "Description"
	);

	function canCreate($Member = null) { return true; }
	function canEdit($Member = null) { return true; }
	function canView($Member = null) { return true; }
	function canDelete($Member = null) { return true; }
   
   	private static $default_sort = "SortID Asc";
   
	public function getCMSFields() {
		// $PhotosGridField = new GridField(
  //           "Photos",
  //           "Photo",
  //           $this->PhotoItems(),
  //           GridFieldConfig::create()
  //               ->addComponent(new GridFieldToolbarHeader())
  //               ->addComponent(new GridFieldAddNewButton("toolbar-header-right"))
  //               ->addComponent(new GridFieldSortableHeader())
  //               ->addComponent(new GridFieldDataColumns())
  //               ->addComponent(new GridFieldPaginator(50))
  //               ->addComponent(new GridFieldEditButton())
  //               ->addComponent(new GridFieldDeleteAction())
  //               ->addComponent(new GridFieldDetailForm())
  //               ->addComponent(new GridFieldBulkManager())
  //               ->addComponent(new GridFieldBulkImageUpload())
  //               ->addComponent(new GridFieldSortableRows("SortID"))
  //       );
        $ImageField = UploadField::create("Photo")->setTitle("Gallery Cover Photo");
    	$ImageField->folderName = "PhotoGallery"; 
      	$ImageField->getValidator()->allowedExtensions = array("jpg","jpeg","gif","png");
	  	return new FieldList(
			TextField::create("Name"),
			TextareaField::create("Description"),
			$ImageField
			// $PhotosGridField
		);
	}
	
	public function Thumbnail() {
		$Image = $this->Photo();
		if ($Image) 
			return $Image->CMSThumbnail();
		else 
			return null;
	}
	
	public function DescriptionExcerpt($length=75) {
	   	$text = strip_tags($this->Description);
	   	$length = abs((int)$length);
	   	if(strlen($text) > $length) {
	   		$text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
	   	}
	   		return $text;
   	}
	
	public function PhotoCropped($x=120,$y=120) {
		if($this->Photo()->exists())
		 	return $this->Photo()->CroppedImage($x,$y);
		else {
			if($this->PhotoGallery()->DefaultAlbumCover()->exists()) {
				return $this->PhotoGallery()->DefaultAlbumCover()->CroppedImage($x,$y);
			}
		}
	}
	
	public function Link() {
        if($PhotoGallery = $this->PhotoGallery()) {
            $Action = "album/" . $this->ID;
            return $PhotoGallery->Link($Action);   
        }
    }

    public function PhotoCount() {
    	return $this->getComponents("PhotoItems")->count();
    }

    public function getTitle() {
    	return $this->Name;
    }
	
}

?>