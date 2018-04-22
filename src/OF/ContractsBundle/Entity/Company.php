<?php

namespace OF\ContractsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table(name="company")
 * @ORM\Entity(repositoryClass="OF\ContractsBundle\Repository\CompanyRepository")
 */
class Company
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="date", type="datetime")
     */

    protected $date;
    /**
     * @ORM\ManyToOne(targetEntity="OF\UserBundle\Entity\User", inversedBy="companies")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    
    private $owner;
    /**
     * @ORM\OneToMany(targetEntity="OF\ContractsBundle\Entity\Contract", mappedBy="company")
     */
    private $contracts;
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
     * Set name
     *
     * @param string $name
     *
     * @return Company
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set owner
     *
     * @param \OF\UserBundle\Entity\User $owner
     *
     * @return Company
     */
    public function setOwner(\OF\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \OF\UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Company
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contracts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add contract
     *
     * @param \OF\ContractsBundle\Entity\Contract $contract
     *
     * @return Company
     */
    public function addContract(\OF\ContractsBundle\Entity\Contract $contract)
    {
        $this->contracts[] = $contract;

        return $this;
    }

    /**
     * Remove contract
     *
     * @param \OF\ContractsBundle\Entity\Contract $contract
     */
    public function removeContract(\OF\ContractsBundle\Entity\Contract $contract)
    {
        $this->contracts->removeElement($contract);
    }

    /**
     * Get contracts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContracts()
    {
        return $this->contracts;
    }
}
