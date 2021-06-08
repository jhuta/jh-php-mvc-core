<?php

namespace jhuta\phpmvc;

use jhuta\phpmvc\DB\DBModel;

abstract class UserModel extends DBModel {
  abstract public function getDisplayName(): string;
}