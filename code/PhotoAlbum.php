<?php

class PhotoAlbum extends DataObject {

  private static $db = array (
    'SortID' => 'Int',
    'Name' => 'Text',
    'Description' => 'HTMLText'
  );

  private static $has_one = array (
    'PhotoGallery' => 'PhotoGallery',
    'AlbumCover' => 'Image'
  );

  private static $has_many = array (
    'PhotoItems' => 'PhotoItem'
  );

  private static $summary_fields = array (
    'Thumbnail' => 'Cover Photo',
    'Name' => 'Name',
    'DescriptionExcerpt' => 'Description'
  );

  public function canCreate($Member = null) { return true; }
  public function canEdit($Member = null) { return true; }
  public function canView($Member = null) { return true; }
  public function canDelete($Member = null) { return true; }

  private static $default_sort = 'SortID ASC';

  public function PageFolder() {
    if($name = $this->getComponent('PhotoGallery')->MenuTitle) {
      $string = strtolower($name);
      $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
      $string = preg_replace("/[\s-]+/", " ", $string);
      $string = preg_replace("/[\s_]/", "-", $string);
      return $string;
    }
    else {
      return "photogallery";
    }
  }

  public function AlbumFolder() {
    if($name = $this->Name) {
      $string = strtolower($name);
      $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
      $string = preg_replace("/[\s-]+/", " ", $string);
      $string = preg_replace("/[\s_]/", "-", $string);
      return $string;
    }
    else {
      return "album";
    }
  }

  public function getCMSFields() {
    if($this->ID == 0) {
      $PhotosGridField = TextField::create('PhotosDisclaimer')->setTitle('Photos')->setDisabled(true)->setValue('You can add photos once you have saved the record for the first time.');
      $ImageField = TextField::create('AlbumCoverDisclaimer')->setTitle('Album Cover Photo')->setDisabled(true)->setValue('You can add an album cover once you have saved the record for the first time.');
    }
    else {
      $BulkUploadComponent = new GridFieldBulkUpload();
      $BulkUploadComponent->setUfSetup('setFolderName',"photogallery/" . $this->PageFolder() . "/" . $this->AlbumFolder());
      $PhotosGridField = new GridField(
        'PhotoItems',
        'Photos',
        $this->PhotoItems(),
        GridFieldConfig::create()
          ->addComponent(new GridFieldToolbarHeader())
          ->addComponent(new GridFieldAddNewButton("toolbar-header-right"))
          ->addComponent(new GridFieldSortableHeader())
          ->addComponent(new GridFieldDataColumns())
          ->addComponent(new GridFieldPaginator(50))
          ->addComponent(new GridFieldEditButton())
          ->addComponent(new GridFieldDeleteAction())
          ->addComponent(new GridFieldDetailForm())
          ->addComponent(new GridFieldFilterHeader())
          ->addComponent(new GridFieldBulkManager())
          ->addComponent($BulkUploadComponent)
          ->addComponent($sortable = new GridFieldSortableRows('SortID'))
      );
      if($this->getComponent('PhotoGallery')->PhotoDefaultTop == true) {
        $sortable->setAppendToTop(true);
      }
      $ImageField = UploadField::create('AlbumCover')->setTitle('Album Cover Photo');
      $ImageField->folderName = 'photogallery/' . $this->PageFolder();
      $ImageField->getValidator()->allowedExtensions = array("jpg","jpeg","gif","png");
    }
    $Fields = new FieldList(
      TextField::create('Name'),
      TextareaField::create('Description'),
      $ImageField,
      $PhotosGridField
    );
    $this->extend('updateCMSFields',$Fields);
    return $Fields;
  }

  public function Thumbnail() {
    $Image = $this->AlbumCover();
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
    $width = $this->PhotoGallery()->AlbumThumbnailWidth;
    $height = $this->PhotoGallery()->AlbumThumbnailWidth;
    if($width != 0)
      $x = $width;
    if($height != 0)
      $y = $height;
    if($this->AlbumCover()->exists())
      return $this->AlbumCover()->CroppedImage($x,$y);
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

  public function Photos() {
    $photoset = new ArrayList();
    $this->extend('GetItems', $photoset);
    if(!$photoset->count()) {
      $photos = PhotoItem::get()->filter('PhotoAlbumID', $this->ID);
      if($photos) {
        foreach($photos AS $photo) {
          if($photo->getComponent('Photo')->exists()) {
            $photoset->push($photo);
          }
        }
      }
    }
    return $photoset;
  }

  public function PhotoCount() {
    return $this->Photos()->count();
  }

  public function getTitle() {
    return $this->Name;
  }

  public function PaginatedPhotos() {
    $paginatedphotos = new PaginatedList($this->Photos(), $this->request);
    $paginatedphotos->setPageLength($this->PhotosPerPage);
    return $paginatedphotos;
  }

  public function OnBeforeDelete(){
    $albumcover = $this->AlbumCover(); 
    $albumcoverfile = Image::get()->byID($albumcover->ID);
    if($albumcoverfile) {
      $albumcoverfile->delete();
    }
    // Delete the photo items in that album
    $photoitems = $this->getComponents('PhotoItems');
    foreach($photoitems as $photoitem) {
      $photoitemfile = Image::get()->byID($photoitem->Photo()->ID);
      if($photoitemfile) {
        $photoitemfile->delete();
      }
    }
    // Delete the album folder
    $albumfolder = $this->AlbumFolder();
    $folder = Folder::get()->filter('Name',$albumfolder)->first();
    if($folder) {
      $folder->delete();
    }
    return parent::OnBeforeDelete(); 
  }

}