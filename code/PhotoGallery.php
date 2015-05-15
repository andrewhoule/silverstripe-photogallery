<?php

class PhotoGallery extends Page {
 
  private static $db = array(
    'AlbumsPerPage' => 'Int',
    'AlbumThumbnailHeight' => 'Int',
    'AlbumThumbnailWidth' => 'Int',
    'AlbumDefaultTop' => 'Boolean',
    'ShowAllPhotoAlbums' => 'Boolean',
    'PhotosPerPage' => 'Int',
    'PhotoThumbnailHeight' => 'Int',
    'PhotoThumbnailWidth' => 'Int',
    'PhotoFullHeight' => 'Int',
    'PhotoFullWidth' => 'Int',
    'PhotoDefaultTop' => 'Boolean'
  );
   
  private static $has_one = array (
    'DefaultAlbumCover' => 'Image'
  );

  private static $has_many = array (
    'PhotoAlbums' => 'PhotoAlbum',
    'PhotoItems' => 'PhotoItem'
  );
   
  private static $defaults = array(
    'AlbumsPerPage' => '6',
    'PhotosPerPage' => '20',
    'ShowAllPhotoAlbums' => true,
    'AlbumThumbnailWidth' => '200',
    'AlbumThumbnailHeight' => '200',
    'AlbumDefaultTop' => true,
    'PhotoThumbnailWidth' => '150',
    'PhotoThumbnailHeight' => '150',
    'PhotoFullWidth' => '700',
    'PhotoFullHeight' => '700',
    'PhotoDefaultTop' => true
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
        ->addComponent(new GridFieldFilterHeader())
        ->addComponent($sortable = new GridFieldSortableRows('SortID'))
    );
    if($this->AlbumDefaultTop == true) {
      $sortable->setAppendToTop(true);
    }
    $fields->addFieldToTab("Root.Albums", $AlbumsGridField);
    $fields->addFieldToTab("Root.Config", HeaderField::create("Album Settings"));
    $fields->addFieldToTab("Root.Config", $DefaultAlbumCoverField);
    $fields->addFieldToTab("Root.Config", SliderField::create('AlbumsPerPage','Number of Albums Per Page',1,25));
    $fields->addFieldToTab("Root.Config", SliderField::create("AlbumThumbnailWidth","Album Cover Thumbnail Width",50,400));
    $fields->addFieldToTab("Root.Config", SliderField::create("AlbumThumbnailHeight","Album Cover Thumbnail Height",50,400));
    $fields->addFieldToTab("Root.Config", CheckboxField::create("ShowAllPhotoAlbums")->setTitle("Show photo album even if it's empty"));
    $fields->addFieldToTab("Root.Config", CheckboxField::create("AlbumDefaultTop")->setTitle("Sort new albums to the top by default"));
    $fields->addFieldToTab("Root.Config", HeaderField::create("Photo Settings"));
    $fields->addFieldToTab("Root.Config", SliderField::create("PhotosPerPage","Number of Photos Per Page",1,50));
    $fields->addFieldToTab("Root.Config", SliderField::create("PhotoThumbnailWidth","Photo Thumbnail Width",50,400));
    $fields->addFieldToTab("Root.Config", SliderField::create("PhotoThumbnailHeight","Photo Thumbnail Height",50,400));
    $fields->addFieldToTab("Root.Config", SliderField::create("PhotoFullWidth","Photo Fullsize Width",400,1200));
    $fields->addFieldToTab("Root.Config", SliderField::create("PhotoFullHeight","Photo Fullsize Height",400,1200));
    $fields->addFieldToTab("Root.Config", CheckboxField::create("PhotoDefaultTop")->setTitle("Sort new photos to the top by default"));
    return $fields;
  }
 
}
 
class PhotoGallery_Controller extends Page_Controller {

  public static function load_requirements() {
    Requirements::CSS("photogallery/shadowbox/shadowbox.css");
    Requirements::CSS("photogallery/css/photogallery.css");
    Requirements::javascript(FRAMEWORK_DIR .'/thirdparty/jquery/jquery.js');
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
    if($this->AlbumsPerPage > 0) {
      $paginatedalbums->setPageLength($this->AlbumsPerPage);
    }
    else {
      $paginatedalbums->setPageLength('6');
    }
    return $paginatedalbums;
  }

  public function AlbumCount() {
    return $this->PhotoAlbums()->count();
  }

  public function Photos() {
    return $this->getAlbum()->Photos();
  }
   
  public function PaginatedPhotos() {
    $paginatedphotos = new PaginatedList($this->Photos(), $this->request);
    if($this->PhotosPerPage > 0) {
      $paginatedphotos->setPageLength($this->PhotosPerPage);
    }
    else {
      $paginatedphotos->setPageLength('20');
    }
    return $paginatedphotos;
  }
   
  public function OtherAlbums() {
    $OtherAlbums = PhotoAlbum::get()->exclude("ID",$this->getAlbum()->ID)->filter("PhotoGalleryID",$this->ID)->limit("10");
    $OtherAlbumSet = new ArrayList();
    if($OtherAlbums->exists()) {
      foreach($OtherAlbums as $OtherAlbum) {
        if($OtherAlbum->getComponents("PhotoItems")->exists() AND $OtherAlbum->getComponent("PhotoGallery")->exists() AND $OtherAlbum->getComponent("AlbumCover")->exists())
        $OtherAlbumSet -> push($OtherAlbum); 
      }
    }
    return $OtherAlbumSet;
  }
 
}