<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Legacy\String\Test;

use Windwalker\Legacy\String\SimpleTemplate;

/**
 * Test class of SimpleTemplate
 *
 * @since 3.0
 * @deprecated Legacy code
 */
class SimpleTemplateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
    }

    /**
     * Method to test render().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\String\SimpleTemplate::render
     */
    public function testRender()
    {
        $data['foo']['bar']['baz'] = 'Flower';

        $this->assertEquals('This is Flower', SimpleTemplate::render('This is {{ foo.bar.baz }}', $data));
        $this->assertEquals('This is ', SimpleTemplate::render('This is {{ foo.yoo }}', $data));
        $this->assertEquals('This is Flower', SimpleTemplate::render('This is [ foo.bar.baz ]', $data, ['[', ']']));
    }
}
