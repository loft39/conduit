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

  public function id(): int|null {
    return $this->id ?? null;
  }

  public function sortOrder(): bool|null {
    return $this->sortorder ?? null;
  }

  /**
   * @throws InvalidDateAddedException
   */
  public function dateAdded(): DateTime|null {
    if ($this->dateadded != "") {
      try {
        return new DateTime($this->dateadded);
      } catch (Exception) {
        throw new InvalidDateAddedException("dateAdded field for this object cannot be parsed into DateTime object");
      }
    } else {
      return null;
    }
  }

  public function published(): bool {
    return $this->published === 1;
  }

  public function getFields(): array {
    return get_class_vars(get_class($this));
  }
  
}