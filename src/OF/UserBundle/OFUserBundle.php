<?php

// src/Of/UserBundle/OfUserBundle.php


namespace OF\UserBundle;


use Symfony\Component\HttpKernel\Bundle\Bundle;


class OFUserBundle extends Bundle

{

  public function getParent()

  {

    return 'FOSUserBundle';

  }

}