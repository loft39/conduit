<?php

namespace Conduit\User;

use Conduit\Database\Database;
use Conduit\User\Role;

use Delight\Auth\Auth;
use Delight\Auth\AuthError;
use Delight\Auth\InvalidEmailException;
use Delight\Auth\InvalidPasswordException;
use Delight\Auth\UnknownIdException;
use Delight\Auth\UserAlreadyExistsException;

class UserController extends Database {

  private array $options;
  private Auth $auth;

  public function __construct(
    private readonly Array|null $userOptions = []
  )
  {
    parent::__construct();
    require_once($_SERVER['DOCUMENT_ROOT'] . "/../app/objects/" . $this->objectName . "Object.php");

    // Default options
    $defaults = [
      "includeUnpublished" => false,
      "limit"              => false,
      "customSort"         => [
          "field"          => "sortorder",
          "direction"      => "desc"
      ]
    ];

    $this->auth = new Auth($this->dbObject);

    // Merge provided options with defaults
    $this->options = array_replace_recursive($defaults, $this->userOptions);

  }

  public function create(
    string $username,
    string $email,
    string $password,
    Role $role
  ): bool {

    // Sanitise user input
    $username_c  = htmlspecialchars($username);
    $email_c     = filter_var($email, FILTER_SANITIZE_EMAIL);
    $pass_c      = htmlspecialchars($password);
    $role_c      = (int)$role;

    try {

      //Create the user in php-auth
      $id = $this->auth->admin()->createUser($email_c,$pass_c,$username_c);

      //Add their relevant role
      $this->auth->admin()->addRoleForUserById($id, $role);

      //If the php-auth function returned an ID, the role assignment worked, and the PDO statement was successful,
      // return true, otherwise something broke so return false.
      return is_int($id);

    } catch (
      AuthError |
      InvalidEmailException |
      InvalidPasswordException |
      UserAlreadyExistsException |
      UnknownIdException $e
    ) {
      die($e->getMessage());
      return false;
    }

  }

}
