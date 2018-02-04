<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag short summary.
 *
 * Tag description.
 *
 * @version 1.0
 * @author Maël Le Goff
 * @ORM\Entity()
 * @ORM\Table(name="tags",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="tags_name_unique",columns={"nameTag"})}
 * )
 */
class Tag {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="idTag")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="nameTag")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Image", inversedBy="tags", cascade={"persist"})
     * @ORM\JoinTable(name="tags_images",
     *   joinColumns={@ORM\JoinColumn(name="idTag", referencedColumnName="idTag")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="idImage", referencedColumnName="idImage")}
     * )
     */
    protected $images;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="idUser", referencedColumnName="idUser")
     * @var User
     */
    protected $user;

    /**
     * Number of images in the tag
     * Count(images)
     */
    protected $nbImages;

    public function __construct() {
        $this->images = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Add image
     *
     * @param Image $price
     *
     * @return Image
     */
    public function addImage(Image $image) {
        $this->images[] = $image;
        return $this;
    }
    /**
     * Remove image
     *
     * @param Image $image
     */
    public function removeImage(Image $image) {
        $this->images->removeElement($image);
    }
    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages() {
        return $this->images;
    }

    public function getUser() {
        return $this->user;
    }
    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    public function getNbImages() {
        return count($this->images);
    }
}