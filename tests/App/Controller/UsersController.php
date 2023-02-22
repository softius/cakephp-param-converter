<?php

namespace ParamConverter\Test\App\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
use DateTime;
use ParamConverter\Test\App\Model\Entity\User;

class UsersController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();
        $this->autoRender = false;
    }

    public function withScalar(bool $bool, int $int, float $float, string $string): void
    {
        Configure::write('Tests.result', compact('bool', 'int', 'float', 'string'));
    }

    public function withEntity(User $entity): void
    {
        Configure::write('Tests.result', compact('entity'));
    }

    public function withDatetime(DateTime $dateTime): void
    {
        Configure::write('Tests.result', compact('dateTime'));
    }

    public function withFrozenDate(FrozenDate $date): void
    {
        Configure::write('Tests.result', compact('date'));
    }

    public function withFrozenTime(FrozenTime $frozenTime): void
    {
        Configure::write('Tests.result', compact('frozenTime'));
    }

    public function withNoParams(): void
    {
        Configure::write('Tests.result', []);
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @param mixed $c
     */
    public function withNoTypehint($a, $b, $c): void
    {
        Configure::write('Tests.result', compact('a', 'b', 'c'));
    }

    /**
     * @param int $a
     */
    public function withOptional(int $a = 0): void
    {
        Configure::write('Tests.result', compact('a'));
    }
}
