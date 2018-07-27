<?php

namespace AndrewHoule\PhotoGallery\Models;

use AndrewHoule\PhotoGallery\Models\PhotoItem;
use AndrewHoule\PhotoGallery\Pages\PhotoGallery;
use AndrewHoule\PhotoGallery\Traits\CMSPermissionProvider;
// use Colymba\BulkUpload\BulkUploader;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

class PhotoAlbum extends DataObject
{

    use CMSPermissionProvider;

    private static $db = [
        'SortID' => 'Int',
        'Name' => 'Text',
        'Description' => 'HTMLText'
    ];

    private static $has_one = [
        'PhotoGallery' => PhotoGallery::class,
        'AlbumCover' => Image::class
    ];

    private static $has_many = [
        'PhotoItems' => PhotoItem::class
    ];

    private static $owns = [
        'AlbumCover',
        'PhotoItems'
    ];

    private static $summary_fields = [
        'Thumbnail' => 'Cover Photo',
        'Name' => 'Name',
        'DescriptionExcerpt' => 'Description'
    ];

    private static $extensions = [
        Versioned::class
    ];

    private static $versioned_gridfield_extensions = true;

    private static $table_name = 'PhotoAlbum';

    private static $default_sort = 'SortID ASC';

    public function AlbumFolderName()
    {
        $directoryName = 'photogallery';
        $defaultName = 'album';
        $photoGallery = $this->PhotoGallery();

        if ($photoGallery) {
            if ($name = $this->Name) {
                $string = strtolower($name);
                $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
                $string = preg_replace("/[\s-]+/", " ", $string);
                $string = preg_replace("/[\s_]/", "-", $string);
                return $directoryName . '/' . $string;
            } else {
                return $directoryName . '/' . $defaultName;
            }
        } else {
            return $directoryName . '/' . $defaultName;
        }
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields = FieldList::create(TabSet::create('Root'));

        // Content
        $fields->addFieldsToTab('Root.Main.Album', [
            TextField::create('Name')
                ->setTitle('Title'),
            TextareaField::create('Description'),
            UploadField::create('AlbumCover')
                ->setTitle('Album Cover Photo')
                ->setDescription('jpg, gif and png filetypes allowed.')
                ->setFolderName($this->AlbumFolderName())
                ->setAllowedExtensions([
                    'jpg',
                    'jpeg',
                    'png',
                    'gif'
                ])
        ]);

        // Photos
        $fields->addFieldToTab('Root.Main.Photos',
            GridField::create(
                'PhotoItems',
                'Photos',
                $this->PhotoItems(),
                GridFieldConfig_RecordEditor::create(100)
                    ->addComponent($sortablePhotos = new GridFieldSortableRows('SortID'))
                    // ->addComponent($bulkUploader = new BulkUploader())
                    // ->removeComponentsByType(GridFieldAddNewButton::class)
            )
        );

        $sortablePhotos->setUpdateVersionedStage('Live');
        if ($this->PhotoGallery()->PhotoDefaultTop == true) {
            $sortablePhotos->setAppendToTop(true);
        }
        // $bulkUploader->setUfSetup('setFolderName', $this->AlbumFolderName());

        return $fields;
    }

    public function Thumbnail()
    {
        $image = $this->AlbumCover();
        return ($image) ? $image->CMSThumbnail() : false;
    }

    public function DescriptionExcerpt($length = 75)
    {
        $text = strip_tags($this->Description);
        $length = abs((int)$length);
        if (strlen($text) > $length) {
            $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
        }
        return $text;
    }

    public function Link()
    {
        if ($PhotoGallery = $this->PhotoGallery()) {
            $Action = 'album/' . $this->ID;
            return $PhotoGallery->Link($Action);
        }
    }

    public function Photos()
    {
        $photoset = new ArrayList();
        $this->extend('GetItems', $photoset);
        if (!$photoset->count()) {
            $photos = PhotoItem::get()->filter('PhotoAlbumID', $this->ID);
            if ($photos) {
                foreach ($photos as $photo) {
                    if ($photo->getComponent('Photo')->exists()) {
                        $photoset->push($photo);
                    }
                }
            }
        }
        return $photoset;
    }

    public function getTitle()
    {
        return $this->Name;
    }

    public function PaginatedPhotos()
    {
        $items = PaginatedList::create($this->Photos(), $this->request)
            ->setPageLength($this->PhotosPerPage);
        return $items ? $items : false;
    }

    public function getAlbumThumb()
    {
        $albumCover = $this->AlbumCover();
        $defaultAlbumCover = $this->PhotoGallery()->DefaultAlbumCover();

        if ($albumCover->exists()) {
            return $albumCover;
        } elseif ($defaultAlbumCover->exists()) {
            return $defaultAlbumCover;
        } else {
            return false;
        }
    }

    public function OnBeforeDelete()
    {
        // Delete the album cover
        $albumcover = $this->AlbumCover();
        $albumcoverfile = Image::get()->byID($albumcover->ID);
        if ($albumcoverfile) {
            $albumcoverfile->delete();
        }

        // Delete the photo items in that album
        $photoitems = $this->getComponents('PhotoItems');
        foreach ($photoitems as $photoitem) {
            $photoitemfile = Image::get()->byID($photoitem->Photo()->ID);
            if ($photoitemfile) {
                $photoitemfile->delete();
            }
        }

        // Delete the album folder
        $albumfolder = $this->AlbumFolder();
        $folder = Folder::get()->filter('Name', $albumfolder)->first();
        if ($folder) {
            $folder->delete();
        }

        return parent::OnBeforeDelete();
    }

}
