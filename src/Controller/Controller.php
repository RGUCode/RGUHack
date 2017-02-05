<?php

namespace Site\Controller;

use Interop\Container\ContainerInterface as Container;

class Controller {
  public function __construct(Container $container) {
    $this->ci = $container;
  }
}
