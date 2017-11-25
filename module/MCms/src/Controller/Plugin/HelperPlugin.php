<?php

namespace MCms\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class HelperPlugin extends AbstractPlugin
{
    /**
     * Не имее своего View. Функция для вызова через Forward
     * Возвращяет массив форм в виде [alias => formName]
     *
     * @return array
     */
    public function getFormsList()
    {
        $om = $this->getController()->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $fields = $om->getRepository('Fields\Entity\Fields')->findBy(['alias' => 'form_name']);
        $fieldIDs = [];
        foreach ($fields as $field) {
            /* @var $field \Fields\Entity\Fields */
            $fieldIDs[$field->getFieldID()] = $field->getAlias();
        }

        $forms = [];
        if (count($fieldIDs) > 0) {
            $arrForms = $om->getRepository('Fields\Entity\FieldsValues')->findBy(['fieldID' => array_keys($fieldIDs)]);
            foreach ($arrForms as $fieldValue) {
                /* @var $fieldValue \Fields\Entity\FieldsValues */
                $forms[$fieldValue->getAlias()] = $fieldValue->getValue();
            }
        }

        return $forms;
    }

    /**
     * Возвращяет массив seo в виде [seo_* => seo_value]
     *
     * @param string|null $alias
     * @return array
     */
    public function getSeo($alias = null)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getController()->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $fields = $em->getRepository('Fields\Entity\Fields')->findBy(['alias' => ['seo_title', 'seo_keywords', 'seo_description']]);
        $fieldIDs = [];
        foreach ($fields as $field) {
            /* @var $field \Fields\Entity\Fields */
            $fieldIDs[$field->getFieldID()] = $field->getAlias();
        }


        $seo = $arrSeo = [];
        if (count($fieldIDs) > 0) {
            if ($alias) {
                $qb = $em->createQueryBuilder();
                $qb->select('f')
                    ->from('Fields\Entity\FieldsValues', 'f', 'f.fieldID')// here the magic
                    ->where('f.fieldID in (?1)')
                    ->andWhere('f.alias = ?2')
                    ->setParameters([
                        1 => array_keys($fieldIDs),
                        2 => $alias
                    ])
                    ->orderBy('f.fieldID', 'ASC');
                $arrSeo = $qb->getQuery()->getResult();
            }

            foreach ($fieldIDs as $fieldId => $fieldName) {
                $seo[$fieldName] = isset($arrSeo[$fieldId]) && $arrSeo[$fieldId] instanceof \Fields\Entity\FieldsValues ? $arrSeo[$fieldId]->getValue() : null;
            }
        }

        return $seo;
    }

    /**
     * Обновляет SEO информацию в базе данных
     * если ($alias != null && $newAlias == null), то удалит записи по alias
     * Возвращяет true|false
     *
     * @param string $alias
     * @param string $newAlias
     * @param array $options
     * @return bool
     */
    public function setSeo($alias = null, $newAlias = null, $options = [])
    {
        $params = [
            'seo_title'       => isset($options[0]) ? $options[0]
                : (isset($options['title']) ? $options['title']
                    : (isset($options['seo_title']) ? $options['seo_title'] : null)),
            'seo_keywords'    => isset($options[1]) ? $options[1]
                : (isset($options['keywords']) ? $options['keywords']
                    : (isset($options['seo_keywords']) ? $options['seo_keywords'] : null)),
            'seo_description' => isset($options[2]) ? $options[2]
                : (isset($options['description']) ? $options['description']
                    : (isset($options['seo_description']) ? $options['seo_description'] : null)),
        ];
        $result = false;

        if ($alias) {
            $om = $this->getController()->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $fields = $om->getRepository('Fields\Entity\Fields')->findBy(['alias' => ['seo_title', 'seo_keywords', 'seo_description']]);
            $fieldIDs = [];
            foreach ($fields as $field) {
                /* @var $field \Fields\Entity\Fields */
                $fieldIDs[$field->getFieldID()] = $field->getAlias();
            }

            if (count($fieldIDs) > 0) {
                $arrSeo = $om->getRepository('Fields\Entity\FieldsValues')->findBy(['fieldID' => array_keys($fieldIDs), 'alias' => $alias]);
                if ($arrSeo) {
                    if ($newAlias) {
                        foreach ($arrSeo as $fieldValue) {
                            /* @var $fieldValue \Fields\Entity\FieldsValues */
                            if ($alias != $newAlias)
                                $fieldValue->setAlias($newAlias);
                            $fieldValue->setValue($params[$fieldIDs[$fieldValue->getFieldID()]]);
                            $om->persist($fieldValue);
                        }
                        $om->flush();
                        $result = true;
                    } else {
                        foreach ($arrSeo as $fieldValue) {
                            /* @var $fieldValue \Fields\Entity\FieldsValues */
                            $om->remove($fieldValue);
                        }
                        $om->flush();
                        $result = true;
                    }
                } else {
                    foreach ($fieldIDs as $key => $val) {
                        $fieldValue = new \Fields\Entity\FieldsValues();
                        $fieldValue->setFieldID($key);
                        $fieldValue->setAlias($alias);
                        $fieldValue->setValue($params[$fieldIDs[$key]]);
                        $om->persist($fieldValue);
                    }
                    $om->flush();
                    $result = true;
                }
            }
        }

        return $result;
    }
}