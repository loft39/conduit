<?php

namespace Conduit\Objects;

class GenericObject {

  private int $id;
  private int $sortorder;
  private int $published;

  public function id(): int {
    return $this->id;
  }

  public function sortOrder(): bool {
    return $this->sortorder;
  }

  public function published(): bool {
    return $this->published === 1;
  }
  
}