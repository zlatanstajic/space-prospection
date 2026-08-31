<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Website_Controller_Test extends UnitTestCase {

    /**
     * Testing class object data
     *
     * @var Website_Controller
     */
    private $class_object;

    /**
     * Website test setup method
     */
    public function setUp(): void
    {
        $this->class_object = $this->newController('Website_Controller');
    }

    /**
     * Testing public pages
     */
    public function test_public_pages()
    {
        $pages = array(
            ''         => 'MVC project showcase | Space Prospection',
            'about'    => 'About the project | Space Prospection',
            'projects' => 'Content powered by SQLite',
            'contact'  => 'Start a conversation about the project',
        );

        foreach ($pages as $path => $expected)
        {
            $output = $this->request('GET', $path);

            $this->assertStringContainsString($expected, $output);
        }
    }

    /**
     * Testing static export contact page
     */
    public function test_static_export_contact_page()
    {
        putenv('STATIC_EXPORT=1');

        try
        {
            $output = $this->request('GET', 'contact');

            $this->assertStringContainsString('read-only demonstration', $output);
            $this->assertStringNotContainsString('<form', $output);
            $this->assertStringNotContainsString('contact.js', $output);
        }
        finally
        {
            putenv('STATIC_EXPORT');
        }
    }

    /**
     * Testing submit_message method
     */
    public function test_submit_message_method()
    {
        $output = $this->request('GET', 'submit-message');

        $this->assertEquals('Method Not Allowed', $output);

        $output = $this->request('POST', 'submit-message', array(
            'name'    => 'Zlatan 1',
            'email'   => 'not-an-email-address',
            'subject' => '',
            'message' => '',
        ));

        $this->assertStringContainsString('Subject field is required', $output);
        $this->assertStringNotContainsString('<p>', $output);

        $output = $this->request('POST', 'submit-message', array(
            'name'    => 'Zlatan',
            'email'   => 'contact@zlatanstajic.com',
            'subject' => 'space-prospection',
            'message' => 'This is PHPUnit test message',
        ));

        $this->assertEquals('NO', $output);
    }

    /**
     * Testing alpha_space_only method
     */
    public function test_alpha_space_only_method()
    {
        $result = $this->class_object->alpha_space_only('Zlatan 1');

        $this->assertFalse($result);

        $result = $this->class_object->alpha_space_only("Zlatan Stajić-O'Neil");

        $this->assertTrue($result);
    }

}
