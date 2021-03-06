<?php

namespace Rhubarb\Stem\Tests\Filters;

use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Filters\OneOf;
use Rhubarb\Stem\Tests\Fixtures\Example;
use Rhubarb\Stem\Tests\Fixtures\ModelUnitTestCase;

class OneOfTest extends ModelUnitTestCase
{
    /**
     * @var Collection
     */
    private $list;

    protected function setUp()
    {
        unset($this->list);

        parent::setUp();

        $example = new Example();
        $example->getRepository()->clearObjectCache();
        $example->Forename = "Pugh";
        $example->save();

        $example = new Example();
        $example->Forename = "Pugh";
        $example->save();

        $example = new Example();
        $example->Forename = "Barney";
        $example->save();

        $example = new Example();
        $example->Forename = "McGrew";
        $example->save();

        $example = new Example();
        $example->Forename = "Cuthbert";
        $example->save();

        $example = new Example();
        $example->Forename = "Dibble";
        $example->save();

        $example = new Example();
        $example->Forename = "Grub";
        $example->save();

        $this->list = new Collection(Example::class);
    }

    public function testFilters()
    {
        $filter = new OneOf("Forename", ["Cuthbert", "Dibble", "Grub", "Pugh"]);

        $this->list->filter($filter);
        $this->assertCount(5, $this->list);
        $this->assertContains("Pugh", $this->list[0]->Forename);

        $filter = new OneOf("Forename", ["Cuthbert", "Dibble", "Grub"]);
        $this->list->filter($filter);
        $this->assertCount(3, $this->list);
        $this->assertContains("Cuthbert", $this->list[0]->Forename);
    }
}