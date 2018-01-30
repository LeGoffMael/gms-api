<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Image short summary.
 *
 * Image description.
 *
 * @version 1.0
 * @author Maël Le Goff
 * @ORM\Entity()
 * @ORM\Table(name="images",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="images_url_unique",columns={"urlImage"})}
 * )
 */
class Image {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="idImage")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="urlImage")
     */
    protected $url;

    /**
     * @ORM\Column(type="datetime", name="dateImage")
     */
    protected $date;

    /**
     * @ORM\Column(type="integer", name="scoreImage")
     */
    protected $score;

    /**
     * @ORM\Column(type="string", name="descriptionImage")
     */
    protected $description;

    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="images", cascade={"persist"})
     * @ORM\JoinTable(name="categories_images",
     *   joinColumns={@ORM\JoinColumn(name="idImage", referencedColumnName="idImage")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="idCategory", referencedColumnName="idCategory")},
     * )
     */
    protected $categories;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="images", cascade={"persist"})
     * @ORM\JoinTable(name="tags_images",
     *   joinColumns={@ORM\JoinColumn(name="idImage", referencedColumnName="idImage")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="idTag", referencedColumnName="idTag")},
     * )
     */
    protected $tags;

    public function __construct() {
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getDate() {
        return $this->date;
    }

    public function getScore() {
        return $this->score;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function setScore($score) {
        $this->score = $score;
        return $this;
    }

    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * Add category
     *
     * @param Category $category
     *
     * @return Category
     */
    public function addCategory(Category $category) {
        $this->categories[] = $category;
        return $this;
    }
    /**
     * Remove category
     *
     * @param Image $image
     */
    public function removeCategory(Category $category) {
        $this->categories->removeElement($category);
    }
    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * Add tag
     *
     * @param Tag $tag
     *
     * @return Tag
     */
    public function addTag(Tag $tag) {
        $this->tags[] = $tag;
        return $this;
    }
    /**
     * Remove tag
     *
     * @param Image $image
     */
    public function removeTag(Tag $tag) {
        $this->tags->removeElement($tag);
    }
    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags() {
        return $this->tags;
    }
}