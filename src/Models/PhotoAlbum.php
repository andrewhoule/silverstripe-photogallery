<?php

namespace AndrewHoule\PhotoGallery\Models;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\TabSet;
use SilverStripe\Assets\Folder;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Forms\GridField\GridField;
use AndrewHoule\PhotoGallery\Models\PhotoItem;
use SilverStripe\AssetAdmin\Forms\UploadField;
use AndrewHoule\PhotoGallery\Pages\PhotoGallery;
use AndrewHoule\PhotoGallery\Extensions\Sortable;
use Bummzack\SortableFile\Forms\SortableUploadField;
use AndrewHoule\PhotoGallery\Traits\CMSPermissionProvider;

class PhotoAlbum extends DataObject
{

    use CMSPermissionProvider;

    private static $db = [
        'Name' => 'Text',
        'Description' => 'HTMLText',
    ];

    private static $has_one = [
        'PhotoGallery' => PhotoGallery::class,
        'AlbumCover' => Image::class,
    ];

    private static $many_many = [
        'PhotoItems' => [
            'through' => PhotoItem::class,
            'from' => 'PhotoAlbum',
            'to' => 'Photo',
        ]
    ];

    private static $owns = [
        'AlbumCover',
        'PhotoItems',
    ];

    private static $cascade_deletes = [
        'AlbumCover',
        'PhotoItems',
    ];

    private static $summary_fields = [
        'Thumbnail' => 'Cover Photo',
        'Name' => 'Name',
        'DescriptionExcerpt' => 'Description',
    ];

    private static $extensions = [
        Versioned::class,
        Sortable::class,
    ];

    private static $table_name = 'PhotoAlbum';

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
                ]),
            SortableUploadField::create('PhotoItems')
                    ->setTitle('Photos')
                    ->setDescription('jpg, gif and png filetypes allowed.')
                    ->setFolderName($this->AlbumFolderName())
                    ->setAllowedExtensions([
                        'jpg',
                        'jpeg',
                        'png',
                        'gif'
                    ])
                    ->setSortColumn('SortID')
        ]);

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
            $photos = PhotoItem::get()
                ->filter('PhotoAlbumID', $this->ID)
                ->sort('SortID');
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
        $photoitems = $this->getManyManyComponents("PhotoItems");
        foreach ($photoitems as $photoitem) {
            $photoitemfile = Image::get()->byID($photoitem->ID);
            if ($photoitemfile) {
                $photoitemfile->delete();
            }
        }

        // Delete the album folder
        $albumfolder = $this->AlbumFolderName();
        $folder = Folder::get()->filter('Name', $albumfolder)->first();
        if ($folder) {
            $folder->delete();
        }

        return parent::OnBeforeDelete();
    }

}
