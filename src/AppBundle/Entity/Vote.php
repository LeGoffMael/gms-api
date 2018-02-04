<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vote short summary.
 *
 * Vote description.
 *
 * @version 1.0
 * @author Maël Le Goff
 * @ORM\Entity()
 * @ORM\Table(name="votes",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="votes_ip_user_image_unique",columns={"ipVote","idUser","idImage"})}
 * )
 */
class Vote {
    /**
     * @ORM\Id
     * @ORM\Column(type="string", name="ipVote")
     */
    protected $ip;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="idUser", referencedColumnName="idUser")
     * @var User
     */
    protected $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="votes")
     * @ORM\JoinColumn(name="idImage", referencedColumnName="idImage")
     * @var Image
     */
    protected $image;

    /**
     * @ORM\Column(type="integer", name="valueVote")
     */
    protected $value;

    /**
     * @ORM\Column(type="datetime", name="dateVote")
     */
    protected $date;

    public function getIp() {
        return $this->ip;
    }
    public function setIp($ip) {
        $this->ip = $ip;
        return $this;
    }

    public function getUser() {
        return $this->user;
    }
    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    public function getImage() {
        return $this->image;
    }
    public function setImage($image) {
        $this->image = $image;
        return $this;
    }

    public function getValue() {
        return $this->value;
    }
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function getDate() {
        return $this->date;
    }
    public function setDate($date) {
        $this->date = $date;
        return $this;
    }
}