<?php
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\View\Requirements;

class PhotoGallery_Controller extends PageController
{

    public static function load_requirements()
    {
        Requirements::CSS(ModuleLoader::getModule('photogallery')->getResource("magnific-popup/dist/magnific-popup.css"));
        Requirements::CSS(ModuleLoader::getModule('photogallery')->getResource("css/photogallery.css"));
        Requirements::javascript(ModuleLoader::getModule('silverstripe/admin')->getResource('/thirdparty/jquery/jquery.js'));
        Requirements::javascript(ModuleLoader::getModule('photogallery')->getResource('magnific-popup/dist/jquery.magnific-popup.js'));
        Requirements::javascript(ModuleLoader::getModule('photogallery')->getResource('js/magnific-popup_init.js'));
    }

    public function init()
    {
        parent::init();
        self::load_requirements();
    }

    private static $allowed_actions = array(
        'album'
    );

    public function getAlbum()
    {
        $Params = $this->getURLParams();
        if (is_numeric($Params['ID']) && $Album = PhotoAlbum::get()->byID((int)$Params['ID'])) {
            return $Album;
        } else {
            return $this->PhotoAlbums()->first();
        }
    }

    public function album()
    {
        if ($PhotoAlbum = $this->getAlbum()) {
            $Data = array('PhotoAlbum' => $PhotoAlbum);
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
        $PhotoAlbums = PhotoAlbum::get()->filter("PhotoGalleryID", $this->ID);
        $PhotoAlbumSet = new ArrayList();
        if ($PhotoAlbums->exists()) {
            foreach ($PhotoAlbums as $PhotoAlbum) {
                if ($PhotoAlbum->getComponents("PhotoItems")->exists() and $PhotoAlbum->getComponent("PhotoGallery")->exists()) {
                    $PhotoAlbumSet -> push($PhotoAlbum);
                }
            }
        }
        return $PhotoAlbumSet;
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
        $paginatedalbums = new PaginatedList($this->PhotoAlbums(), $this->request);
        if ($this->AlbumsPerPage > 0) {
            $paginatedalbums->setPageLength($this->AlbumsPerPage);
        } else {
            $paginatedalbums->setPageLength('6');
        }
        return $paginatedalbums;
    }

    public function AlbumCount()
    {
        return $this->PhotoAlbums()->count();
    }

    public function Photos()
    {
        return $this->getAlbum()->Photos();
    }

    public function PaginatedPhotos()
    {
        $paginatedphotos = new PaginatedList($this->Photos(), $this->request);
        if ($this->PhotosPerPage > 0) {
            $paginatedphotos->setPageLength($this->PhotosPerPage);
        } else {
            $paginatedphotos->setPageLength('20');
        }
        return $paginatedphotos;
    }

    public function OtherAlbums()
    {
        $OtherAlbums = PhotoAlbum::get()->exclude("ID", $this->getAlbum()->ID)->filter("PhotoGalleryID", $this->ID)->limit("10");
        $OtherAlbumSet = new ArrayList();
        if ($OtherAlbums->exists()) {
            foreach ($OtherAlbums as $OtherAlbum) {
                if ($OtherAlbum->getComponents("PhotoItems")->exists() and $OtherAlbum->getComponent("PhotoGallery")->exists() and $OtherAlbum->getComponent("AlbumCover")->exists()) {
                    $OtherAlbumSet -> push($OtherAlbum);
                }
            }
        }
        return $OtherAlbumSet;
    }
}
