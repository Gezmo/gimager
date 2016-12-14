<?php
namespace Gimager\Model;

use Zend\Db\TableGateway\TableGateway;

class GimagerTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getGimager($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function fetchByTitle($title)
    {
        $rowset = $this->tableGateway->select(array('title' => $title));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveGimager(Gimager $gimager)
    {
        $data = array(
            'localCopy'  => $gimager->localCopy,
            'latestState'  => $gimager->latestState,
            'stateDate'  => $gimager->stateDate,
            'title'  => $gimager->title,
            'url' => $gimager->url,
            'urlSubmitted'  => $gimager->urlSubmitted,
            'description' => $gimager->description,
            'comment' => $gimager->comment,
        );
		
        $id = (int)$gimager->id;
		$titleExists = $this->fetchByTitle($data['title']);
		if ($titleExists)
		{
			$id = $titleExists->id;
		}
        if ($id == 0) {
            $this->tableGateway->insert($data);
        	$id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getGimager($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception("Form: id $id does not exist");
            }
        }
		
		return $id;
    }

    public function deleteGimager($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}