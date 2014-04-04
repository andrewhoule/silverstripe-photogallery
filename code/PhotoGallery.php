<?php

class PhotoGallery extends Page {
 
    private static $db = array(
        "AlbumsPerPage" => "Int",
        "PhotosPerPage" => "Int",
        "ShowAllPhotoAlbums" => "Boolean"
    );
   
    private static $has_one = array (
        "DefaultAlbumCover" => "Image"
    );

    private static $has_many = array (
        "PhotoAlbums" => "PhotoAlbum",
        "PhotoItems" => "PhotoItem"
    );
   
    private static $defaults = array(
        "AlbumsPerPage" => "6",
        "PhotosPerPage" => "20",
        "ShowAllPhotoAlbums" => true
    );
   
    private static $icon = "photogallery/images/photogallery";
   
    public function getCMSFields() {
        $DefaultAlbumCoverField = UploadField::create('DefaultAlbumCover');
        $DefaultAlbumCoverField->folderName = "PhotoGallery"; 
        $DefaultAlbumCoverField->getValidator()->allowedExtensions = array('jpg','jpeg','gif','png');
        $fields = parent::getCMSFields();
        $AlbumsGridField = new GridField(
            "PhotoAlbums",
            "Album",
            $this->PhotoAlbums(),
            GridFieldConfig::create()
                ->addComponent(new GridFieldToolbarHeader())
                ->addComponent(new GridFieldAddNewButton('toolbar-header-right'))
                ->addComponent(new GridFieldSortableHeader())
                ->addComponent(new GridFieldDataColumns())
                ->addComponent(new GridFieldPaginator(50))
                ->addComponent(new GridFieldEditButton())
                ->addComponent(new GridFieldDeleteAction())
                ->addComponent(new GridFieldDetailForm())
                ->addComponent(new GridFieldSortableRows('SortID'))
        );
        $fields->addFieldToTab("Root.Albums", $AlbumsGridField);
        $fields->addFieldToTab("Root.Config", TextField::create("AlbumsPerPage")->setTitle("Number of Albums Per Page"));
        $fields->addFieldToTab("Root.Config", TextField::create("PhotosPerPage")->setTitle("Number of Photos Per Page"));
        $fields->addFieldToTab("Root.Config", $DefaultAlbumCoverField);
        $fields->addFieldToTab("Root.Config", CheckboxField::create("ShowAllPhotoAlbums")->setTitle("Show photo album even if it's empty"));
        return $fields;
    }
 
}
 
class PhotoGallery_Controller extends Page_Controller {

   public static function load_requirements() {
        // Requirements::CSS("photogallery/prettyPhoto/css/prettyPhoto.css");
        // Requirements::CSS("photogallery/css/photogallery.css");
        // Requirements::javascript("http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js");
        // Requirements::javascript("photogallery/prettyPhoto/js/jquery.prettyPhoto.js");
        // Requirements::javascript("photogallery/js/prettyPhoto_init.js");

        Requirements::CSS("photogallery/shadowbox/shadowbox.css");
        Requirements::CSS("photogallery/css/photogallery.css");
        Requirements::javascript("http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js");
        Requirements::javascript("photogallery/shadowbox/shadowbox.js");
        Requirements::javascript("photogallery/js/shadowbox_init.js");
    }

    public function init() {
        parent::init();
        self::load_requirements();
    }
   
    private static $allowed_actions = array(
        'album'
    ); 
    
    public function getAlbum() {
        $Params = $this->getURLParams();
        if ( is_numeric($Params["ID"]) && $Album = PhotoAlbum::get()->byID((int)$Params["ID"]) ) {  
            return $Album;
        }  
        else {
            return $this->PhotoAlbums()->first();
        }
    }
    
    public function album() {      
        if($PhotoAlbum = $this->getAlbum()) {
            $Data = array("PhotoAlbum" => $PhotoAlbum);
            return $this->Customise($Data);
        }
        else {
            return $this->httpError(404, "Sorry that photo album could not be found");
        }
    }

    public function AllPhotoAlbums() {
        return PhotoAlbum::get()->filter("PhotoGalleryID",$this->ID);
    }

    public function PopulatedPhotoAlbums() {
        $PhotoAlbums = PhotoAlbum::get()->filter("PhotoGalleryID",$this->ID);
        $PhotoAlbumSet = new ArrayList();
        if($PhotoAlbums->exists()) {
            foreach($PhotoAlbums as $PhotoAlbum) {
                if($PhotoAlbum->getComponents("PhotoItems")->exists() AND $PhotoAlbum->getComponent("PhotoGallery")->exists())
               $PhotoAlbumSet -> push($PhotoAlbum); 
            }
        }
        return $PhotoAlbumSet;
    }

    public function PhotoAlbums() { 
        if($this->ShowAllPhotoAlbums){
            return $this->AllPhotoAlbums();
        }
        else {
            return $this->PopulatedPhotoAlbums();
        }
    }
   
    public function PaginatedAlbums() {
        $paginatedalbums = new PaginatedList($this->PhotoAlbums(), $this->request);
        $paginatedalbums->setPageLength($this->AlbumsPerPage);
        return $paginatedalbums;
    }

    public function AlbumCount() {
        return $this->PhotoAlbums()->count();
    }

    public function Photos() {
        $photoset = new ArrayList();
        $photos = PhotoItem::get()->filter("PhotoAlbumID",$this->getAlbum()->ID);
        if($photos) {
            foreach($photos AS $photo) {
                if($photo->getComponent("Photo")->exists()) {
                    $photoset->push($photo);
                }
            }
        }
        return $photoset;
    }
   
    public function PaginatedPhotos() {
        $paginatedphotos = new PaginatedList($this->Photos(), $this->request);
        $paginatedphotos->setPageLength($this->PhotosPerPage);
        return $paginatedphotos;
    }
   
    public function OtherAlbums() {
        $OtherAlbums = PhotoAlbum::get()->exclude("ID",$this->getAlbum()->ID)->filter("PhotoGalleryID",$this->ID)->limit("10");
        $OtherAlbumSet = new ArrayList();
        if($OtherAlbums->exists()) {
            foreach($OtherAlbums as $OtherAlbum) {
                if($OtherAlbum->getComponents("PhotoItems")->exists() AND $OtherAlbum->getComponent("PhotoGallery")->exists() AND $OtherAlbum->getComponent("Photo")->exists())
                $OtherAlbumSet -> push($OtherAlbum); 
            }
        }
        return $OtherAlbumSet;
    }
 
}