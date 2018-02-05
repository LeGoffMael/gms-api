<?php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * User short summary.
 *
 * User description.
 *
 * @version 1.0
 * @author Ma�l Le Goff
 * @ORM\Entity()
 * @ORM\Table(name="users",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="users_name_mail_unique",columns={"nameUser","emailUser","hashValidationUser","forgetPassUser"})}
 * )
 */
class User implements UserInterface {
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
     * @ORM\Column(type="string", name="emailUser")
     */
    protected $email;

    /**
     * @ORM\Column(type="string", name="passwordUser")
     */
    protected $password;

    /**
     * Not save in the database.
     * It contain's the user's password in clear when it is created or modified.
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", name="hashValidationUser")
     */
    protected $hashValidation;

    /**
     * @ORM\Column(type="datetime", name="createdAtUser")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="string", name="forgetPassUser")
     */
    protected $forgetPass;

    /**
     * @ORM\Column(type="datetime", name="updatedAtUser")
     */
    protected $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="users")
     * @ORM\JoinColumn(name="idRole", referencedColumnName="idRole")
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

    public function getEmail() {
        return $this->email;
    }
    public function setEmail($email) {
        $this->email = $email;
    }

    public function getHashValidation() {
        return $this->hashValidation;
    }
    public function setHashValidation($hash) {
        $this->hashValidation = $hash;
    }

    public function getCreatedAt() {
        return $this->updatedAt;
    }
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    public function getForgetPass() {
        return $this->forgetPass;
    }
    public function setForgetPass($forgetPass) {
        $this->forgetPass = $forgetPass;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    public function getRole() {
        return $this->role;
    }
    public function setRole($role) {
        $this->role = $role;
        return $this;
    }

    /******** UserInterface ********/

    public function getPassword() {
        return $this->password;
    }
    public function setPassword($password) {
        $this->password = $password;
    }

    public function getPlainPassword() {
        return $this->plainPassword;
    }
    public function setPlainPassword($plainPassword) {
        $this->plainPassword = $plainPassword;
    }

    public function getUsername() {
        return $this->name;
    }

    public function getRoles() {
        //return $this->role;
        return [];
    }

    public function getSalt() {
        return null;
    }

    /**
     * Deleting sensitive data
     */
    public function eraseCredentials() {
        $this->plainPassword = null;
    }
}