<?php
/*
 * PHP / Zend Framework 2
 *
 * @category  PHP
 * @package   gezmo
 * @author    Ge Zuidema <gezmo@gezmo.info>
 * @copyright 2016 gezmo
 * @link      http://www.gezmo.info
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Validator;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
		$promo_content = "
			Welcome";
		
		return new ViewModel(array(
			'promoContent'	=> $promo_content,
        ));
    }

    public function contactAction()
    {
		// Init
		$showForm			= true;
		$message 			= 'For questions or remarks, please use the form.';
		$errorMessages		= array();
		$missingFields		= array();
		$message_name		= '';
		$message_email		= '';
		$message_text		= '';
				
		// Something posted
		$request = $this->getRequest();
        if ( $request->isPost() )
		{
			$message_name	= $this->formatString( $request->getPost('name' , '') );
			$message_email	= $this->formatString( $request->getPost('email', '') );
			$message_text	= $this->formatString( $request->getPost('formtext', '') );
		
			// Validate email
			$validator = new Validator\EmailAddress();
			$valid_email = $validator->isValid($message_email);
			
			// Input ok?
			if ( $message_name && $message_email && $message_text && $valid_email )
			{
				// Mail the message
				// part of overall gezmo application, left out here.
			}
			else
			{
				// Missing fields
				if ( !$message_name  ) $missingFields[] = 'name';
				if ( !$message_email || !$valid_email ) $missingFields[] = 'email';
				if ( !$message_text  ) $missingFields[] = 'formtext';
				
				// Error
				$errorMessages[] = 'Sorry, your input did not pass our control system. You either did not fill all fields, or your email address is invalid.';
			}
		}
	
		// To the view
		return new ViewModel(array(
        	'errorMessages' => $errorMessages,
			'missingFields'	=> $missingFields,
			'showForm'		=> $showForm,
            'message'		=> $message,
			'name'			=> $message_name,
			'email'			=> $message_email,
			'formtext'		=> $message_text,
        ));
    }
	
}
