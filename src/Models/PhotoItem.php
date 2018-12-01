<?php

namespace AndrewHoule\PhotoGallery\Models;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\TabSet;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Versioned\Versioned;
use SilverStripe\AssetAdmin\Forms\UploadField;
use AndrewHoule\PhotoGallery\Models\PhotoAlbum;
use AndrewHoule\PhotoGallery\Pages\PhotoGallery;
use AndrewHoule\PhotoGallery\Extensions\Sortable;
use AndrewHoule\PhotoGallery\Traits\CMSPermissionProvider;

class PhotoItem extends DataObject
{

    use CMSPermissionProvider;

    private static $db = [
        'Caption' => 'Text',
    ];

    private static $has_one = [
        'Photo' => Image::class,
        'PhotoGallery' => PhotoGallery::class,
        'PhotoAlbum' => PhotoAlbum::class,
    ];

    private static $owns = [
        'Photo',
    ];

    private static $cascade_deletes = [
        'Photo',
    ];

    private static $summary_fields = [
        'Thumbnail' => 'Photo',
        'CaptionExcerpt' => 'Caption',
    ];

    private static $extensions = [
        Versioned::class,
        Sortable::class,
    ];

    private static $table_name = 'PhotoItem';

    private static $singular_name = 'Photo';

    private static $plural_name = 'Photos';

    public function getCMSFields()
    {
    $fields = parent::getCMSFields();
        $fields = FieldList::create(TabSet::create('Root'));

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Caption')
                ->setMaxLength(75)
                ->setDescription('75 characters max'),
            UploadField::create('Photo')
                ->setDescription('jpg, gif and png filetypes allowed.')
                ->setFolderName($this->PhotoAlbum()->AlbumFolderName())
                ->setAllowedExtensions([
                    'jpg',
                    'jpeg',
                    'png',
                    'gif'
                ])
        ]);

        return $fields;
    }

    public function Thumbnail()
    {
        $image = $this->Photo();
        return ($image) ? $image->CMSThumbnail() : false;
    }

    public function getTitle() {
        return $this->Caption;
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
            return $albums->map('ID', 'Name', 'Please Select');
        } else {
            return array('No albums found');
        }
    }

    public function AlbumTitle()
    {
        if ($this->PhotoAlbum()->exists()) {
            return $this->PhotoAlbum()->Name;
        }
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
