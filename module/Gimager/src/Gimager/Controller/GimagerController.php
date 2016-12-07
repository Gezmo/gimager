<?php
namespace Gimager\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Gimager\Model\Gimager;
use Gimager\Form\GimagerForm;
use Gimager\Model\ProcessQueue;

class GimagerController extends AbstractActionController
{
    protected $gimagerTable;
    protected $processQueueTable;

    public function indexAction()
    {
        return new ViewModel(array(
            'gimagers' => $this->getGimagerTable()->fetchAll(),
        ));
    }

    public function addAction()
    {
        $form = new GimagerForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $gimager = new Gimager();
            $form->setInputFilter($gimager->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
				$formData = $form->getData();
				$checkData = $this->checkUrl($formData['url']);
                $gimager->exchangeArray($form->getData());
                $this->getGimagerTable()->saveGimager($gimager);

                // Redirect to list of images
                return $this->redirect()->toRoute('gimager');
            }
        }
        return array('form' => $form);
    }

    public function editGimagerAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('gimager', array(
                'action' => 'add'
            ));
        }
        $gimager = $this->getGimagerTable()->getGimager($id);

        $form  = new GimagerForm();
        $form->bind($gimager);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($gimager->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getGimagerTable()->saveGimager($form->getData());

                // Redirect to list of images
                return $this->redirect()->toRoute('gimager');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('gimager');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getGimagerTable()->deleteGimager($id);
            }

            // Redirect to list of images
            return $this->redirect()->toRoute('gimager');
        }

        return array(
            'id'    => $id,
            'gimager' => $this->getGimagerTable()->getGimager($id)
        );
    }

    public function entriesAction()
    {
        return new ViewModel(array(
            'processEntries' => $this->getProcessQueueTable()->fetchAll(),
        ));
    }

    public function processAction()
    {
        $amount = (int) $this->params()->fromRoute('id', 1);
		$entries =  $this->getProcessQueueTable()->getEntries($amount);
		foreach($entries AS $entry)
		{
    	    $gimager = $this->getGimagerTable()->getGimager($entry->gimager_id);
			$checkedUrl = $this->checkUrl($gimager->url);
			//@todo add results of check to gimager and store
			//@todo update process queue. On failure use executionMultiplier to set new execution timestamp
			$gimagers[] = $gimager;
		}
        return new ViewModel(array(
            'gimagers' => $gimagers,
        ));
    }

    public function getGimagerTable()
    {
        if (!$this->gimagerTable) {
            $sm = $this->getServiceLocator();
            $this->gimagerTable = $sm->get('Gimager\Model\GimagerTable');
        }
        return $this->gimagerTable;
    }
	
    public function getProcessQueueTable()
    {
        if (!$this->processQueueTable) {
            $sm = $this->getServiceLocator();
            $this->processQueueTable = $sm->get('Gimager\Model\ProcessQueueTable');
        }
        return $this->processQueueTable;
    }
	
	protected function checkUrl($url)
	{
		if (substr($url, 0, 4) == 'http')
		{
			$_url = $url;
			$result = $this->isValidImage($_url);
		}
		else
		{
			$_url='http://'.$url;
			$result = $this->isValidImage($_url);
			if (!$result)
			{
				$_url = 'https://'.$url;
				$result = $this->isValidImage($_url);
			}
		}
		$data['url'] = $_url;
		$data['result'] = $result;
		return $data;
	}
	
	protected function isValidImage($url)
	{
		$url = str_replace(' ', '%20', $url);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
		curl_close($ch);
		return $httpcode;
	}
}