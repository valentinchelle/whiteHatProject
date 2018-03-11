<?php

namespace OF\ContractsBundle\Entity;

/**
 * Contract
 */
class Contract
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $bounty;

    /**
     * @var string
     */
    private $difficulty;

    /**
     * @var string
     */
    private $report;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set bounty
     *
     * @param integer $bounty
     *
     * @return Contract
     */
    public function setBounty($bounty)
    {
        $this->bounty = $bounty;

        return $this;
    }

    /**
     * Get bounty
     *
     * @return int
     */
    public function getBounty()
    {
        return $this->bounty;
    }

    /**
     * Set difficulty
     *
     * @param string $difficulty
     *
     * @return Contract
     */
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    /**
     * Get difficulty
     *
     * @return string
     */
    public function getDifficulty()
    {
        return $this->difficulty;
    }

    /**
     * Set report
     *
     * @param string $report
     *
     * @return Contract
     */
    public function setReport($report)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Get report
     *
     * @return string
     */
    public function getReport()
    {
        return $this->report;
    }
}

