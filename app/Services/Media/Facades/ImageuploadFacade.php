<?php namespace App\Services\Media\Facades;

use Illuminate\Support\Facades\Facade;

class ImageuploadFacade extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'imageupload';
    }

}