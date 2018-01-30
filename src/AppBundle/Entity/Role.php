<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Role short summary.
 *
 * Role description.
 *
 * @version 1.0
 * @author Maël Le Goff
 * @ORM\Entity()
 * @ORM\Table(name="roles",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="roles_name_unique",columns={"nameRole"})}
 * )
 */
class Role {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="idRole")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="nameRole")
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="roles")
     * @var User[]
     */
    protected $users;

    public function __construct() {
        $this->users = new ArrayCollection();
    }

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

    /**
     * Add User
     *
     * @param User $price
     *
     * @return Category
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;
        return $this;
    }
    /**
     * Remove User
     *
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }
    /**
     * Get Users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers() {
        return $this->users;
    }
}