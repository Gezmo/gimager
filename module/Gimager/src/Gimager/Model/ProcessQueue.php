<?php
namespace Gimager\Model;

class ProcessQueue
{
    public $id;
    public $title;
    public $url;
    public $description;
    public $comment;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id']))     ? $data['id']     : null;
        $this->gimager_id  = (isset($data['gimager_id']))  ? $data['gimager_id']  : null;
        $this->dateCreated = (isset($data['dateCreated'])) ? $data['dateCreated'] : null;
        $this->nextExecution = (isset($data['nextExecution'])) ? $data['nextExecution'] : null;
        $this->executionMultiplier = (isset($data['executionMultiplier'])) ? $data['executionMultiplier'] : null;
        $this->executed = (isset($data['executed'])) ? $data['executed'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

}