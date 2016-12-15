<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2016, iBenchu.org
 * @datetime 2016-12-15 18:38
 */
namespace Notadd\Foundation\Theme;

/**
 * Class Theme.
 */
class Theme
{
    /**
     * @var string|array
     */
    protected $author;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $installed;

    /**
     * @var string
     */
    protected $name;

    /**
     * Theme constructor.
     *
     * @param string $name
     * @param string|array $author
     * @param string $description
     */
    public function __construct($name = null, $author = null, $description = null)
    {
        $this->author = $author;
        $this->description = $description;
        $this->name = $name;
    }

    /**
     * Author of theme.
     *
     * @return string|array
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Description of theme.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Theme install status.
     *
     * @return bool
     */
    public function getInstalled()
    {
        return $this->installed;
    }

    /**
     * Name of theme.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set theme's author.
     *
     * @param string|array $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Set theme's description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Set theme's install status.
     *
     * @param bool $installed
     */
    public function setInstalled($installed)
    {
        $this->installed = $installed;
    }

    /**
     * Set theme's name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
