<?php

namespace ParamConverter\Test\App\Controller;

use Cake\Controller\Controller;
use ParamConverter\Test\App\Model\Entity\AltUser;

class AltUsersController extends Controller
{
    public function withEntity(AltUser $entity): void
    {
    }
}
