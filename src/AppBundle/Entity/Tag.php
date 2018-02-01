<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Settings short summary.
 *
 * Settings description.
 *
 * @version 1.0
 * @author Maël Le Goff
 * @ORM\Entity()
 * @ORM\Table(name="settings",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="tags_name_unique",columns={"nameTag"})}
 * )
 */
class Settings {
    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('1')")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="title")
     */
    protected $title;

    /**
     * @ORM\Column(type="integer", name="limitGallery")
     */
    protected $limitGallery;

    /**
     * @ORM\ManyToMany(targetEntity="Image", inversedBy="tags", cascade={"persist"})
     * @ORM\JoinTable(name="tags_images",
     *   joinColumns={@ORM\JoinColumn(name="idTag", referencedColumnName="idTag")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="idImage", referencedColumnName="idImage")}
     * )
     */
    protected $images;

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
}