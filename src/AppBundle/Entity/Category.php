<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Category short summary.
 *
 * Category description.
 *
 * @version 1.0
 * @author Ma�l Le Goff
 * @ORM\Entity()
 * @ORM\Table(name="categories",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="categories_name_unique",columns={"nameCategory"})}
 * )
 */
class Category {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="idCategory")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="nameCategory")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="urlImageCategory")
     */
    protected $urlImage;

    /**
     * One Category has Many Categories.
     * http://docs.doctrine-project.org/en/latest/reference/association-mapping.html#one-to-many-self-referencing
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     * @var Category[]
     */
    protected $childrens;

    /**
     * Many Categories have One Category.
     * http://docs.doctrine-project.org/en/latest/reference/association-mapping.html#one-to-many-self-referencing
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="childrens")
     * @ORM\JoinColumn(name="idParentCategory", referencedColumnName="idCategory")
     */
    protected $parent;

    /**
     * @ORM\ManyToMany(targetEntity="Image", inversedBy="categories", cascade={"persist"})
     * @ORM\JoinTable(name="categories_images",
     *   joinColumns={@ORM\JoinColumn(name="idCategory", referencedColumnName="idCategory")},
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
     * Number of images in the category
     * Count(images)
     */
    protected $nbImages;

    public function __construct() {
        $this->childrens = new ArrayCollection();
        $this->images = new ArrayCollection();
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

    public function getUrlImage() {
        return $this->urlImage;
    }
    public function setUrlImage($urlImage) {
        $this->urlImage = $urlImage;
        return $this;
    }

    public function getParent() {
        return $this->parent;
    }
    public function setParent($parent) {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Add children
     *
     * @param Category $price
     *
     * @return Category
     */
    public function addChildren(Category $children)
    {
        $this->childrens[] = $children;
        return $this;
    }
    /**
     * Remove children
     *
     * @param Category $children
     */
    public function removeChildren(Category $children)
    {
        $this->childrens->removeElement($children);
    }
    /**
     * Get childrens
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildrens() {
        return $this->childrens;
    }

    /**
     * Add image
     *
     * @param Image $price
     *
     * @return Image
     */
    public function addImage(Image $image) {
        $this->images->add($image);
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