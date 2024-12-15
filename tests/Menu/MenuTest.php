<?php

namespace App\Tests\Menu;

use App\Menu\Menu;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;

class MenuTest extends TestCase
{
    public function testMainMenu(): void
    {
        $router = $this->createMock(RouterInterface::class);
        $menu = new Menu($router);
        $result = $menu->getTopMenu(3);
        $this->assertCount(3, $result);
    }
}
