<?php

namespace Fields\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

/**
 * FieldsValues
 *
 * @ORM\Table(name="fields_values", uniqueConstraints={@ORM\UniqueConstraint(name="fieldID", columns={"fieldID", "alias"})})
 * @ORM\Entity
 */
class FieldsValues extends MCmsEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="fieldID", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $fieldID;
    
    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", precision=0, scale=0, nullable=false, unique=false)
     */
    protected $value;


    /**
     * Set fieldID
     *
     * @param integer $fieldID
     * @return FieldsValues
     */
    public function setFieldID($fieldID)
    {
        $this->fieldID = $fieldID;

        return $this;
    }

    /**
     * Get fieldID
     *
     * @return integer
     */
    public function getFieldID()
    {
        return $this->fieldID;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return FieldsValues
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return FieldsValues
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }
}
