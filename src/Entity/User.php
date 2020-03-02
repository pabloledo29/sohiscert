<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Entity;

use App\Entity\AccessLog;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;



/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @Gedmo\Loggable
 * @ORM\HasLifecycleCallbacks()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string", length=255)
 * @ORM\DiscriminatorMap({"useroperator" = "UserOperator", "useradmin" = "UserAdmin"})
 * @ORM\AttributeOverrides(
 * {@ORM\AttributeOverride(name="email",
 *     column=@ORM\Column(type="string", name="email", length=255, unique=false, nullable=false)),
 * @ORM\AttributeOverride(name="emailCanonical",
 *     column=@ORM\Column(type="string", name="email_canonical", length=255, unique=false, nullable=false))
 * })
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
 abstract class User implements UserInterface
//class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @Assert\NotBlank(message="Introduzca el nombre de usuario.")
     * @Assert\Length(
     *     min=5,
     *     max=18,
     *     minMessage="Su usuario es muy corto.",
     *     maxMessage="Su usuario es muy largo."
     * )
     * @ORM\Column(type="string", name="username", length=255, unique=false, nullable=false)
     */
    protected $username;
    
    /** 
    * @ORM\Column(type="string", name="username_canonical", length=255, unique=true, nullable=false)
    */
    private $username_canonical;


    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", name="email", length=255, unique=false, nullable=false)
     */
    protected $email;

    /**
     * @ORM\Column(name="createdDate",type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @ORM\Column(name="updatedDate",type="datetime", nullable=true)
     */
    private $updatedDate;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $expired;

    /**
     * @ORM\Column(name="expires_at",type="datetime", nullable=true)
     */
    private $expires_at = NULL;

    
    /**
     * @ORM\Column(name="credentials_expire_at",type="datetime", nullable=true)
     */
    private $credentials_expire_at;

    /**
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $last_login;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    protected $password;
    
    /**
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     */
    protected $password_requested_at = NULL;
    

    /**
     * @ORM\Column(type="string", name="email_canonical", length=255, unique=false, nullable=false)
     */
    private $emailCanonical;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @ORM\Column(type="array", name="roles")
     */
    private $roles = []; 

    /** 
    * @ORM\Column(type="string", name="salt", length=255, nullable=false,columnDefinition="VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL")
    */
    private $salt;

    /**
     * @ORM\Column(name="credentials_expired",type="boolean", nullable=false)
     */
    private $credentials_expired;
    
    

    /**
     * @ORM\Column(type="boolean", name="locked", nullable=false)
     */
    private $locked;
  
    /**
    * @ORM\Column(type="string", name="confirmation_token", length=255, nullable=true, columnDefinition="VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL")
    */
    private $confirmation_token = NULL;

    public function __construct()
    {
        //parent::__construct();
        // your own logic
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedDateValue()
    {
        $this->createdDate = new \DateTime(date('Y-m-d H:i:s'));
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PostUpdate
     */
    public function setUpdatedDateValue()
    {
        $this->updatedDate = new \DateTime(date('Y-m-d H:i:s'));
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
     * Get enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled) :self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return User
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function setPasswordRequestedAt(\DateTime $password_requested_at): self{
        $this->password_requested_at = $password_requested_at;

        return $this;
    }

    /**
     * Get password_requested_at
     *
     * @return \DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->password_requested_at;
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
     * Set updatedDate
     *
     * @param \DateTime $updatedDate
     * @return User
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

     /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN']);
    }

 

    public function addRoles(string $roles): self
    {
        if (!$this->roles->contains($roles)) {
            $this->roles[] = $roles;
            $roles->setAncestro($this);
        }

        return $this;
    }

    public function removeRoles(string $roles): self
    {
        if ($this->roles->contains($roles)) {
            $this->roles->removeElement($roles);
            // set the owning side to null (unless already changed)
            if ($roles->getAncestro() === $this) {
                $roles->setAncestro(null);
            }
        }

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }
    
    public function setPassword(string $password): self
    {
        
        /*$options = [
            'cost' => 12
        ];
        $this->password = password_hash($password,PASSWORD_BCRYPT,$options);
        */
        $this->password =$password;
        return $this;
    }


     /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        if( is_string($username)){
            $this->username=$username;
        }else{
            $this->username="";
        }
        
        return $this;
    }

    public function getEmail(): string
    {
        return (string) $this->email;
    }

    /* Validado por el estandar de formularios html5 */
    public function setEmail(string $email): self
    {   
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->email = "";
        }else{
            $this->email = $email;
        }
        return $this;
    }

   

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    

}