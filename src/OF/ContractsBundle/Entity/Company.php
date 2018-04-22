<?php

namespace OF\ContractsBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @Assert\File(maxSize="2048k")
     * @Assert\Image(mimeTypesMessage="Please upload a valid image.")
     *
     * Assert\Length(groups={"Registration", "Logo"})
     */
    protected $logoPictureFile;

    // for temporary storage
    private $tempLogoPicturePath;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)

     */

    protected $logoPicturePath;


    public function __construct()
    {
        $this->contracts = new \Doctrine\Common\Collections\ArrayCollection();
    }


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



    /////////// LOGO PICTURE//////////////////

     /**
     * Sets the file used for logo picture uploads
     * 
     * @param UploadedFile $file
     * @return object
     */
    public function setLogoPictureFile(UploadedFile $file = null) {
        // set the value of the holder
        $this->logoPictureFile       =   $file;
         // check if we have an old image path
        if (isset($this->logoPicturePath)) {
            // store the old name to delete after the update
            $this->tempLogoPicturePath = $this->logoPicturePath;
            $this->logoPicturePath = 'absent';
        } else {
            $this->logoPicturePath = 'initial';
        }

        return $this;
    }

     /**
     * Get the file used for logo picture uploads
     * 
     * @return UploadedFile
     */
    public function getLogoPictureFile() {

        return $this->logoPictureFile;
    }




 	/**
     * Set logoPicturePath
     *
     * @param string $logoPicturePath
     * @return User
     */
    public function setLogoPicturePath($logoPicturePath)
    {
        $this->logoPicturePath = $logoPicturePath;

        return $this;
    }

    /**
     * Get logoPicturePath
     *
     * @return string 
     */
    public function getLogoPicturePath()
    {
        return $this->logoPicturePath;
    }

    /**
     * Get the absolute path of the logoPicturePath
     */
    public function getLogoPictureAbsolutePath() {
        return null === $this->logoPicturePath
            ? null
            : $this->getUploadRootDir().'/'.$this->logoPicturePath;
    }

    /**
     * Get root directory for file uploads
     * 
     * @return string
     */
  
     protected function getUploadRootDir($type='logoPicture')

  	{

    // On retourne le chemin relatif vers l'image pour notre code PHP

    return __DIR__.'/../../../../web/'.$this->getUploadDir();

  	}

    /**
     * Specifies where in the /web directory logo pic uploads are stored
     * 
     * @return string
     */
    protected function getUploadDir($type='logoPicture') {
        // the type param is to change these methods at a later date for more file uploads
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/companiesPictures';
    }

    /**
     * Get the web path for the user
     * 
     * @return string
     */
    public function getWebLogoPicturePath() {

        return ''.$this->getUploadDir().'/'.$this->getLogoPicturePath(); 
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUploadLogoPicture() {
        if (null !== $this->getLogoPictureFile()) {
            // a file was uploaded
            // generate a unique filename
            $filename = $this->generateRandomLogoPictureFilename();
            $this->setLogoPicturePath($filename.'.'.$this->getLogoPictureFile()->guessExtension());
        }
    }
     /**
     * Generates a 32 char long random filename
     * 
     * @return string
     */
    public function generateRandomLogoPictureFilename() {
        $count                  =   0;
        do {
            $random = random_bytes(16);
            $randomString = bin2hex($random);
            $count++;
        }
        while(file_exists($this->getUploadRootDir().'/'.$randomString.'.'.$this->getLogoPictureFile()->guessExtension()) && $count < 50);

        return $randomString;
    }


    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     * 
     * Upload the logo picture
     * 
     * @return mixed
     */
    public function uploadLogoPicture() {
        // check there is a logo pic to upload
        if ($this->getLogoPictureFile() === null) {
            return;
        }
        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getLogoPictureFile()->move($this->getUploadRootDir(), $this->getLogoPicturePath());

        // check if we have an old image
        if (isset($this->tempLogoPicturePath) && file_exists($this->getUploadRootDir().'/'.$this->tempLogoPicturePath)) {
            // delete the old image
             if ($this->tempLogoPicturePath != "default.png"){
                unlink($this->getUploadRootDir().'/'.$this->tempLogoPicturePath);
            }
            // clear the temp image path
            $this->tempLogoPicturePath = null;
        }
        $this->logoPictureFile = null;
    }

     /**
     * @ORM\PostRemove()
     */
    public function removeLogoPictureFile()
    {
        if ($this->logoPicturePath != "default.png"){
            if ($file = $this->getLogoPictureAbsolutePath() && file_exists($this->getLogoPictureAbsolutePath())) {
                //unlink($this->getLogoPictureAbsolutePath());
            }
        }
    }


    


}