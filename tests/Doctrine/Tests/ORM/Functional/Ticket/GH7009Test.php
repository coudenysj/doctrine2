<?php

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\ORM\Annotation as ORM;

class GH7009Test extends \Doctrine\Tests\OrmFunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->sqlLoggerStack->enabled = false;

        try {
            $this->schemaTool->createSchema(
                [
                    $this->em->getClassMetadata(GH7009Anything::class),
                ]
            );
        } catch (\Exception $e) {
        }
    }

    public function testIssue()
    {
        $startMemory = memory_get_usage(true);

        for($y = 0;$y<10;$y++) {
            for($x = 0;$x<1000+$y;$x++) {
                $object = new GH7009Anything();
                $object->text = uniqid();
                $this->em->persist($object);
            }
            $this->em->flush();
            $this->em->clear();
        }

        $endMemory = memory_get_usage(true);

        self::assertSame($startMemory, $endMemory);
    }
}

/**
 * @ORM\Entity
 */
class GH7009Anything
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(name="_text", type="string", length=23)
     */
    public $text;
}
