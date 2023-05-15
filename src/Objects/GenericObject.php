<?php

namespace Conduit\Objects;

use DateTime;
use Exception;

use Conduit\Exceptions\Database\InvalidDateAddedException;

class GenericObject {

  protected int $id;
  protected int $sortorder;
  protected int $dateadded;
  protected int $published;

  public function id(): int {
    return $this->id;
  }

  public function sortOrder(): bool {
    return $this->sortorder;
  }

  /**
   * @throws InvalidDateAddedException
   */
  public function dateAdded(): DateTime|bool {
    if ($this->dateadded != "") {
      try {
        return new DateTime($this->dateadded);
      } catch (Exception) {
        throw new InvalidDateAddedException("dateAdded field for this object cannot be parsed into DateTime object");
      }
    } else {
      return false;
    }
  }

  public function published(): bool {
    return $this->published === 1;
  }

  public function getFields(): array {
    return get_class_vars(get_class($this));
  }
  
}