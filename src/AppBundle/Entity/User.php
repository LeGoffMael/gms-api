<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User short summary.
 *
 * User description.
 *
 * @version 1.0
 * @author Maël Le Goff
 * @ORM\Entity()
 * @ORM\Table(name="users",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="users_name_mail_unique",columns={"nameUser","mailUser"})}
 * )
 */
class User {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="idUser")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="nameUser")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="mailUser")
     */
    protected $mail;

    /**
     * @ORM\Column(type="string", name="passwordUser")
     */
    protected $password;

    /**
     * @ORM\Column(type="string", name="hashValidationUser")
     */
    protected $hashValidation;

    /**
     * @ORM\Column(type="datetime", name="dateCreationUser")
     * @ORM\Version
     */
    protected $dateCreation;

    /**
     * @ORM\Column(type="string", name="forgetPassUser")
     */
    protected $forgetPass;

    /**
     * @ORM\Column(type="datetime", name="dateLastModificationUser")
     */
    protected $dateLastModification;

    /**
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="users")
     * @var Role
     */
    protected $role;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getMail() {
        return $this->mail;
    }

    public function setMail($mail) {
        $this->mail = $mail;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($pass) {
        $this->password = $pass;
    }

    public function getHashValidation() {
        return $this->hashValidation;
    }

    public function setHashValidation($hash) {
        $this->hashValidation = $hash;
    }

    public function getDateCreation() {
        return $this->dateCreation;
    }

    public function setDateCreation($dateCreation) {
        $this->dateCreation = $dateCreation;
    }

    public function getForgetPass() {
        return $this->forgetPass;
    }

    public function set($forgetPass) {
        $this->forgetPass = $forgetPass;
    }

    public function getDateLastModification() {
        return $this->dateLastModification;
    }

    public function setDateLastModification($dateLastModification) {
        $this->dateLastModification = $dateLastModification;
    }
}