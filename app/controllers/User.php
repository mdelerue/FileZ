<?php
/**
 * Copyright 2010  Université d'Avignon et des Pays de Vaucluse 
 * email: gpl@univ-avignon.fr
 *
 * This file is part of Filez.
 *
 * Filez is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Filez is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Filez.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Controller used for user administration
 */
class App_Controller_User extends Fz_Controller {

    public function init () {
        layout ('layout'.DIRECTORY_SEPARATOR.'admin.html.php');
    }

    /**
     * Action list users
     * List users.
     */
    public function indexAction () {
        $this->secure ('admin');
        set ('users', Fz_Db::getTable ('User')->findAll ()); // TODO paginate
        return html ('user/index.php');
    }

    /**
     * Action called to display user details
     */
    public function showAction () {
        $this->secure ('admin');
        set ('user', Fz_Db::getTable ('User')->findById (params ('id')));
        return html ('user/show.php');
    }

    /**
     * Action called to post values of a new user.
     */
    public function postnewAction () {
        $this->secure ('admin');
//print_r($_REQUEST);
        $post = $_POST;
          $user = new App_Model_User ();
          $user->setUsername  ($post ['username']);
          $user->setPassword  ($this->encrypt($post ['password']));
          $user->setFirstname ($post ['firstname']);
          $user->setLastname  ($post ['lastname']);
          $user->setIs_admin  ( ('on'==$post ['is_admin']) ? 1 : 0 );
          $user->setEmail     ($post ['email']);
	  if(filter_var($post ['email'], FILTER_VALIDATE_EMAIL) && null!=$post ['username'] && (3 <= strlen($post['password'])) ){
          //try {
          //    $user->save ();
          //}
     	     $user->save ();
		// test if the email and the username are not already in DB
	  }
          else {
	    echo "error: email not valid or no username or password too short.";       //return error message.
          }
          return $this->indexAction();
    }

    private function encrypt($pass) {
       $algorithm = fz_config_get ('user_factory_options', 'db_password_algorithm');
       $encryped_pass = ($algorithm == "SHA1") ? sha1($pass) : md5($pass) ; // TODO: allow the others password algorithms defined in filez.ini.example.
       return $encryped_pass;
    }

    /**
     * Action called to create a new user
     */
    public function createAction () {
        $this->secure ('admin');
        return html ('user/create.php');
    }

    /**
     * Action called to edit a user
     */
    public function editAction () {
        $this->secure ('admin');
        return html ('user/edit.php');
        //TODO
    }

    /**
     * Action called to delete a user
     */
    public function deleteAction () {
        $this->secure ('admin');
        $user = Fz_Db::getTable ('User')->findById (params ('id'));
	if($user)
	{
		$user->delete();
	}
        return $this->indexAction();
    }
}
