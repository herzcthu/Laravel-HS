<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Media\MediaContract;
use App\Services\Media\Imageupload;
use Illuminate\Http\Request;

class MediaController extends Controller
{
        //
        public function __construct(MediaContract $media) {
            $this->media = $media;
        }
        /**
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
            $medias = $this->media->getMediasPaginatedTable(config('access.users.default_per_page'), true);
            
            return view('backend.media', compact('medias'));
	}
        
        public function upload(Request $request, Imageupload $image)
        {
           
            if(array_key_exists('owner_id', $request->all())) {
                /**
                if ($request->hasFile('file')) {
                $mime = $request->file('file')->getMimeType();
                if(substr($mime, 0, 5) == 'image') {
                        $data = $image->upload($request->file('file'));
                    }
                    die('this is not image');
                
                }
                $request->file = $data;
                */
                $media = $this->media->create($request);
            
            
                //if (Request::ajax()) {
		//	return Response::json(View::make('ajax-posts')->with(compact('posts'))->render());
		//}
                if ($request->ajax()) {
                    $media['file'] = json_decode($media->file,true);
                    $media['owner_id'] = $media->owner_id;
                    $media['filename'] = $media->filename;
                    $media['filedir'] = $media->filedir;
                return response()->json($media);
                } else {
                 // return $media;  
                return redirect()->route('admin.media.index')->withFlashSuccess('File uploaded.');

                }
            } else {
                return redirect()->route('admin.media.index')->withFlashDanger('There is a problem uploading.');
            } 
            
        }
}
