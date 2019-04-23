<?php

namespace ParamConverter\Test\App\Model\Table;

use Cake\ORM\Table;

class AltUsersTable extends Table
{
    public $paramConverterGetMethod = 'altGet';

    public function altGet($id) {
        $altUser = $this->newEntity();
        $altUser->set('name', 'AltUserName');
        return $altUser;
    }
}
