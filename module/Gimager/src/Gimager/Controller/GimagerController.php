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
			$checkedUrl = $this->checkUrl($gimager->urlSubmitted);
			if ($checkedUrl['httpcode'] == 200)
			{
				$gimager->url = $checkedUrl['url'];
				// image is available, so lets make a local copy
				$_fileName = $gimager->id.str_replace(' ', '_', $gimager->title);
				$fileSaved = $this->getUrl($checkedUrl['url'], $_fileName);
				if ($fileSaved)
				{
					$gimager->localCopy = 1;
					
				}
				
			}
			if ($gimager->latestState != $checkedUrl['httpcode'])
			{
				$_now = new \DateTime();
				$now = $_now->format("Y-m-d H:i:s");
				$gimager->stateDate = $now;
			}

			$gimager->latestState = $checkedUrl['httpcode'];
			$this->getGimagerTable()->saveGimager($gimager);
			//@todo add results of check to gimager and store
			//@todo update process queue. On failure use executionMultiplier to set new execution timestamp
			$gimagers[] = $gimager;
			$checkedUrls[] = $checkedUrl;
		}
        return new ViewModel(array(
            'gimagers' => $gimagers,
            'checkedUrls' => $checkedUrls,
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
			$_httpcode = $this->isValidImage($_url);
		}
		else
		{
			$_url='http://'.$url;
			$_httpcode = $this->isValidImage($_url);
			if (!$_httpcode)
			{
				$_url = 'https://'.$url;
				$_httpcode = $this->isValidImage($_url);
			}
		}
		$data['url'] = $_url;
		$data['httpcode'] = $_httpcode;
		return $data;
	}
	
	protected function getUrl($url, $fileName)
	{
		$filetype['image/jpeg'] = '.jpg';
		$filetype['image/pjpeg'] = '.jpg';
		$filetype['image/png'] = '.png';
		$filetype['image/gif'] = '.gif';
		$filetype['image/pict'] = '.pic';
		$filetype['image/tiff'] = '.tif';
		$filetype['image/x-tiff'] = '.tif';
		$filetype['image/bmp'] = '.bmp';
		$filetype['image/x-icon'] = '.ico';
		$url = str_replace(' ', '%20', $url);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
		if(curl_exec($ch) === FALSE) 
		{
			return false;
		}
		else
		{
			$fileContent = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
			$type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			curl_close($ch);
//		$fileContent = file_get_contents($url);
			$extension = $filetype[$type];
			$fileLocation = $_SERVER['DOCUMENT_ROOT']."/uploads/copies/".$fileName.$extension;
			$fileHandle = fopen($fileLocation, 'w');
			fwrite($fileHandle, $fileContent);
			fclose($fileHandle);
		}
		return true;	
	}
	
	protected function isValidImage($url)
	{
		$url = str_replace(' ', '%20', $url);
		$ch = curl_init($url);
//		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
		curl_close($ch);
		return $httpcode;
	}
}