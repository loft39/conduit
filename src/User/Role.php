<?php

namespace Conduit\User;

use Delight\Auth\Role as DelightRole;

final class Role
{
  const Admin = DelightRole::ADMIN;
  const User  = DelightRole::AUTHOR;
}
