<?php

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

class PhotoGallery extends Page
{

    private static $db = array (
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
    'DefaultAlbumCover' => Image::class
    );

    private static $has_many = array (
        'PhotoAlbums' => 'PhotoAlbum',
        'PhotoItems' => 'PhotoItem'
    );

    private static $defaults = array (
        'AlbumsPerPage' => '6',
        'PhotosPerPage' => '20',
        'ShowAllPhotoAlbums' => true,
        'AlbumThumbnailWidth' => '200',
        'AlbumThumbnailHeight' => '200',
        'AlbumDefaultTop' => true,
        'PhotoThumbnailWidth' => '150',
        'PhotoThumbnailHeight' => '150',
        'PhotoFullWidth' => '1200',
        'PhotoFullHeight' => '1200',
        'PhotoDefaultTop' => true
    );

    private static $icon = 'photogallery/images/photogallery';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Albums', array (
            GridField::create(
                'PhotoAlbums',
                'Albums',
                $this->PhotoAlbums(),
                GridFieldConfig_RecordEditor::create(500)
                    ->addComponent($sortable = new GridFieldSortableRows('SortID'))
            )
        ));
        if ($this->AlbumDefaultTop == true) {
            $sortable->setAppendToTop(true);
        }

        $defaultAlbumCoverField = UploadField::create('DefaultAlbumCover')
            ->setDescription('jpg, gif and png filetypes allowed.')
            ->setFolderName('PhotoGallery')
            ->setAllowedExtensions(array (
                'jpg',
                'jpeg',
                'png',
                'gif'
            ));
        $fields->addFieldsToTab('Root.AlbumSettings', array (
            $defaultAlbumCoverField,
            SliderField::create('AlbumsPerPage', 'Albums Per Page', 1, 100, $this->AlbumsPerPage),
            SliderField::create('AlbumThumbnailWidth', 'Album Cover Thumbnail Width', 50, 400, $this->AlbumThumbnailWidth),
            SliderField::create('AlbumThumbnailHeight', 'Album Cover Thumbnail Height', 50, 400, $this->AlbumThumbnailHeight),
            CheckboxField::create('ShowAllPhotoAlbums', $this->ShowAllPhotoAlbums)
                ->setTitle('Show photo album even if it\'s empty'),
            CheckboxField::create('AlbumDefaultTop', $this->AlbumDefaultTop)
                ->setTitle('Sort new albums to the top by default')
        ));

        $fields->addFieldsToTab('Root.PhotoSettings', array (
            SliderField::create('PhotosPerPage', 'Photos Per Page', 1, 50, $this->PhotosPerPage),
            SliderField::create('PhotoThumbnailWidth', 'Photo Thumbnail Width', 50, 400, $this->PhotoThumbnailWidth),
            SliderField::create('PhotoThumbnailHeight', 'Photo Thumbnail Height', 50, 400, $this->PhotoThumbnailHeight),
            SliderField::create('PhotoFullWidth', 'Photo Fullsize Width', 400, 1900, $this->PhotoFullWidth),
            SliderField::create('PhotoFullHeight', 'Photo Fullsize Height', 400, 1200, $this->PhotoFullHeight),
            CheckboxField::create('PhotoDefaultTop', $this->PhotoDefaultTop)
                ->setTitle('Sort new photos to the top by default')
        ));

        return $fields;
    }
}

