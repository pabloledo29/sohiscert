<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

/**
 * Created by PhpStorm.
 * User: Eduardo.Facenda
 * Date: 14/12/2015
 * Time: 9:55
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="uploadedfileregistry")
 * @ORM\HasLifecycleCallbacks()
 */
class UploadedFileRegistry
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserOperator")
     * @ORM\JoinColumn(name="useroperator_id", referencedColumnName="id")
     */
    private $userOperator;

    /**
     * @var string
     * @ORM\Column(name="opnop", type="string")
     */
    private $opNop;

    /**
     * @var string
     * @ORM\Column(name="docexptypes", type="string")
     */
    private $docexptype;

    /**
     * @var string
     * @ORM\Column(name="filename", type="string")
     */
    private $fileName;

    /**
     * @var string
     * @ORM\Column(name="fileorigname", type="string")
     */
    private $fileOrigName;

    /**
     * @var string
     * @ORM\Column(name="file_path", type="string")
     */
    private $filePath;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate",type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedDateValue()
    {
        $this->createdDate = new \DateTime(date('Y-m-d H:i:s'));

    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set opNop
     *
     * @param string $opNop
     * @return UploadedFileRegistry
     */
    public function setOpNop($opNop)
    {
        $this->opNop = $opNop;

        return $this;
    }

    /**
     * Get opNop
     *
     * @return string
     */
    public function getOpNop()
    {
        return $this->opNop;
    }

    /**
     * Set docexptype
     *
     * @param string $docexptype
     * @return UploadedFileRegistry
     */
    public function setDocexptype($docexptype)
    {
        $this->docexptype = $docexptype;

        return $this;
    }

    /**
     * Get docexptype
     *
     * @return string
     */
    public function getDocexptype()
    {
        return $this->docexptype;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return UploadedFileRegistry
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set fileOrigName
     *
     * @param string $fileOrigName
     * @return UploadedFileRegistry
     */
    public function setFileOrigName($fileOrigName)
    {
        $this->fileOrigName = $fileOrigName;

        return $this;
    }

    /**
     * Get fileOrigName
     *
     * @return string
     */
    public function getFileOrigName()
    {
        return $this->fileOrigName;
    }

    /**
     * Set filePath
     *
     * @param string $filePath
     * @return UploadedFileRegistry
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get filePath
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return UploadedFileRegistry
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set userOperator
     *
     * @param \App\Entity\UserOperator $userOperator
     * @return UploadedFileRegistry
     */
    public function setUserOperator(UserOperator $userOperator = null)
    {
        $this->userOperator = $userOperator;

        return $this;
    }

    /**
     * Get userOperator
     *
     * @return \App\Entity\UserOperator
     */
    public function getUserOperator()
    {
        return $this->userOperator;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return UploadedFileRegistry
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
