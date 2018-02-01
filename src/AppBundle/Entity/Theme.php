<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Theme short summary.
 *
 * Theme description.
 *
 * @version 1.0
 * @author Maël Le Goff
 * @ORM\Entity()
 * @ORM\Table(name="themes",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="themes_name_unique",columns={"nameTheme"})}
 * )
 */
class Theme {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="idTheme")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="nameTheme")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="mainColor")
     */
    protected $mainColor;

    /**
     * @ORM\Column(type="string", name="mainDarkFontColor")
     */
    protected $mainDarkFontColor;

    /**
     * @ORM\Column(type="string", name="bodyColor")
     */
    protected $bodyColor;

    /**
     * @ORM\Column(type="string", name="bodyFontColor")
     */
    protected $bodyFontColor;

    /**
     * @ORM\Column(type="string", name="sideBarColor")
     */
    protected $sideBarColor;

    /**
     * @ORM\Column(type="string", name="sideBarFontColor")
     */
    protected $sideBarFontColor;

    /**
     * @ORM\Column(type="string", name="linkColor")
     */
    protected $linkColor;

    /**
     * @ORM\Column(type="string", name="linkHoverColor")
     */
    protected $linkHoverColor;

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

    public function getMainColor() {
        return $this->mainColor;
    }
    public function setMainColor($mainColor) {
        $this->mainColor = $mainColor;
        return $this;
    }

    public function getMainDarkFontColor() {
        return $this->mainDarkFontColor;
    }
    public function setMainDarkFontColor($mainDarkFontColor) {
        $this->mainDarkFontColor = $mainDarkFontColor;
        return $this;
    }

    public function getBodyColor() {
        return $this->bodyColor;
    }
    public function setBodyColor($bodyColor) {
        $this->bodyColor = $bodyColor;
        return $this;
    }

    public function getBodyFontColor() {
        return $this->bodyFontColor;
    }
    public function setBodyFontColor($bodyFontColor) {
        $this->bodyFontColor = $bodyFontColor;
        return $this;
    }

    public function getSideBarColor() {
        return $this->sideBarColor;
    }
    public function setSideBarColor($sideBarColor) {
        $this->sideBarColor = $sideBarColor;
        return $this;
    }

    public function getSideBarFontColor() {
        return $this->sideBarFontColor;
    }
    public function setSideBarFontColor($sideBarFontColor) {
        $this->sideBarFontColor = $sideBarFontColor;
        return $this;
    }

    public function getLinkColor() {
        return $this->linkColor;
    }
    public function setLinkColor($linkColor) {
        $this->linkColor = $linkColor;
        return $this;
    }

    public function getLinkHoverColor() {
        return $this->linkHoverColor;
    }
    public function setLinkHoverColor($linkHoverColor) {
        $this->linkHoverColor = $linkHoverColor;
        return $this;
    }
}