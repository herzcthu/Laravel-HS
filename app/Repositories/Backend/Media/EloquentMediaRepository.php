<?php namespace App\Repositories\Backend\Media;

use App\Exceptions\GeneralException;
use App\Media;
use App\Repositories\Frontend\Auth\AuthenticationContract;
use App\Repositories\Frontend\User\UserContract;
use App\Services\Media\Imageupload;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;

/**
 * Class EloquentMediaRepository
 * @package App\Repositories\Media
 */
class EloquentMediaRepository implements MediaContract {

	

	/**
	 * @param UserRepositoryContract $role
	 * @param AuthenticationContract $auth
	 */
	public function __construct(UserContract $users, Imageupload $image) {
            $this->users = $users;
            $this->image_upload = $image;
	}

	/**
	 * @param $id
	 * @param bool $withUsers
	 * @return mixed
	 * @throws GeneralException
	 */
	public function findOrThrowException($id, $withUsers = false) {
		if ($withUsers) {
			$auth_id = access()->user()->id;
                
                        $media = Media::where('owner_id', $auth_id)->withTrashed()->find($id);
                }else{
			$media = Media::withTrashed()->find($id);
                }
		if (! is_null($media)) return $media;

		throw new GeneralException('That media does not exist.');
	}

	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function getMediasPaginated($per_page, $withUsers = false, $status = 0, $order_by = 'id', $sort = 'asc') {
            
            if ($withUsers)	
            {
                $auth_id = access()->user()->id;
                
                return Media::where('owner_id', $auth_id)->orderBy($order_by, $sort)->paginate($per_page);
            }else{
                return Media::orderBy($order_by, $sort)->paginate($per_page);
            }
                
	}
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function getMediasPaginatedTable($per_page, $withUsers = false) {
            
            if ($withUsers)
            {
                $auth_id = access()->user()->id;
                
                return Media::where('owner_id', $auth_id)->sort(Input::get('field'), Input::get('sort'))->paginate($per_page);
            }else{
                return Media::sort(Input::get('field'), Input::get('sort'))->paginate($per_page);
            }
                		
	}
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function searchMedias($queue, $status = 1, $order_by = 'id', $sort = 'asc') {
            return Media::where('status', $status)->orderBy($order_by, $sort)->search($queue)->get();
	}

	/**
	 * @param $per_page
	 * @return Paginator
	 */
	public function getDeletedMediasPaginated($per_page) {
		return Media::onlyTrashed()->paginate($per_page);
	}

	/**
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getAllMedias($order_by = 'id', $sort = 'asc') {
		return Media::orderBy($order_by, $sort)->get();
	}

	/**
	 * @param $input
	 * @param $users
	 * @param $permissions
	 * @return bool
	 * @throws GeneralException
	 * @throws MediaNeedsUsersException
	 */
	public function create($input) {
            
		$media = $this->createMediaStub($input);
                
                $current_user = auth()->user();
                $media->owner()->associate($current_user);
                
		if ($media->save()) {
			//Media Created, Validate Users
			//$this->validateUserAmount($media, $users['owner_id']);

			//Attach new users
			//$media->attachUsers($users['owner_id']);

			return $media;
		}

		throw new GeneralException('There was a problem creating this media. Please try again.');
	}

	/**
	 * @param $id
	 * @param $input
	 * @param $users
	 * @return bool
	 * @throws GeneralException
	 */
	public function update($id, $input) {
		$media = $this->findOrThrowException($id);
		//$this->checkMediaByEmail($input, $media);
                //dd(\Illuminate\Support\Facades\Input::file());
                
                
		if ($media->update($input)) {
                      if ($file) {
                       // $media->saveMedia($file, 'profile_picture');
                      }

			//For whatever reason this just wont work in the above call, so a second is needed for now
			$media->status = isset($input['status']) ? 1 : 0;
			$media->confirmed = isset($input['confirmed']) ? 1 : 0;
			$media->save();

			$this->checkMediaUsersCount($users);
			$this->flushUsers($users, $media);
			$this->flushPermissions($permissions, $media);

			return true;
		}

		throw new GeneralException('There was a problem updating this media. Please try again.');
	}

	
	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function destroy($id) {
		if (auth()->id() == $id)
			throw new GeneralException("You can not delete yourself.");

		$media = $this->findOrThrowException($id);
		if ($media->delete())
			return true;

		throw new GeneralException("There was a problem deleting this media. Please try again.");
	}

	/**
	 * @param $id
	 * @return boolean|null
	 * @throws GeneralException
	 */
	public function delete($id) {
		$media = $this->findOrThrowException($id, true);

		//Detach all users & permissions
		$media->detachUsers($media->users);
		$media->detachPermissions($media->permissions);

		try {
			$media->forceDelete();
		} catch (\Exception $e) {
			throw new GeneralException($e->getMessage());
		}
	}

	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function restore($id) {
		$media = $this->findOrThrowException($id);

		if ($media->restore())
			return true;

		throw new GeneralException("There was a problem restoring this media. Please try again.");
	}

	/**
	 * @param $id
	 * @param $status
	 * @return bool
	 * @throws GeneralException
	 */
	public function mark($id, $status) {
		if (auth()->id() == $id && ($status == 0 || $status == 2))
			throw new GeneralException("You can not do that to yourself.");

		$media = $this->findOrThrowException($id);
		$media->status = $status;

		if ($media->save())
			return true;

		throw new GeneralException("There was a problem updating this media. Please try again.");
	}

	/**
	 * Check to make sure at lease one role is being applied or deactivate media
	 * @param $media
	 * @param $users
	 * @throws MediaNeedsUsersException
	 */
	private function validateUserAmount($media, $users) {
		//Validate that there's at least one role chosen, placing this here so
		//at lease the media can be updated first, if this fails the users will be
		//kept the same as before the media was updated
		if (count($users) == 0) {
			//Deactivate media
			$media->status = 0;
			$media->save();

			$exception = new MediaNeedsUsersException();
			$exception->setValidationErrors('You must choose at lease one role. Media has been created but deactivated.');

			//Grab the media id in the controller
			$exception->setMediaID($media->id);
			throw $exception;
		}
	}

	

	/**
	 * @param $users
	 * @param $media
	 */
	private function flushUsers($users, $media)
	{
		//Flush users out, then add array of new ones
		$media->detachUsers($media->users);
		$media->attachUsers($users['owner_id']);
	}
	

	/**
	 * @param $users
	 * @throws GeneralException
	 */
	private function checkMediaUsersCount($users)
	{
		//Media Updated, Update Users
		//Validate that there's at least one role chosen
		if (count($users['owner_id']) == 0)
			throw new GeneralException('You must choose at least one role.');
	}

	/**
	 * @param $input
	 * @return mixed
	 */
	private function createMediaStub($request)
	{
		$media = new Media;
                
                if ($request->hasFile('file')) {
                    $mime = $request->file('file')->getMimeType();
                    
                    if(substr($mime, 0, 5) == 'image') {
                        $data = $this->image_upload->upload($request->file('file'));
                    }
                    else {
                        die('this is not image');  
                    }
                                  
                }
                if(isset($data)) {            
		$media->file = json_encode($data);
                
                $media->filename = $data['original_filename'];
                $media->filedir = $data['original_filedir'];
		//$media->owner_id = $request['owner_id'];
                ///$media->save();
		return $media;
                }else{
                throw new GeneralException('No media file found!');
                }
	}
}