<?php

namespace Conduit\User;

use Conduit\Database\Database;
use Conduit\User\Role;

use Delight\Auth\Auth;

use Conduit\Exceptions\User\UserNotLoggedInException;

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

  /**
   * Creates a new user.
   *
   * Takes a raw username, email, password, and Role enum (these are all sanitised),
   * and then creates a user by wrapping the delight-im/php-auth admin create method.
   *
   * @param string $username The users's desired username
   * @param string #email    The user's email address
   * @param string $password The user's password
   * @param Role   $role     The user's role constant
   *                         (Default: Role::User)
   *
   * @throw AuthError
   * @throw InvalidEmailException
   * @throw InvalidPasswordException
   * @throw UserAlreadyExistsException
   * @throw UnknownIdException
   *
   * @return bool
   */ 
  public function create(
    string $username,
    string $email,
    string $password,
    Role $role = Role::User
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

    } finally {
      //If the php-auth function returned an ID, the role assignment worked, and the PDO statement was successful,
      // return true, otherwise something broke so return false.
      return is_int($id);
    }
  }


  /**
   * @throws AuthError 
   */
  public function logout(): bool {

    if (!$this->auth->isLoggedIn()){
      throw new UserNotLoggedInException("No user currently logged in.");
    } else {
      try {
        $this->auth->logOut();
        $this->auth->destroySession();
      } finally {
        return true;
      }
    }

  }

}
