<?php
namespace Gimager\Model;

use Zend\Db\TableGateway\TableGateway;

class ProcessQueueTable
{
    protected $tableGateway;
	protected $now;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
		$_now = new \DateTime();
		$this->now = $_now->format('Y-m-d H:i:s');
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getEntries($amount)
    {
        $amount  = (int) $amount;
        $select = $this->tableGateway->getSql()->select();
		$select->where(array(
			'executed' => 0
			))
			->where->and->lessThan("nextExecution", $this->now)
		;
		$select->order('id ASC');
		$select->limit($amount);
		$results = $this->tableGateway->selectWith($select);
		if ($results->count() > 0)
		{
			return $results->buffer();
		}
		return false;
    }

    public function getProcessQueue($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveProcessQueue(ProcessQueue $ProcessQueue)
    {
        $data = array(
            'gimager_id'  => $ProcessQueue->gimager_id,
            'nextExecution' => $ProcessQueue->nextExecution,
            'executionMultiplier' => $ProcessQueue->executionMultiplier,
            'executed' => $ProcessQueue->executed,
        );

        $id = (int)$ProcessQueue->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        	$id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getProcessQueue($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception("Form: id $id does not exist");
            }
        }
		
		return $id;
    }

}