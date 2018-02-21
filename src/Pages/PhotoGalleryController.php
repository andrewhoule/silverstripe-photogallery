<?php

namespace AndrewHoule\PhotoGallery\Pages;

use AndrewHoule\PhotoGallery\Models\PhotoAlbum;
use PageController;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\View\Requirements;

class PhotoGalleryController extends PageController
{

    public static function load_requirements()
    {
        Requirements::CSS('resources/andrewhoule/silverstripe-photogallery/magnific-popup/dist/magnific-popup.css');
        Requirements::CSS('resources/andrewhoule/silverstripe-photogallery/client/css/photogallery.css');
        Requirements::javascript('resources/andrewhoule/silverstripe-photogallery/magnific-popup/libs/jquery/jquery.js');
        Requirements::javascript('resources/andrewhoule/silverstripe-photogallery/magnific-popup/dist/jquery.magnific-popup.js');
        Requirements::javascript('resources/andrewhoule/silverstripe-photogallery/client/js/magnific-popup_init.js');
    }

    public function init()
    {
        parent::init();
        self::load_requirements();
    }

    private static $allowed_actions = [
        'album'
    ];

    public function getAlbum()
    {
        $params = $this->getURLParams();
        if (is_numeric($params['ID']) && $album = PhotoAlbum::get()->byID((int)$params['ID'])) {
            return $album;
        } else {
            return $this->PhotoAlbums()->first();
        }
    }

    public function album()
    {
        if ($photoAlbum = $this->getAlbum()) {
            $Data = array('PhotoAlbum' => $photoAlbum);
            return $this->Customise($Data);
        } else {
            return $this->httpError(404, 'Sorry that photo album could not be found');
        }
    }

    public function AllPhotoAlbums()
    {
        return PhotoAlbum::get()
            ->filter('PhotoGalleryID', $this->ID);
    }

    public function PopulatedPhotoAlbums()
    {
        $photoAlbums = PhotoAlbum::get()->filter('PhotoGalleryID', $this->ID);
        $photoAlbumSet = new ArrayList();

        if ($photoAlbums->exists()) {
            foreach ($photoAlbums as $photoAlbum) {
                if ($photoAlbum->photoItems()->exists() and $photoAlbum->photoGallery()->exists()) {
                    $photoAlbumSet->push($photoAlbum);
                }
            }
        }
        return $photoAlbumSet;
    }

    public function PhotoAlbums()
    {
        if ($this->ShowAllPhotoAlbums) {
            return $this->AllPhotoAlbums();
        } else {
            return $this->PopulatedPhotoAlbums();
        }
    }

    public function PaginatedAlbums()
    {
        if ($this->AlbumsPerPage > 0) {
            $pageLength = $this->AlbumsPerPage;
        } else {
            $pageLength = 6;
        }

        $items = PaginatedList::create($this->PhotoAlbums(), $this->request)
            ->setPageLength($pageLength);
        return $items ? $items : false;
    }

    public function Photos()
    {
        return $this->getAlbum()->Photos();
    }

    public function PaginatedPhotos()
    {
        if ($this->PhotosPerPage > 0) {
            $pageLength = $this->PhotosPerPage;
        } else {
            $pageLength = 20;
        }

        $items = PaginatedList::create($this->Photos(), $this->request)
            ->setPageLength($pageLength);
        return $items ? $items : false;
    }

    public function OtherAlbums()
    {
        $otherAlbumSet = new ArrayList();

        $otherAlbums = PhotoAlbum::get()
            ->exclude('ID', $this->getAlbum()->ID)
            ->filter('PhotoGalleryID', $this->ID)
            ->limit('10');

        if ($otherAlbums->exists()) {
            foreach ($otherAlbums as $otherAlbum) {
                if ($otherAlbum->PhotoItems()->exists() and $otherAlbum->PhotoGallery()->exists() and $otherAlbum->AlbumCover()->exists()) {
                    $otherAlbumSet->push($otherAlbum);
                }
            }
        }
        return $otherAlbumSet;
    }

}
