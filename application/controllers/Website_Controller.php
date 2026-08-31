<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Website_Controller extends CI_Controller {

    /**
     * Data passed to view
     * 
     * @var array
     */
    protected $data = array();
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->data['socials']       = $this->website_model->social_links();
        $this->data['navigation']    = $this->website_model->navigation();
        $this->data['current_year']  = date('Y');
        $this->data['static_export'] = getenv('STATIC_EXPORT') === '1';
    }
    
    /**
     * Index page
     */
    public function index()
    {
        $this->render_page(
            'index',
            'MVC project showcase',
            'A CodeIgniter MVC showcase by Zlatan Stajic, built with PHP, SQLite, tested models and controllers, and a responsive interface.'
        );
    }
    
    /**
     * About page
     */
    public function about()
    {
        $this->render_page(
            'about',
            'About the project',
            'Learn how Space Prospection demonstrates a complete server-rendered CodeIgniter application.'
        );
    }
    
    /**
     * Projects page
     */
    public function projects()
    {
        $data['projects'] = $this->website_model->projects();

        $this->render_page(
            'projects',
            'Space projects',
            'Explore database-driven space mission content rendered through the Space Prospection MVC application.',
            $data
        );
    }
    
    /**
     * Contact page
     */
    public function contact()
    {
        $this->render_page(
            'contact',
            'Contact',
            'Contact Zlatan Stajic about Space Prospection and its CodeIgniter implementation.'
        );
    }

    /**
     * Accepting parameters from form inside contact_view page
     */
    public function submit_message()
    {
        if ($this->input->method() !== 'post')
        {
            $this->output->set_status_header(405);
            echo 'Method Not Allowed';

            return;
        }

        $this->form_validation->set_error_delimiters('', "\n");
        $this->form_validation->set_rules(
            'name',
            'Name',
            'trim|required|max_length[80]|callback_alpha_space_only'
        );
        $this->form_validation->set_rules(
            'email',
            'E-mail Address',
            'trim|required|valid_email'
        );
        $this->form_validation->set_rules(
            'subject',
            'Subject',
            'trim|required|max_length[120]'
        );
        $this->form_validation->set_rules(
            'message',
            'Message',
            'trim|required|max_length[160]'
        );

        if ($this->form_validation->run() === FALSE)
        {
            echo validation_errors();
        }
        else
        {
            $name       = $this->input->post('name', TRUE);
            $from_email = $this->input->post('email', TRUE);
            $subject    = $this->input->post('subject', TRUE);
            $message    = $this->input->post('message', TRUE);

            $this->email->from(EMAIL_ADMIN, 'Space Prospection');
            $this->email->reply_to($from_email, $name);
            $this->email->to(EMAIL_ADMIN);
            $this->email->subject($subject);
            $this->email->message(nl2br(html_escape($message)));

            echo $this->email->send() ? 'YES' : 'NO';
        }
    }

    /**
     * Custom validation function to accept alphabets and space
     *
     * @param string $input_value
     *
     * @return bool
     */
    public function alpha_space_only($input_value)
    {
        if (preg_match("/^[\p{L}\p{M} .'-]+$/u", $input_value))
        {
            return TRUE;
        }

        $this->form_validation->set_message(
            'alpha_space_only',
            'The %s field may contain letters, spaces, apostrophes, hyphens, and periods.'
        );

        return FALSE;
    }

    /**
     * Render a complete website page
     *
     * @param string $view             Page view name
     * @param string $title            Browser title suffix
     * @param string $description      Page meta description
     * @param array  $page_data        Page-specific view data
     *
     * @return void
     */
    private function render_page($view, $title, $description, $page_data = array())
    {
        $data = array_merge($this->data, $page_data, array(
            'current_page'     => $view === 'index' ? NULL : $view,
            'page_title'       => $title,
            'meta_description' => $description,
        ));

        $this->load->view('templates/header_view', $data);
        $this->load->view('pages/' . $view . '_view', $data);
        $this->load->view('templates/footer_view', $data);
    }

}
