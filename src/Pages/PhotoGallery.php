<?php

namespace AndrewHoule\PhotoGallery\Pages;

use Page;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use AndrewHoule\PhotoGallery\Models\PhotoItem;
use SilverStripe\AssetAdmin\Forms\UploadField;
use AndrewHoule\PhotoGallery\Models\PhotoAlbum;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

class PhotoGallery extends Page
{

    private static $db = [
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
    ];

    private static $has_one = [
        'DefaultAlbumCover' => Image::class,
    ];

    private static $has_many = [
        'PhotoAlbums' => PhotoAlbum::class,
        'PhotoItems' => PhotoItem::class,
    ];

    private static $owns = [
        'DefaultAlbumCover',
        'PhotoAlbums',
        'PhotoItems',
    ];

    private static $cascade_deletes = [
        'DefaultAlbumCover',
        'PhotoAlbums',
        'PhotoItems',
    ];

    private static $defaults = [
        'AlbumsPerPage' => 6,
        'PhotosPerPage' => 20,
        'ShowAllPhotoAlbums' => true,
        'AlbumThumbnailWidth' => 400,
        'AlbumThumbnailHeight' => 400,
        'AlbumDefaultTop' => true,
        'PhotoThumbnailWidth' => 400,
        'PhotoThumbnailHeight' => 400,
        'PhotoFullWidth' => 1200,
        'PhotoFullHeight' => 1200,
    ];

    private static $icon_class = 'font-icon-image';

    private static $table_name = 'PhotoGallery';

    public function GalleryPageFolderName()
    {
        $directoryName = 'photogallery';
        $defaultName = 'gallerypage';
        $name = $this->MenuTitle;

        if ($name) {
            if ($name = $this->MenuTitle) {
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

        // Albums
        $fields->addFieldToTab('Root.Albums',
            GridField::create(
                'PhotoAlbums',
                'Albums',
                $this->PhotoAlbums(),
                GridFieldConfig_RecordEditor::create(100)
                    ->addComponent($sortableAlbums = new GridFieldSortableRows('SortID'))
                    ->removeComponentsByType(GridFieldDeleteAction::class)
            )
        );
        $sortableAlbums->setUpdateVersionedStage('Live');
        if ($this->AlbumDefaultTop == true) {
            $sortableAlbums->setAppendToTop(true);
        }

        // Album Settings
        $fields->addFieldsToTab('Root.AlbumSettings', [
            UploadField::create('DefaultAlbumCover')
                ->setDescription('jpg, gif and png filetypes allowed.')
                ->setFolderName($this->GalleryPageFolderName())
                ->setAllowedExtensions([
                    'jpg',
                    'jpeg',
                    'png',
                    'gif'
                ]),
            NumericField::create('AlbumsPerPage', 'Albums Per Page', $this->AlbumsPerPage),
            NumericField::create('AlbumThumbnailWidth', 'Album Cover Thumbnail Width', $this->AlbumThumbnailWidth),
            NumericField::create('AlbumThumbnailHeight', 'Album Cover Thumbnail Height', $this->AlbumThumbnailHeight),
            CheckboxField::create('ShowAllPhotoAlbums', $this->ShowAllPhotoAlbums)
                ->setTitle('Show photo album even if it\'s empty'),
            CheckboxField::create('AlbumDefaultTop', $this->AlbumDefaultTop)
                ->setTitle('Sort new albums to the top by default')
        ]);

        // Photo Settings
        $fields->addFieldsToTab('Root.PhotoSettings', [
            NumericField::create('PhotosPerPage', 'Photos Per Page', $this->PhotosPerPage),
            NumericField::create('PhotoThumbnailWidth', 'Photo Thumbnail Width', $this->PhotoThumbnailWidth),
            NumericField::create('PhotoThumbnailHeight', 'Photo Thumbnail Height', $this->PhotoThumbnailHeight),
            NumericField::create('PhotoFullWidth', 'Photo Fullsize Width', $this->PhotoFullWidth),
            NumericField::create('PhotoFullHeight', 'Photo Fullsize Height', $this->PhotoFullHeight)
        ]);

        return $fields;
    }

}
