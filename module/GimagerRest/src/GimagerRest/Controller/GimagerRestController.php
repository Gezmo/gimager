<?php
/*
 * Gezmo
 *
 * PHP version 5.3+ / Zend Framework 2.+
 *
 * @category  PHP
 * @package   Gezmo
 * @author    Ge Zuidema <gezmo@gezmo.info>
 * @copyright 2016 Gezmo
 * @link      http://www.gezmo.info
 */
namespace GimagerRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Gimager\Form\GimagerForm;
use Gimager\Model\Gimager;
use Gimager\Model\GimagerTable;

class GimagerRestController extends AbstractRestfulController
{
    protected $gimagerTable;
	
	public function getList()
	{
		$results = $this->getGimagerTable()->fetchAll();
		$data = array();
		foreach($results as $result) {
			$data[] = $result;
		}
	 
		return new JsonModel(array('data' => $data));
	}
 
	public function get($id)
	{
		$gimager = $this->getGimagerTable()->getGimager($id);
	 
		return new JsonModel(array("data" => $gimager));
	}
	
	public function create($data)
	{
		if (isset($data['csv']))
		{
			$fieldName[0] = 'title';
			$fieldName[1] = 'url';
			$fieldName[2] = 'description';
			$fieldname[3] = 'comment';
			
			$csvLines = preg_split("/\\r\\n|\\r|\\n/", $data['csv']);
			$delimiter = $this->findDelimiter($csvLines);
			$numberOfDelimiters = substr_count($csvLines[0], $delimiter);
			$lineError = array();
			foreach($csvLines AS $key => $line)
			{
				// first line is a header, skip!
				if($key > 0)
				{
					// the assumption is that all lines must have the same number of fields
					// this will break if the delimiter is also used as part of a field
					if ($numberOfDelimiters != substr_count($line, $delimiter) )
					{
						$lineError[$key] = "Error on line ".$key+1;
					}
					else
					{
						$lineData = explode($delimiter, $line);
						$fieldCounter = 0;
						$gimagerData = array();
						foreach ($lineData AS $field)
						{
							$gimagerData[$fieldName[$fieldCounter]] = $field;
							if ($fieldName[$fieldCounter] == 'url')
							{
								$gimagerData['urlSubmitted'] = trim(strip_tags($field));
							}
							$fieldCounter++;
						}
						$gimager = new Gimager();
						$gimager->exchangeArray($gimagerData);
						$id = $this->getGimagerTable()->saveGimager($gimager);
						//@todo store id in process_queue so we can retreive the image
					}
				}
			}
			return new JsonModel(array("error" => $lineError));
		}
		else
		{
			$form = new GimagerForm();
			$gimager = new Gimager();
			$form->setInputFilter($gimager->getInputFilter());
			$form->setData($data);
			if ($form->isValid()) {
				$gimager->exchangeArray($form->getData());
				$id = $this->getGimagerTable()->saveGimager($gimager);
			}
		 
			return $this->get($id);
		}
	}
     
	public function update($id, $data)
	{
		$data['id'] = $id;
		$gimager = $this->getGimagerTable()->getGimager($id);
		$form  = new GimagerForm();
		$form->bind($gimager);
		$form->setInputFilter($gimager->getInputFilter());
		$form->setData($data);
		if ($form->isValid()) {
			$id = $this->getGimagerTable()->saveGimager($form->getData());
		}
	 
		return $this->get($id);
	}
 
	public function delete($id)
	{
		$this->getGimagerTable()->deleteGimager($id);
	 
		return new JsonModel(array(
			'data' => 'deleted',
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

	public function deleteList()
	{
		$response = $this->getResponse();
		$response->setStatusCode(400);
		
		$result = array(
			'Error' => array(
				'Http Status' => '400',
				'Code' => '123',
				'Message' => 'An image id is required to delete an image',
				'More info' => 'http://gimager.local/gimager',
				),
			)
		;
		
		return new JsonModel($result);
	}
	
	public function findDelimiter($data)
	{
		$komma0 = substr_count($data[0], ',');
		$semicolon0 = substr_count($data[0], ';');
		$pipe0 = substr_count($data[0], '|');
		$komma1 = substr_count($data[1], ',');
		$semicolon1 = substr_count($data[1], ';');
		$pipe1 = substr_count($data[1], '|');
		if($komma0 == $komma1)
		{
			return ',';
		}
		if($semicolon0 == $semicolon1)
		{
			return ';';
		}
		if ($pipe0 == $pipe1)
		{
			return '|';
		}
	}
	
}
