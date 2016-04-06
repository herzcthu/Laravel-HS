<?php
namespace App\Services\Forms;

use Illuminate\Contracts\View\View;
use Kris\LaravelFormBuilder\FormBuilderTrait;


/*
 * Copyright (C) 2015 sithu
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of FormService
 *
 * @author sithu
 */
class LoginForm {
    //put your code here
    use FormBuilderTrait;
    /**
	 * @return View2
	 */
	public function loginForm() {
            $form = $this->form('App\Forms\MemberLogin', [
                'method' => 'POST',
                'url' => action('Frontend\Auth\AuthController@postLogin')
            ]);
		return $form;
	}
        
    public function compose(View $view) {
        $view->with('loginform', $this->loginForm());
    }
    
    
}
