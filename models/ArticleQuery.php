<?php

namespace Models;

use Models\Base\ArticleQuery as BaseArticleQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'articles' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class ArticleQuery extends BaseArticleQuery
{
    public static function recent(){
        $recent = ArticleQuery::create()
            ->joinWithImage()
            ->orderByCreatedAt("desc")
            ->limit(5)
            ->find();
    
        return $recent;
    }
}
