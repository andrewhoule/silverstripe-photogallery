<?php

class PhotoGallery extends Page {
 
   private static $db = array(
      "AlbumsPerPage" => "Int",
      "PhotosPerPage" => "Int"
   );
   
   private static $has_many = array (
      "PhotoAlbums" => "PhotoAlbum",
      "PhotoItems" => "PhotoItem"
   );
   
   public static $defaults = array(
      "AlbumsPerPage" => "6",
      "PhotosPerPage" => "20"
   );
   
   private static $icon = "photogallery/images/photogallery";
   
   function getCMSFields() {
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
      $PhotosGridField = new GridField(
         "PhotoItems",
         "Photo",
         $this->PhotoItems(),
         GridFieldConfig::create()
            ->addComponent(new GridFieldToolbarHeader())
            ->addComponent(new GridFieldAddNewButton('toolbar-header-right'))
            ->addComponent(new GridFieldSortableHeader())
            ->addComponent(new GridFieldDataColumns())
            ->addComponent(new GridFieldPaginator(50))
            ->addComponent(new GridFieldEditButton())
            ->addComponent(new GridFieldDeleteAction())
            ->addComponent(new GridFieldDetailForm())
            ->addComponent(new GridFieldBulkManager())
            ->addComponent(new GridFieldBulkImageUpload())
            ->addComponent(new GridFieldSortableRows('SortID'))
      );
      $fields->addFieldToTab("Root.Photos", $PhotosGridField);
      $fields->addFieldToTab("Root.Config", TextField::create("AlbumsPerPage")->setTitle("Number of Albums Per Page"));
      $fields->addFieldToTab("Root.Config", TextField::create("PhotosPerPage")->setTitle("Number of Photos Per Page"));
      return $fields;
   }
 
}
 
class PhotoGallery_Controller extends Page_Controller {

   function init() {
      parent::init();
      Requirements::CSS("photogallery/prettyPhoto/css/prettyPhoto.css");
      Requirements::CSS("photogallery/css/photogallery.css");
   }
   
   static $allowed_actions = array(
      'album'
   ); 
    
   public function getAlbum() {
      $Params = $this->getURLParams();
      if ( is_numeric($Params["ID"]) && $Album = PhotoAlbum::get()->byID((int)$Params["ID"]) ) {  
         return $Album;
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
   
   public function PhotoAlbums() {
      $PhotoAlbums = PhotoAlbum::get()->filter("PhotoGalleryID",$this->ID)->exclude("PhotoID","0");
      $PhotoAlbumSet = new ArrayList();
      if($PhotoAlbums->exists()) {
         foreach($PhotoAlbums as $PhotoAlbum) {
            if($PhotoAlbum->getComponents("PhotoItems")->exists() AND $PhotoAlbum->getComponent("PhotoGallery")->exists())
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

?>
