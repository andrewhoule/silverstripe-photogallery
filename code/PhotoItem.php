<?php
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;

class PhotoItem extends DataObject
{
  
    private static $db = array(
    'SortID' => Int::class,
    'Caption' => 'Text'
  );
  
    private static $has_one = array(
    'Photo' => Image::class,
    'PhotoGallery' => PhotoGallery::class,
    'PhotoAlbum' => PhotoAlbum::class
  );

    private static $summary_fields = array(
    'Thumbnail' => 'Photo',
    'CaptionExcerpt' => 'Caption'
  );

    public function canCreate($Member = null, $context = array())
    {
        return true;
    }
    public function canEdit($Member = null)
    {
        return true;
    }
    public function canView($Member = null)
    {
        return true;
    }
    public function canDelete($Member = null)
    {
        return true;
    }
  
    private static $default_sort = 'SortID ASC';
    private static $singular_name = 'Photo';
    private static $plural_name = 'Photos';
  
    public function getCMSFields()
    {
        if ($this->ID == 0) {
            $albumsdropdown = TextField::create('AlbumDisclaimer')->setTitle('Album')->setDisabled(true)->setValue('You can assign an album once you have saved the record for the first time.');
        } else {
            $albums = PhotoAlbum::get()->sort("Name ASC");
            $map = $albums ? $albums->map("ID", "Name", "Please Select") : array();
            if ($map) {
                $albumsdropdown = new DropdownField("PhotoAlbumID", "Photo Album", $map);
                $albumsdropdown->setEmptyString("-- Please Select --");
            } else {
                $albumsdropdown = new DropdownField("PhotoAlbumID", "Photo Album", $map);
                $albumsdropdown->setEmptyString("There are no photo albums created yet");
            }
        }
        $imgfield = UploadField::create("Photo");
        $imgfield->folderName = "PhotoGallery";
        $imgfield->getValidator()->allowedExtensions = array("jpg","jpeg","gif","png");
        $captionfield = TextField::create("Caption");
        $captionfield->setMaxLength("75");
        $Fields = new FieldList(
        $albumsdropdown,
        $imgfield,
        $captionfield
      );
        $this->extend('updateCMSFields', $Fields);
        return $Fields;
    }
  
    public function Thumbnail()
    {
        $extThumb = $this->extend('Thumbnail');
        if ($extThumb && count($extThumb)) {
            return end($extThumb);
        }
        $Image = $this->Photo();
        if ($Image) {
            return $Image->CMSThumbnail();
        } else {
            return null;
        }
    }
  
    public function CaptionExcerpt($length = 75)
    {
        $text = strip_tags($this->Caption);
        $length = abs((int)$length);
        if (strlen($text) > $length) {
            $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1 ...', $text);
        }
        return $text;
    }
  
    public function getAlbums()
    {
        $albums = PhotoAlbum::get()->sort('Created DESC');
        if ($albums->Exists()) {
            return $albums->map("ID", "Name", "Please Select");
        } else {
            return array("No albums found");
        }
    }
  
    public function PhotoCropped($x=120, $y=120)
    {
        $width = $this->getComponent('PhotoAlbum')->PhotoGallery()->PhotoThumbnailWidth;
        $height = $this->getComponent('PhotoAlbum')->PhotoGallery()->PhotoThumbnailHeight;
        if ($width != 0) {
            $x = $width;
        }
        if ($height != 0) {
            $y = $height;
        }
        return $this->Photo()->Fill($x, $y);
    }
    public function PhotoHeight($y=700)
    {
        $height = $this->PhotoGallery()->PhotoThumbnailHeight;
        if ($height != 0) {
            $y = $height;
        }
        return $this->Photo()->ScaleHeight($y);
    }

  
    public function PhotoSized($x=700, $y=700)
    {
        $width = $this->getComponent('PhotoAlbum')->PhotoGallery()->PhotoFullWidth;
        $height = $this->getComponent('PhotoAlbum')->PhotoGallery()->PhotoFullHeight;
        if ($width != 0) {
            $x = $width;
        }
        if ($height != 0) {
            $y = $height;
        }
        return $this->Photo()->Fit($x, $y);
    }

    public function AlbumTitle()
    {
        if ($this->getComponent("PhotoAlbum")->exists()) {
            return $this->getComponent("PhotoAlbum")->Name;
        }
    }

    public function getTitle()
    {
        return $this->Caption;
    }

    public function OnBeforeDelete()
    {
        $photo = $this->Photo();
        $file = Image::get()->byID($photo->ID);
        if ($file) {
            $file->delete();
        }
        return parent::OnBeforeDelete();
    }
}
