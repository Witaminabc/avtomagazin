<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers;

use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;

use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\HeartBeat;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\Term;
use Itgalaxy\Wc\Exchange1c\Includes\Logger;

class Groups
{
    public static function process($reader, $processData)
    {
        if (!isset($_SESSION['IMPORT_1C']['numberOfCategories'])) {
            $_SESSION['IMPORT_1C']['numberOfCategories'] = 0;
        }

        if ($reader->name == 'Группы'
            && $reader->nodeType == \XMLReader::ELEMENT
            && str_replace(' ', '', $reader->readOuterXml()) == '<Группы/>'
        ) {
            return $processData;
        }

        if ($reader->name == 'Группы' && $reader->nodeType == \XMLReader::ELEMENT) {
            if ($processData['numberOfCategories'] >= $_SESSION['IMPORT_1C']['numberOfCategories']) {
                array_unshift($processData['categoryIdStack'], $processData['currentCategoryId']);
            }
        }

        if ($reader->name == 'Группы' && $reader->nodeType == \XMLReader::END_ELEMENT) {
            if ($processData['numberOfCategories'] >= $_SESSION['IMPORT_1C']['numberOfCategories']) {
                array_shift($processData['categoryIdStack']);
            }
        }

        if ($reader->name == 'Группа' && $reader->nodeType == \XMLReader::ELEMENT) {
            if (!HeartBeat::nextTerm()) {
                return false;
            }

            $element = $reader->readOuterXml();
            $element = simplexml_load_string(trim($element));

            if (!isset($element->{Bootstrap::XML_TAGS['id']})) {
                unset($element);

                return $processData;
            }

            $processData['numberOfCategories']++;

            if ($processData['numberOfCategories'] < $_SESSION['IMPORT_1C']['numberOfCategories']) {
                unset($element);

                return $processData;
            }

            if (is_array($processData['exclude1cCategories'])
                && in_array((string) $element->{Bootstrap::XML_TAGS['id']}, $processData['exclude1cCategories'])
            ) {
                $_SESSION['IMPORT_1C']['numberOfCategories'] = $processData['numberOfCategories'];

                unset($element);

                return $processData;
            }

            $categoryEntry = [];

            $category = Term::getTermIdByMeta((string) $element->{Bootstrap::XML_TAGS['id']});

            if (!$category) {
                $category = apply_filters('itglx_wc1c_find_product_cat_term_id', $category, $element, 'product_cat');

                if ($category) {
                    Term::update1cId($category, (string) $element->{Bootstrap::XML_TAGS['id']});
                }
            }

            if ($category) {
                $categoryEntry['term_id'] = $category;
            }

            $_SESSION['IMPORT_1C']['categoryIdStack'] = $processData['categoryIdStack'];

            $categoryEntry['parent'] = $processData['categoryIdStack'][0];
            $categoryEntry['name'] = trim(strip_tags((string) $element->{Bootstrap::XML_TAGS['name']}));

            $_SESSION['IMPORT_1C_PROCESS']['currentCategorys1c'][] =
                (string) $element->{Bootstrap::XML_TAGS['id']};

            if (isset($categoryEntry['term_id'])) {
                Term::updateProductCat($categoryEntry);
                Logger::logChanges('update', 'term', $categoryEntry['term_id']);
            } else {
                $categoryEntry['term_id'] = Term::insertProductCat($categoryEntry);
                Term::update1cId($categoryEntry['term_id'], (string) $element->{Bootstrap::XML_TAGS['id']});
                Logger::logChanges('insert', 'term', $categoryEntry['term_id']);
            }

            $processData['currentCategoryId'] = $categoryEntry['term_id'];
            $_SESSION['IMPORT_1C']['currentCategoryId'] = $processData['currentCategoryId'];
            $_SESSION['IMPORT_1C']['numberOfCategories'] = $processData['numberOfCategories'];

            unset($element, $categoryEntry);
        }

        return $processData;
    }
}
