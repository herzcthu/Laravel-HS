<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionTableSeeder extends Seeder {

	public function run() {

		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		DB::table(config('access.permissions_table'))->truncate();
		DB::table(config('access.permission_role_table'))->truncate();
		DB::table(config('access.permission_user_table'))->truncate();

		$permission_model = config('access.permission');
		$viewBackend = new $permission_model;
		$viewBackend->name = 'view_backend';
		$viewBackend->display_name = 'View Backend';
		$viewBackend->system = true;
		$viewBackend->created_at = Carbon::now();
		$viewBackend->updated_at = Carbon::now();
		$viewBackend->save();
                
                $editAllUsers = new $permission_model;
		$editAllUsers->name = 'editall_users';
		$editAllUsers->display_name = 'Edit all other users';
		$editAllUsers->system = true;
		$editAllUsers->created_at = Carbon::now();
		$editAllUsers->updated_at = Carbon::now();
		$editAllUsers->save();
                
                $editUsers = new $permission_model;
		$editUsers->name = 'edit_users';
		$editUsers->display_name = 'Edit other users';
		$editUsers->system = true;
		$editUsers->created_at = Carbon::now();
		$editUsers->updated_at = Carbon::now();
		$editUsers->save();
                
                $uploadMedia = new $permission_model;
		$uploadMedia->name = 'upload_media';
		$uploadMedia->display_name = 'Upload Media files';
		$uploadMedia->system = true;
		$uploadMedia->created_at = Carbon::now();
		$uploadMedia->updated_at = Carbon::now();
		$uploadMedia->save();
                
                $manageMedia = new $permission_model;
		$manageMedia->name = 'manage_media';
		$manageMedia->display_name = 'Manage Media files';
		$manageMedia->system = true;
		$manageMedia->created_at = Carbon::now();
		$manageMedia->updated_at = Carbon::now();
		$manageMedia->save();
                
                $manageLocation = new $permission_model;
		$manageLocation->name = 'manage_location';
		$manageLocation->display_name = 'Manage Locations';
		$manageLocation->system = true;
		$manageLocation->created_at = Carbon::now();
		$manageLocation->updated_at = Carbon::now();
		$manageLocation->save();
                
                $accessLocation = new $permission_model;
		$accessLocation->name = 'access_location';
		$accessLocation->display_name = 'Access Locations';
		$accessLocation->system = true;
		$accessLocation->created_at = Carbon::now();
		$accessLocation->updated_at = Carbon::now();
		$accessLocation->save();
                
                $manageParticipant = new $permission_model;
		$manageParticipant->name = 'manage_participant';
		$manageParticipant->display_name = 'Manage Participants';
		$manageParticipant->system = true;
		$manageParticipant->created_at = Carbon::now();
		$manageParticipant->updated_at = Carbon::now();
		$manageParticipant->save();
                
                $accessParticipant = new $permission_model;
		$accessParticipant->name = 'access_participant';
		$accessParticipant->display_name = 'Access Participants';
		$accessParticipant->system = true;
		$accessParticipant->created_at = Carbon::now();
		$accessParticipant->updated_at = Carbon::now();
		$accessParticipant->save();
                
                $manageProject = new $permission_model;
		$manageProject->name = 'manage_project';
		$manageProject->display_name = 'Manage Projects';
		$manageProject->system = true;
		$manageProject->created_at = Carbon::now();
		$manageProject->updated_at = Carbon::now();
		$manageProject->save();
                
                $accessProject = new $permission_model;
		$accessProject->name = 'access_project';
		$accessProject->display_name = 'Access Projects';
		$accessProject->system = true;
		$accessProject->created_at = Carbon::now();
		$accessProject->updated_at = Carbon::now();
		$accessProject->save();
                
                $manageQuestion = new $permission_model;
		$manageQuestion->name = 'manage_question';
		$manageQuestion->display_name = 'Manage Questions';
		$manageQuestion->system = true;
		$manageQuestion->created_at = Carbon::now();
		$manageQuestion->updated_at = Carbon::now();
		$manageQuestion->save();
                
                $accessQuestion = new $permission_model;
		$accessQuestion->name = 'access_question';
		$accessQuestion->display_name = 'Access Questions';
		$accessQuestion->system = true;
		$accessQuestion->created_at = Carbon::now();
		$accessQuestion->updated_at = Carbon::now();
		$accessQuestion->save();
                
                $manageOrganization = new $permission_model;
		$manageOrganization->name = 'manage_organization';
		$manageOrganization->display_name = 'Manage Organizations';
		$manageOrganization->system = true;
		$manageOrganization->created_at = Carbon::now();
		$manageOrganization->updated_at = Carbon::now();
		$manageOrganization->save();
                
                $accessOrganization = new $permission_model;
		$accessOrganization->name = 'access_organization';
		$accessOrganization->display_name = 'Access Organizations';
		$accessOrganization->system = true;
		$accessOrganization->created_at = Carbon::now();
		$accessOrganization->updated_at = Carbon::now();
		$accessOrganization->save();
                
                $accessResult = new $permission_model;
		$accessResult->name = 'access_result';
		$accessResult->display_name = 'Access Results';
		$accessResult->system = true;
		$accessResult->created_at = Carbon::now();
		$accessResult->updated_at = Carbon::now();
		$accessResult->save();
                
                $addResult = new $permission_model;
		$addResult->name = 'add_result';
		$addResult->display_name = 'Add Results';
		$addResult->system = true;
		$addResult->created_at = Carbon::now();
		$addResult->updated_at = Carbon::now();
		$addResult->save();
                

		//Find the first role (admin) give it all permissions
		$role_model = config('access.role');
		$role_model = new $role_model;
		$admin = $role_model::first();
		$admin->permissions()->sync(
			[
				$viewBackend->id,
                                $editAllUsers->id,
                                $uploadMedia->id,
                                $manageMedia->id,
                                $manageLocation->id,
                                $accessLocation->id,
                                $manageParticipant->id,
                                $accessParticipant->id,
                                $manageProject->id,
                                $accessProject->id,
                                $manageOrganization->id,
                                $accessOrganization->id,
                                $manageQuestion->id,
                                $accessQuestion->id,
                                $addResult->id,
                                $accessResult->id,
			]
		);
                
                $user = $role_model::find(2);
		$user->permissions()->sync(
			[
				$uploadMedia->id,
                                $manageMedia->id,
                                $accessResult->id,
			]
		);
                /**
                $org_manager = $role_model::find(3);
		$org_manager->permissions()->sync(
			[
				$viewBackend->id,
                                $editAllUsers->id,
                                $uploadMedia->id,
                                $manageMedia->id,
                                $manageLocation->id,
                                $accessLocation->id,
                                $manageParticipant->id,
                                $accessParticipant->id,
                                $manageProject->id,
                                $accessProject->id,
                                $manageOrganization->id,
                                $accessOrganization->id,
                                $manageQuestion->id,
                                $accessQuestion->id,
                                $addResult->id,
                                $accessResult->id,
			]
		);
                
                $project_manager = $role_model::find(4);
		$project_manager->permissions()->sync(
			[
				$viewBackend->id,
                                $editUsers->id,
                                $uploadMedia->id,
                                $manageMedia->id,
                                $manageLocation->id,
                                $accessLocation->id,
                                $manageParticipant->id,
                                $accessParticipant->id,
                                $manageProject->id,
                                $accessProject->id,
                                $manageQuestion->id,
                                $accessQuestion->id,
                                $addResult->id,
                                $accessResult->id,
			]
		);
                
                $data_clerk = $role_model::find(5);
		$data_clerk->permissions()->sync(
			[
				$uploadMedia->id,
                                $manageMedia->id,
                                $accessLocation->id,
                                $accessParticipant->id,
                                $accessProject->id,
                                $accessQuestion->id,
                                $addResult->id,
                                $accessResult->id,
			]
		);
                 
		$permission_model = config('access.permission');
		$userOnlyPermission = new $permission_model;
		$userOnlyPermission->name = 'user_only_permission';
		$userOnlyPermission->display_name = 'User Only Permission';
		$userOnlyPermission->system = false;
		$userOnlyPermission->created_at = Carbon::now();
		$userOnlyPermission->updated_at = Carbon::now();
		$userOnlyPermission->save();

		$user_model = config('auth.model');
		$user_model = new $user_model;
		$user = $user_model::find(2);
		$user->permissions()->sync(
			[
                                $uploadMedia->id,	
                                $userOnlyPermission->id,
			]
		);
                
                 * 
                 */

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}
}