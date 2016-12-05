<?php
namespace Gimager\Form;

use Zend\Form\Form;

class GimagerForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('gimager');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Picture Title',
            ),
        ));
        $this->add(array(
            'name' => 'url',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Picture URL',
            ),
        ));
        $this->add(array(
            'name' => 'description',
            'attributes' => array(
                'type'  => 'textarea',
            ),
            'options' => array(
                'label' => 'Picture description',
            ),
        ));
        $this->add(array(
            'name' => 'comment',
            'attributes' => array(
                'type'  => 'textarea',
            ),
            'options' => array(
                'label' => 'comment',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Submit',
                'id' => 'submitbutton',
            ),
        ));
    }
}