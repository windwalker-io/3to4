<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 LYRASOFT Taiwan, Inc.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\IO\Test\Cli;

use Windwalker\Legacy\Filter\InputFilter;
use Windwalker\Legacy\IO\Cli\Input\CliInput;
use Windwalker\Legacy\Test\TestHelper;

/**
 * Test class of CliInput
 *
 * @since 2.0
 * @deprecated Legacy code
 */
class CliInputTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test instance.
     *
     * @var CliInput
     */
    protected $instance;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->instance = new CliInput();
    }

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
     * Method to test serialize().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Input\CliInput::serialize
     * @TODO   Implement testSerialize().
     */
    public function testSerialize()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test get().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Input\CliInput::get
     */
    public function testGet()
    {
        $argv = ['/dev/null', '--foo=bar', '-ab', 'blah', '-g', 'flower sakura'];

        $this->instance = new CliInput($argv);

        $this->assertEquals(
            'bar',
            $this->instance->get('foo'),
            'Line: ' . __LINE__ . '.'
        );

        $this->assertEquals(
            true,
            $this->instance->get('a'),
            'Line: ' . __LINE__ . '.'
        );

        $this->assertEquals(
            true,
            $this->instance->get('b'),
            'Line: ' . __LINE__ . '.'
        );

        $this->assertEquals(
            ['blah'],
            $this->instance->args,
            'Line: ' . __LINE__ . '.'
        );

        // Default filter
        $this->assertEquals(
            'flower sakura',
            $this->instance->get('g'),
            'Default filter should be string. Line: ' . __LINE__
        );
    }

    /**
     * Test the Windwalker\Legacy\IO\Cli::get method.
     *
     * @return  void
     *
     * @covers  \Windwalker\Legacy\IO\Cli\Input\CliInput::get
     * @covers  \Windwalker\Legacy\IO\Cli\Input\CliInput::parseArguments
     * @since   2.0
     */
    public function testParseLongArguments()
    {
        $_SERVER['argv'] = ['/dev/null', '--ab', 'cd', '--ef', '--gh=bam'];

        $this->instance = new CliInput();

        $this->assertThat(
            $this->instance->get('ab'),
            $this->identicalTo('cd'),
            'Line: ' . __LINE__ . '.'
        );

        $this->assertThat(
            $this->instance->get('ef'),
            $this->identicalTo('1'),
            'Line: ' . __LINE__ . '.'
        );

        $this->assertThat(
            $this->instance->get('gh'),
            $this->identicalTo('bam'),
            'Line: ' . __LINE__ . '.'
        );

        $this->assertEmpty(
            $this->instance->args,
            'Line: ' . __LINE__ . '.'
        );
    }

    /**
     * Test the Windwalker\Legacy\IO\Cli::get method.
     *
     * @return  void
     *
     * @covers  \Windwalker\Legacy\IO\Cli\Input\CliInput::get
     * @covers  \Windwalker\Legacy\IO\Cli\Input\CliInput::parseArguments
     * @since   2.0
     */
    public function testParseShortArguments()
    {
        $_SERVER['argv'] = ['/dev/null', '-ab', '-c', '-e', 'f', 'foobar', 'ghijk'];

        $this->instance = new CliInput();

        $this->assertThat(
            $this->instance->get('a'),
            $this->identicalTo('1'),
            'Line: ' . __LINE__ . '.'
        );

        $this->assertThat(
            $this->instance->get('b'),
            $this->identicalTo('1'),
            'Line: ' . __LINE__ . '.'
        );

        $this->assertThat(
            $this->instance->get('c'),
            $this->identicalTo('1'),
            'Line: ' . __LINE__ . '.'
        );

        $this->assertThat(
            $this->instance->get('e'),
            $this->identicalTo('f'),
            'Line: ' . __LINE__ . '.'
        );

        $this->assertThat(
            $this->instance->args,
            $this->equalTo(['foobar', 'ghijk']),
            'Line: ' . __LINE__ . '.'
        );
    }

    /**
     * Test the JInput::parseArguments method.
     *
     * @dataProvider provider_parseArguments
     */
    public function testParseArguments($inputArgv, $expectedData, $expectedArgs)
    {
        $_SERVER['argv'] = $inputArgv;

        $this->instance = new CliInput();

        $this->assertThat(
            TestHelper::getValue($this->instance, 'data'),
            $this->identicalTo($expectedData)
        );

        $this->assertThat(
            $this->instance->args,
            $this->identicalTo($expectedArgs)
        );
    }

    /**
     * Test inputs:
     *
     * php test.php --foo --bar=baz
     * php test.php -abc
     * php test.php arg1 arg2 arg3
     * php test.php plain-arg --foo --bar=baz --funny="spam=eggs" --also-funny=spam=eggs \
     *     'plain arg 2' -abc -k=value "plain arg 3" --s="original" --s='overwrite' --s
     * php test.php --key value -abc not-c-value
     * php test.php --key1 value1 -a --key2 -b b-value --c
     *
     * Note that this pattern is not supported: -abc c-value
     *
     * @codingStandardsIgnoreStart
     */
    public function provider_parseArguments()
    {
        // @codingStandardsIgnoreEnd
        return [

            // php test.php --foo --bar=baz
            [
                ['test.php', '--foo', '--bar=baz'],
                [
                    'foo' => true,
                    'bar' => 'baz',
                ],
                [],
            ],

            // php test.php -abc
            [
                ['test.php', '-abc'],
                [
                    'a' => true,
                    'b' => true,
                    'c' => true,
                ],
                [],
            ],

            // php test.php arg1 arg2 arg3
            [
                ['test.php', 'arg1', 'arg2', 'arg3'],
                [],
                [
                    'arg1',
                    'arg2',
                    'arg3',
                ],
            ],

            // php test.php plain-arg --foo --bar=baz --funny="spam=eggs" --also-funny=spam=eggs \
            //      'plain arg 2' -abc -k=value "plain arg 3" --s="original" --s='overwrite' --s
            [
                [
                    'test.php',
                    'plain-arg',
                    '--foo',
                    '--bar=baz',
                    '--funny=spam=eggs',
                    '--also-funny=spam=eggs',
                    'plain arg 2',
                    '-abc',
                    '-k=value',
                    'plain arg 3',
                    '--s=original',
                    '--s=overwrite',
                    '--s',
                ],
                [
                    'foo' => true,
                    'bar' => 'baz',
                    'funny' => 'spam=eggs',
                    'also-funny' => 'spam=eggs',
                    'a' => true,
                    'b' => true,
                    'c' => true,
                    'k' => 'value',
                    's' => 'overwrite',
                ],
                [
                    'plain-arg',
                    'plain arg 2',
                    'plain arg 3',
                ],
            ],

            // php test.php --key value -abc not-c-value
            [
                ['test.php', '--key', 'value', '-abc', 'not-c-value'],
                [
                    'key' => 'value',
                    'a' => true,
                    'b' => true,
                    'c' => true,
                ],
                [
                    'not-c-value',
                ],
            ],

            // php test.php --key1 value1 -a --key2 -b b-value --c
            [
                ['test.php', '--key1', 'value1', '-a', '--key2', '-b', 'b-value', '--c'],
                [
                    'key1' => 'value1',
                    'a' => true,
                    'key2' => true,
                    'b' => 'b-value',
                    'c' => true,
                ],
                [],
            ],
        ];
    }

    /**
     * Test the Windwalker\Legacy\IO\Cli::get method.
     *
     * @return  void
     *
     * @covers  \Windwalker\Legacy\IO\Cli\Input\CliInput::get
     * @since   2.0
     */
    public function testGetFromServer()
    {
        // Check the object type.
        $this->assertInstanceOf(
            'Windwalker\Legacy\\IO\\Input',
            $this->instance->server,
            'Line: ' . __LINE__ . '.'
        );

        // Test the get method.
        $this->assertThat(
            $this->instance->server->get('PHP_SELF', null, InputFilter::STRING),
            $this->identicalTo($_SERVER['PHP_SELF']),
            'Line: ' . __LINE__ . '.'
        );
    }

    /**
     * Method to test all().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Input\CliInput::all
     */
    public function testAll()
    {
        $_SERVER['argv'] = ['/dev/null', '-ab', '-c', '-e', 'f', 'foobar', 'ghijk'];

        $this->instance = new CliInput();

        $this->assertEquals(['a' => 1, 'b' => 1, 'c' => 1, 'e' => 'f'], $this->instance->all());
    }

    /**
     * Method to test unserialize().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Input\CliInput::unserialize
     * @TODO   Implement testUnserialize().
     */
    public function testUnserialize()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test getArgument().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Input\CliInput::getArgument
     */
    public function testGetArgument()
    {
        $_SERVER['argv'] = ['/dev/null', '-ab', '-c', '-e', 'f', 'foobar', 'ghijk'];

        $input = new CliInput();

        $this->assertEquals('foobar', $input->getArgument(0));
        $this->assertEquals('d', $input->getArgument(5, 'd'));
    }

    /**
     * Method to test setArgument().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Input\CliInput::setArgument
     */
    public function testSetArgument()
    {
        $_SERVER['argv'] = ['/dev/null', '-ab', '-c', '-e', 'f', 'foobar', 'ghijk'];

        $input = new CliInput();

        $input->setArgument(2, 'bar');

        $this->assertEquals('bar', $input->getArgument(2));
    }

    /**
     * Method to test in().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Input\CliInput::in
     */
    public function testIn()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test getInputStream().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Input\CliInput::getInputStream
     */
    public function testGetInputStream()
    {
        $this->assertEquals(STDIN, $this->instance->getInputStream());
    }

    /**
     * Method to test setInputStream().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Input\CliInput::setInputStream
     * @TODO   Implement testSetInputStream().
     */
    public function testSetInputStream()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test getCalledScript().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Input\CliInput::getCalledScript
     */
    public function testGetCalledScript()
    {
        $_SERVER['argv'] = ['/dev/null', '-ab', '-c', '-e', 'f', 'foobar', 'ghijk'];

        $input = new CliInput();

        $this->assertEquals('/dev/null', $input->getCalledScript());
    }

    /**
     * Method to test setCalledScript().
     *
     * @return void
     *
     * @covers \Windwalker\Legacy\IO\Cli\Input\CliInput::setCalledScript
     * @TODO   Implement testSetCalledScript().
     */
    public function testSetCalledScript()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
