<?php
/**
 * MailerTest.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@dutchakdev.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.dutchakdev.net/license license
 * @version XXX
 * @link http://www.dutchakdev.net
 * @package tests\unit
 */

namespace tests\unit;

use dutchakdev\sendgrid\Mailer;

/**
 * Test node basic functions
 *
 * @author Philippe Gaultier <pgaultier@dutchakdev.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.dutchakdev.net/license license
 * @version XXX
 * @link http://www.dutchakdev.net
 * @package tests\unit
 * @since XXX
 */
class MailerTest extends TestCase
{

    public function setUp()
    {
        $this->mockApplication([
            'components' => [
                'email' => $this->createTestEmailComponent()
            ]
        ]);
    }

    protected function createTestEmailComponent()
    {
        $component = new Mailer();
        $component->token = SENDGRID_TOKEN;
        return $component;
    }

    public function testGetPostmarkMailer()
    {
        $mailer = $this->createTestEmailComponent();
        $this->assertInstanceOf(Mailer::className(), $mailer);
    }
}
