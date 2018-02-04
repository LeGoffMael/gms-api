<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Settings short summary.
 *
 * Settings description.
 *
 * @version 1.0
 * @author Maël Le Goff
 * @ORM\Entity()
 * @ORM\Table(name="settings")
 */
class Settings {
    /**
     * @ORM\Id
     * @ORM\Column(type="string", name="id", columnDefinition="ENUM('1')")
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
     * @ORM\ManyToOne(targetEntity="Theme")
     * @ORM\JoinColumn(name="idTheme", referencedColumnName="idTheme")
     * @var Theme
     */
    protected $theme;

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getLimitGallery() {
        return $this->limitGallery;
    }
    public function setLimitGallery($limit) {
        $this->limitGallery = $limit;
        return $this;
    }

    public function getTheme() {
        return $this->theme;
    }
    public function setTheme($theme) {
        $this->theme = $theme;
        return $this;
    }
}